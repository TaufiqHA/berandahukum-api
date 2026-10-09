<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SubCategoryController extends AdminApiController
{
    public function index()
    {
        $rows = SubCategory::orderBy('urutan')->orderBy('sub_category_id')->get()->map(function ($s) {
            $cat = Category::find($s->category_id);

            return [
                'id' => (int) $s->sub_category_id,
                'category_id' => (int) $s->category_id,
                'category_name' => $cat->category_name ?? '-',
                'name' => $s->sub_category_name,
                'uri' => $s->sub_category_uri,
                'show' => $s->sub_category_show === 'yes',
                'urutan' => (int) $s->urutan,
            ];
        })->all();

        return $this->ok(['data' => $rows]);
    }

    public function store(Request $request)
    {
        $data = $this->payload($request, true);
        $this->ensureUnique($data);

        try {
            SubCategory::create($data);
        } catch (QueryException $e) {
            $this->failWhenDuplicate($e, $data['sub_category_name']);
        }

        return $this->message('Sub kategori berhasil disimpan.', 201);
    }

    public function update(Request $request, $id)
    {
        $row = SubCategory::findOrFail($id);
        $data = $this->payload($request);
        $this->ensureUnique($data, $row->sub_category_id);

        try {
            $row->update($data);
        } catch (QueryException $e) {
            $this->failWhenDuplicate($e, $data['sub_category_name']);
        }

        return $this->message('Sub kategori berhasil diubah.');
    }

    public function destroy($id)
    {
        SubCategory::findOrFail($id)->delete();

        return $this->message('Sub kategori berhasil dihapus.');
    }

    /** Simpan urutan sub-kategori hasil geser. Body: { category_id, position: [id, id, ...] } */
    public function urutan(Request $request)
    {
        $categoryId = (int) $request->input('category_id');
        $position = array_values(array_map('intval', (array) $request->input('position', [])));

        // Penomoran per kategori, sama seperti website
        // (Admin\SubCategoryController::urutanSave). Kategori lain tidak ikut
        // dinomori ulang agar setelan tetap konsisten dengan website.
        foreach ($position as $i => $id) {
            $query = SubCategory::where('sub_category_id', $id);
            if ($categoryId > 0) {
                $query->where('category_id', $categoryId);
            }
            $query->update(['urutan' => $i + 1]);
        }

        return $this->message('Urutan sub kategori disimpan.');
    }

    /**
     * Artikel terbit pada sebuah sub-kategori, untuk pengaturan urutan.
     * GET sub-categories/{id}/articles
     */
    public function articles($id)
    {
        $sub = SubCategory::findOrFail($id);

        $rows = Article::select(
            'tbl_article.article_id as id',
            'tbl_article.article_title as title',
            'tbl_article.article_date as date',
            'ac.urutan as urutan'
        )
            ->join('tbl_article_category as ac', 'ac.article_id', '=', 'tbl_article.article_id')
            ->where('ac.sub_category_id', $sub->sub_category_id)
            ->where('tbl_article.article_status', 1)
            ->orderBy('ac.urutan')
            ->orderByDesc('tbl_article.article_date')
            ->get()
            ->map(fn ($a) => [
                'id' => (int) $a->id,
                'title' => $a->title,
                'date' => $a->date,
                'urutan' => (int) $a->urutan,
            ])->all();

        return $this->ok(['data' => $rows]);
    }

    /** Simpan urutan artikel pada sebuah sub-kategori. Body: { position: [article_id, ...] } */
    public function articlesUrutan(Request $request, $id)
    {
        $sub = SubCategory::findOrFail($id);
        $position = array_values(array_map('intval', (array) $request->input('position', [])));

        foreach ($position as $i => $articleId) {
            ArticleCategory::where('sub_category_id', $sub->sub_category_id)
                ->where('article_id', $articleId)
                ->update(['urutan' => $i + 1]);
        }

        // Sisa artikel pada sub-kategori ini diletakkan setelahnya agar urutan
        // lama (0) tidak menyerobot posisi teratas.
        $offset = count($position);
        ArticleCategory::where('sub_category_id', $sub->sub_category_id)
            ->whereNotIn('article_id', $position)
            ->orderBy('urutan')->orderByDesc('created_date')
            ->pluck('article_id')
            ->each(fn ($articleId, $k) => ArticleCategory::where('sub_category_id', $sub->sub_category_id)
                ->where('article_id', $articleId)
                ->update(['urutan' => $offset + $k + 1]));

        return $this->message('Urutan artikel disimpan.');
    }

    private function payload(Request $request, bool $isNew = false): array
    {
        $request->validate([
            'category_id' => 'required|integer',
            'name' => 'required|string|max:70',
        ]);
        $name = $request->input('name');

        $data = [
            'category_id' => (int) $request->input('category_id'),
            'sub_category_uri' => urlencode(str_replace(' ', '-', strtolower($name))),
            'sub_category_name' => $name,
            'sub_category_show' => $request->boolean('show', true) ? 'yes' : 'no',
        ];

        // Saat menambah: urutan kosong/0 berarti otomatis di akhir kategori.
        // Saat mengubah: hanya perbarui bila dikirim, supaya menyunting nama
        // tidak mengacak urutan hasil geser.
        if ($isNew) {
            $data['urutan'] = (int) ($request->input('urutan') ?: $this->nextUrutan());
            $data['create_date'] = date('Y-m-d');
        } elseif ($request->has('urutan')) {
            $data['urutan'] = (int) $request->input('urutan');
        }

        return $data;
    }

    private function nextUrutan(): int
    {
        return ((int) SubCategory::max('urutan')) + 1;
    }

    /**
     * Pastikan nama sub-kategori tidak menghasilkan URI yang sudah dipakai
     * (kolom sub_category_uri bersifat unik). Bila bentrok, kembalikan pesan
     * yang menyebutkan kategori pemiliknya alih-alih error 500.
     */
    private function ensureUnique(array $data, ?int $ignoreId = null): void
    {
        $query = SubCategory::where('sub_category_uri', $data['sub_category_uri']);

        if ($ignoreId) {
            $query->where('sub_category_id', '!=', $ignoreId);
        }

        $existing = $query->first();

        if (! $existing) {
            return;
        }

        $category = Category::find($existing->category_id);
        $categoryName = $category->category_name ?? 'kategori lain';

        throw ValidationException::withMessages([
            'name' => 'Sub kategori "'.$data['sub_category_name'].'" sudah terdaftar'
                .' pada kategori "'.$categoryName.'". Silakan gunakan nama sub kategori yang lain.',
        ]);
    }

    /**
     * Penjaga terakhir bila ada dua permintaan bersamaan: ubah error database
     * "Duplicate entry" menjadi pesan validasi yang informatif.
     */
    private function failWhenDuplicate(QueryException $e, string $name): never
    {
        $isDuplicate = (int) ($e->errorInfo[1] ?? 0) === 1062
            || str_contains($e->getMessage(), 'Duplicate entry');

        if (! $isDuplicate) {
            throw $e;
        }

        throw ValidationException::withMessages([
            'name' => 'Sub kategori "'.$name.'" sudah terdaftar. Silakan gunakan nama sub kategori yang lain.',
        ]);
    }
}
