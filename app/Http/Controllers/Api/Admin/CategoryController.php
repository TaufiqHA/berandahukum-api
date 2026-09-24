<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class CategoryController extends AdminApiController
{
    public function index()
    {
        $rows = Category::orderBy('urutan')->orderBy('category_id')->get()->map(fn ($c) => [
            'id' => (int) $c->category_id,
            'name' => $c->category_name,
            'uri' => $c->category_uri,
            'show' => $c->category_show === 'yes',
            'urutan' => (int) $c->urutan,
            'sub_count' => SubCategory::where('category_id', $c->category_id)->count(),
        ])->all();

        return $this->ok(['data' => $rows]);
    }

    public function store(Request $request)
    {
        $data = $this->payload($request);
        Category::create($data);

        return $this->message('Kategori berhasil disimpan.', 201);
    }

    public function update(Request $request, $id)
    {
        Category::findOrFail($id)->update($this->payload($request));

        return $this->message('Kategori berhasil diubah.');
    }

    public function destroy($id)
    {
        Category::findOrFail($id)->delete();

        return $this->message('Kategori berhasil dihapus.');
    }

    private function payload(Request $request): array
    {
        $request->validate(['name' => 'required|string|max:70']);
        $name = $request->input('name');

        return [
            'category_uri' => urlencode(str_replace(' ', '-', strtolower($name))),
            'category_name' => $name,
            'category_show' => $request->boolean('show') ? 'yes' : 'no',
            'urutan' => (int) $request->input('urutan', 0),
        ];
    }
}
