<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.category.index', [
            'title' => 'Daftar Kategori',
            'category' => Category::orderBy('urutan')->orderBy('category_id')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.category.form', ['title' => 'Tambah Kategori', 'row' => null]);
    }

    public function edit($id)
    {
        return view('admin.category.form', ['title' => 'Ubah Kategori', 'row' => Category::findOrFail($id)]);
    }

    public function store(Request $request)
    {
        Category::create($this->payload($request));

        return redirect(site_admin('category'))->with('msg_flash', success_message('Data berhasil disimpan.'));
    }

    public function update(Request $request, $id)
    {
        Category::findOrFail($id)->update($this->payload($request));

        return redirect(site_admin('category'))->with('msg_flash', success_message('Data berhasil diubah.'));
    }

    public function destroy($id)
    {
        Category::findOrFail($id)->delete();

        return redirect(site_admin('category'))->with('msg_flash', success_message('Kategori berhasil dihapus.'));
    }

    private function payload(Request $request): array
    {
        $request->validate(['categoryName' => 'required|max:70']);
        $name = $request->input('categoryName');

        return [
            'category_uri' => urlencode(str_replace(' ', '-', strtolower($name))),
            'category_name' => $name,
            'category_show' => $request->input('categoryShow') === 'yes' ? 'yes' : 'no',
        ];
    }
}
