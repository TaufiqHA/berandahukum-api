<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends AdminApiController
{
    public function index()
    {
        $rows = SubCategory::orderBy('sub_category_id')->get()->map(function ($s) {
            $cat = Category::find($s->category_id);

            return [
                'id' => (int) $s->sub_category_id,
                'category_id' => (int) $s->category_id,
                'category_name' => $cat->category_name ?? '-',
                'name' => $s->sub_category_name,
                'uri' => $s->sub_category_uri,
                'show' => $s->sub_category_show === 'yes',
            ];
        })->all();

        return $this->ok(['data' => $rows]);
    }

    public function store(Request $request)
    {
        SubCategory::create($this->payload($request));

        return $this->message('Sub kategori berhasil disimpan.', 201);
    }

    public function update(Request $request, $id)
    {
        SubCategory::findOrFail($id)->update($this->payload($request));

        return $this->message('Sub kategori berhasil diubah.');
    }

    public function destroy($id)
    {
        SubCategory::findOrFail($id)->delete();

        return $this->message('Sub kategori berhasil dihapus.');
    }

    private function payload(Request $request): array
    {
        $request->validate([
            'category_id' => 'required|integer',
            'name' => 'required|string|max:70',
        ]);
        $name = $request->input('name');

        return [
            'category_id' => (int) $request->input('category_id'),
            'sub_category_uri' => urlencode(str_replace(' ', '-', strtolower($name))),
            'sub_category_name' => $name,
            'sub_category_show' => $request->boolean('show', true) ? 'yes' : 'no',
            'create_date' => date('Y-m-d'),
        ];
    }
}
