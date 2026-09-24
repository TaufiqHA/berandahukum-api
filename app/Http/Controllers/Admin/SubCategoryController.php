<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index()
    {
        return view('admin.sub_category.index', [
            'title' => 'Daftar Sub Kategori',
            'subCategory' => SubCategory::orderBy('sub_category_id')->get(),
            'categories' => Category::orderBy('urutan')->get()->keyBy('category_id'),
        ]);
    }

    public function create()
    {
        return view('admin.sub_category.form', [
            'title' => 'Tambah Sub Kategori',
            'row' => null,
            'categories' => Category::orderBy('urutan')->get(),
        ]);
    }

    public function edit($id)
    {
        return view('admin.sub_category.form', [
            'title' => 'Ubah Sub Kategori',
            'row' => SubCategory::findOrFail($id),
            'categories' => Category::orderBy('urutan')->get(),
        ]);
    }

    public function store(Request $request)
    {
        SubCategory::create($this->payload($request));

        return redirect(site_admin('sub-category'))->with('msg_flash', success_message('Data berhasil disimpan.'));
    }

    public function update(Request $request, $id)
    {
        SubCategory::findOrFail($id)->update($this->payload($request));

        return redirect(site_admin('sub-category'))->with('msg_flash', success_message('Data berhasil diubah.'));
    }

    public function destroy($id)
    {
        SubCategory::findOrFail($id)->delete();

        return redirect(site_admin('sub-category'))->with('msg_flash', success_message('Kategori berhasil dihapus.'));
    }

    private function payload(Request $request): array
    {
        $request->validate([
            'categoryName' => 'required',
            'subCategoryName' => 'required|max:70',
        ]);
        $name = $request->input('subCategoryName');
        $createDate = $request->input('subCategoryCreateTime') ?: $request->input('createDate');

        return [
            'category_id' => (int) $request->input('categoryName'),
            'sub_category_uri' => urlencode(str_replace(' ', '-', strtolower($name))),
            'sub_category_name' => $name,
            'sub_category_show' => $request->input('subcategoryShow') === 'yes' ? 'yes' : 'no',
            'create_date' => $createDate ? date('Y-m-d', strtotime($createDate)) : null,
        ];
    }
}
