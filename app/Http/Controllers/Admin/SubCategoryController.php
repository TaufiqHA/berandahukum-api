<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SubCategoryController extends Controller
{
    public function index()
    {
        return view('admin.sub_category.index', [
            'title' => 'Daftar Sub Kategori',
            'subCategory' => SubCategory::orderBy('urutan')->orderBy('sub_category_id')->get(),
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
        $data = $this->payload($request);
        $this->ensureUnique($data);

        // Sub-kategori baru diletakkan di akhir daftar.
        $data['urutan'] = ((int) SubCategory::max('urutan')) + 1;

        try {
            SubCategory::create($data);
        } catch (QueryException $e) {
            $this->failWhenDuplicate($e, $data['sub_category_name']);
        }

        return redirect(site_admin('sub-category'))->with('msg_flash', success_message('Data berhasil disimpan.'));
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

        return redirect(site_admin('sub-category'))->with('msg_flash', success_message('Data berhasil diubah.'));
    }

    public function destroy($id)
    {
        SubCategory::findOrFail($id)->delete();

        return redirect(site_admin('sub-category'))->with('msg_flash', success_message('Kategori berhasil dihapus.'));
    }

    /**
     * Halaman pengaturan urutan sub-kategori, dikelompokkan per kategori
     * (padanan layar "Urutan Sub Kategori" pada panel mobile).
     */
    public function urutan()
    {
        return view('admin.sub_category.urutan', [
            'title' => 'Seting Urutan Sub Kategori',
            'categories' => Category::orderBy('urutan')->orderBy('category_id')->get(),
            'subCategories' => SubCategory::orderBy('urutan')->orderBy('sub_category_id')
                ->get()->groupBy('category_id'),
            'show_ui' => true,
        ]);
    }

    /**
     * Simpan urutan sub-kategori dalam satu kategori.
     * Body: { category_id, position: [id, id, ...] }
     */
    public function urutanSave(Request $request)
    {
        $categoryId = (int) $request->input('category_id');
        $position = array_values(array_map('intval', (array) $request->input('position', [])));

        foreach ($position as $i => $id) {
            SubCategory::where('category_id', $categoryId)
                ->where('sub_category_id', $id)
                ->update(['urutan' => $i + 1]);
        }

        return redirect(site_admin('sub-category/urutan'))
            ->with('msg_flash', success_message('Urutan sub kategori berhasil disimpan.'));
    }

    /** Artikel terbit pada sebuah sub-kategori, untuk pengaturan urutan. */
    public function articles($id)
    {
        $sub = SubCategory::findOrFail($id);

        $articles = Article::select(
            'tbl_article.article_id',
            'tbl_article.article_title',
            'tbl_article.article_date',
            'ac.urutan as ac_urutan',
        )
            ->join('tbl_article_category as ac', 'ac.article_id', '=', 'tbl_article.article_id')
            ->where('ac.sub_category_id', $sub->sub_category_id)
            ->where('tbl_article.article_status', 1)
            ->orderBy('ac.urutan')
            ->orderByDesc('tbl_article.article_date')
            ->get();

        return view('admin.sub_category.articles', [
            'title' => 'Seting Urutan Artikel',
            'subCategory' => $sub,
            'articles' => $articles,
            'show_ui' => true,
        ]);
    }

    /**
     * Simpan urutan artikel pada sebuah sub-kategori.
     * Body: { position: [article_id, ...] }
     */
    public function articlesSave(Request $request, $id)
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

        return redirect(site_admin('sub-category/articles/'.$id))
            ->with('msg_flash', success_message('Urutan artikel berhasil disimpan.'));
    }

    /**
     * Pastikan nama sub-kategori tidak menghasilkan URI yang sudah dipakai
     * (kolom sub_category_uri bersifat unik). Bila bentrok, tampilkan pesan
     * yang menyebutkan kategori pemiliknya, bukan halaman error 500.
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
            'subCategoryName' => 'Sub kategori "'.$data['sub_category_name'].'" sudah terdaftar'
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
            'subCategoryName' => 'Sub kategori "'.$name.'" sudah terdaftar. Silakan gunakan nama sub kategori yang lain.',
        ]);
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
