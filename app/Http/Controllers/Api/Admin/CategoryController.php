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
        $data = $this->payload($request, true);
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

    /** Simpan urutan hasil geser. Body: { position: [id, id, ...] } */
    public function urutan(Request $request)
    {
        $position = array_values(array_map('intval', (array) $request->input('position', [])));

        foreach ($position as $i => $id) {
            Category::where('category_id', $id)->update(['urutan' => $i + 1]);
        }

        // Kategori yang tidak disertakan diletakkan setelahnya, agar urutan
        // lama (0) tidak menyerobot posisi teratas.
        $offset = count($position);
        Category::whereNotIn('category_id', $position)
            ->orderBy('urutan')->orderBy('category_id')
            ->pluck('category_id')
            ->each(fn ($id, $k) => Category::where('category_id', $id)->update(['urutan' => $offset + $k + 1]));

        return $this->message('Urutan kategori disimpan.');
    }

    private function payload(Request $request, bool $isNew = false): array
    {
        $request->validate(['name' => 'required|string|max:70']);
        $name = $request->input('name');

        $data = [
            'category_uri' => urlencode(str_replace(' ', '-', strtolower($name))),
            'category_name' => $name,
            'category_show' => $request->boolean('show') ? 'yes' : 'no',
        ];

        // Saat menambah: urutan kosong/0 berarti otomatis diletakkan di akhir.
        // Saat mengubah: hanya perbarui urutan bila memang dikirim, supaya
        // menyunting nama tidak mengacak urutan hasil geser.
        if ($isNew) {
            $data['urutan'] = (int) ($request->input('urutan') ?: $this->nextUrutan());
        } elseif ($request->has('urutan')) {
            $data['urutan'] = (int) $request->input('urutan');
        }

        return $data;
    }

    private function nextUrutan(): int
    {
        return ((int) Category::max('urutan')) + 1;
    }
}
