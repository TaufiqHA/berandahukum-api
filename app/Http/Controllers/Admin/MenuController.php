<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Label;
use App\Models\Menu;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        return view('admin.menu.index', ['title' => 'Seting Menu', 'datamenu' => Menu::orderBy('urutan')->get()]);
    }

    public function create()
    {
        return view('admin.menu.form', [
            'title' => 'Tambah Menu',
            'row' => null,
            'categories' => Category::orderBy('urutan')->get(),
        ]);
    }

    public function edit($id)
    {
        return view('admin.menu.form', [
            'title' => 'Update Menu',
            'row' => Menu::findOrFail($id),
            'categories' => Category::orderBy('urutan')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Menu::create($this->payload($request));

        return redirect(site_admin('menu'))->with('msg_flash', success_message('Data menu berhasil disimpan.'));
    }

    public function update(Request $request, $id)
    {
        Menu::findOrFail($id)->update($this->payload($request));

        return redirect(site_admin('menu'))->with('msg_flash', success_message('Data menu berhasil disimpan.'));
    }

    public function destroy($id)
    {
        Menu::findOrFail($id)->delete();

        return redirect(site_admin('menu'))->with('msg_flash', success_message('Menu berhasil dihapus.'));
    }

    /** AJAX: daftar sumber menu per tipe. */
    public function getdatamenu(Request $request)
    {
        $tipe = $request->input('tipe');
        $menu = match ($tipe) {
            'kategori' => Category::orderBy('urutan')->get(['category_id as id_menu', 'category_name as judul_menu']),
            'subkategori' => SubCategory::get(['sub_category_id as id_menu', 'sub_category_name as judul_menu']),
            'label' => Label::orderBy('label_id')->get(['label_id as id_menu', 'label_name as judul_menu']),
            default => [],
        };

        return response()->json($menu);
    }

    /** AJAX: daftar artikel per kategori/sub kategori. */
    public function getartikel(Request $request)
    {
        $idKat = $request->input('id_kategori');
        $idSub = $request->input('sub_kategori');

        $menu = Article::select('tbl_article.article_id as id_menu', 'tbl_article.article_title as judul_menu')
            ->join('tbl_article_category as ac', 'ac.article_id', '=', 'tbl_article.article_id')
            ->where('tbl_article.article_status', '1')
            ->when($idKat, fn ($q) => $q->where('ac.category_id', $idKat))
            ->when($idSub, fn ($q) => $q->where('ac.sub_category_id', $idSub))
            ->orderBy('tbl_article.article_id')
            ->get();

        return response()->json($menu);
    }

    private function payload(Request $request): array
    {
        $request->validate(['tipe' => 'required', 'id_menu' => 'required', 'urutan' => 'required']);

        return [
            'tipe' => $request->input('tipe'),
            'id_menu' => (int) $request->input('id_menu'),
            'uri_menu' => $this->resolveUri($request->input('tipe'), (int) $request->input('id_menu')),
            'urutan' => (int) $request->input('urutan'),
        ];
    }

    private function resolveUri(string $tipe, int $id): string
    {
        return match ($tipe) {
            'artikel' => Article::find($id)->article_uri ?? '',
            'label' => Label::find($id)->label_uri ?? '',
            'kategori' => Category::find($id)->category_uri ?? '',
            'subkategori' => SubCategory::find($id)->sub_category_uri ?? '',
            default => '',
        };
    }
}
