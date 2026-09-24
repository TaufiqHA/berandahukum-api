<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Pilihan;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class PilihanController extends Controller
{
    public function index()
    {
        $pilihan = Pilihan::orderBy('urutan')->get()->map(function ($p) {
            $p = $p->toArray();
            $p['article_title'] = Article::find($p['article_id'])->article_title ?? '';

            return $p;
        });

        return view('admin.pilihan.index', ['title' => 'Seting Artikel Pilihan', 'datapilihan' => $pilihan]);
    }

    public function create()
    {
        return view('admin.pilihan.form', ['title' => 'Tambah Artikel Pilihan', 'row' => null, 'categories' => Category::orderBy('urutan')->get()]);
    }

    public function edit($id)
    {
        return view('admin.pilihan.form', ['title' => 'Update Artikel Pilihan', 'row' => Pilihan::findOrFail($id), 'categories' => Category::orderBy('urutan')->get()]);
    }

    public function store(Request $request)
    {
        Pilihan::create($this->payload($request));

        return redirect(site_admin('pilihan'))->with('msg_flash', success_message('Data berhasil disimpan.'));
    }

    public function update(Request $request, $id)
    {
        Pilihan::findOrFail($id)->update($this->payload($request));

        return redirect(site_admin('pilihan'))->with('msg_flash', success_message('Data berhasil disimpan.'));
    }

    public function destroy($id)
    {
        Pilihan::findOrFail($id)->delete();

        return redirect(site_admin('pilihan'))->with('msg_flash', success_message('Artikel Pilihan berhasil dihapus.'));
    }

    /** AJAX: daftar artikel per kategori/sub kategori. */
    public function getartikel(Request $request)
    {
        $menu = Article::select('tbl_article.article_id as id_menu', 'tbl_article.article_title as judul_menu')
            ->join('tbl_article_category as ac', 'ac.article_id', '=', 'tbl_article.article_id')
            ->where('tbl_article.article_status', '1')
            ->when($request->input('id_kategori'), fn ($q, $v) => $q->where('ac.category_id', $v))
            ->when($request->input('sub_kategori'), fn ($q, $v) => $q->where('ac.sub_category_id', $v))
            ->orderBy('tbl_article.article_id')->get();

        return response()->json($menu);
    }

    public function getsub(Request $request)
    {
        return response()->json(SubCategory::where('category_id', $request->input('id_cat'))->get());
    }

    private function payload(Request $request): array
    {
        $request->validate(['posisi' => 'required', 'id_menu' => 'required', 'urutan' => 'required']);

        return [
            'posisi' => $request->input('posisi'),
            'article_id' => (int) $request->input('id_menu'),
            'urutan' => (int) $request->input('urutan'),
        ];
    }
}
