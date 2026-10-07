<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Label;
use App\Models\Referensi;
use App\Models\SubCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ArticleController extends Controller
{
    /** Panjang maksimum kolom article_title/article_uri di database. */
    private const TITLE_MAX = 100;

    public function index(Request $request)
    {
        $query = Article::orderByDesc('article_id');

        if ($request->filled('q')) {
            $query->where('article_title', 'like', '%'.$request->input('q').'%');
        }

        // Nilai "none" = artikel tanpa kategori / sub kategori / label.
        if ($request->input('category') === 'none') {
            $query->whereNotIn('article_id', ArticleCategory::select('article_id'));
        } elseif ($request->filled('category')) {
            $query->whereIn('article_id', ArticleCategory::select('article_id')
                ->where('category_id', (int) $request->input('category')));
        }

        if ($request->input('sub_category') === 'none') {
            $query->whereNotIn('article_id', ArticleCategory::select('article_id')
                ->where('sub_category_id', '>', 0));
        } elseif ($request->filled('sub_category')) {
            $query->whereIn('article_id', ArticleCategory::select('article_id')
                ->where('sub_category_id', (int) $request->input('sub_category')));
        }

        if ($request->input('label') === 'none') {
            $query->where('label_id', 0);
        } elseif ($request->filled('label')) {
            $query->where('label_id', (int) $request->input('label'));
        }

        if ($request->filled('author')) {
            $query->where('article_author', $request->input('author'));
        }

        if ($request->filled('status')) {
            $query->where('article_status', (int) $request->input('status'));
        }

        $articles = $query->paginate(20)->withQueryString();

        // Peta kategori & sub kategori untuk artikel pada halaman ini.
        $pairs = ArticleCategory::whereIn('article_id', $articles->pluck('article_id'))
            ->get()->keyBy('article_id');
        $categoryNames = Category::pluck('category_name', 'category_id');
        $subCategoryNames = SubCategory::pluck('sub_category_name', 'sub_category_id');

        $articleMeta = [];
        foreach ($articles as $a) {
            $pair = $pairs->get($a->article_id);
            $articleMeta[$a->article_id] = [
                'category' => ($pair && $pair->category_id) ? ($categoryNames[$pair->category_id] ?? '-') : '-',
                'sub_category' => ($pair && $pair->sub_category_id) ? ($subCategoryNames[$pair->sub_category_id] ?? '-') : '-',
            ];
        }

        return view('admin.article.index', [
            'title' => 'Daftar Artikel',
            'articles' => $articles,
            'articleMeta' => $articleMeta,
            'labels' => Label::pluck('label_name', 'label_id'),
            'categories' => Category::orderBy('urutan')->orderBy('category_id')->get(),
            'subCategories' => SubCategory::orderBy('urutan')->orderBy('sub_category_id')->get(),
            'authors' => Article::query()
                ->whereRaw("TRIM(COALESCE(article_author, '')) <> ''")
                ->distinct()->orderBy('article_author')->pluck('article_author'),
        ]);
    }

    public function create()
    {
        return view('admin.article.form', [
            'title' => 'Tambah Artikel',
            'row' => null,
            'labels' => Label::orderBy('label_id')->get(),
            'categories' => Category::orderBy('urutan')->get(),
            'subCategories' => SubCategory::orderBy('urutan')->orderBy('sub_category_id')->get(),
            'selectedCategory' => null,
            'selectedSub' => null,
        ]);
    }

    public function edit($id)
    {
        $row = Article::findOrFail($id);
        $junction = ArticleCategory::where('article_id', $row->article_id)->first();

        return view('admin.article.form', [
            'title' => 'Ubah Artikel',
            'row' => $row,
            'labels' => Label::orderBy('label_id')->get(),
            'categories' => Category::orderBy('urutan')->get(),
            'subCategories' => SubCategory::orderBy('urutan')->orderBy('sub_category_id')->get(),
            'selectedCategory' => $junction->category_id ?? null,
            'selectedSub' => $junction->sub_category_id ?? null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->payload($request);

        try {
            $article = Article::create($data);
        } catch (QueryException $e) {
            $this->failWhenDataTooLong($e);
        }

        $this->syncCategory($article, $request);

        return redirect(site_admin('article'))->with('msg_flash', success_message('Data artikel berhasil disimpan.'));
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $data = $this->payload($request, $article);

        try {
            $article->update($data);
        } catch (QueryException $e) {
            $this->failWhenDataTooLong($e);
        }

        $this->syncCategory($article, $request);

        return redirect(site_admin('article'))->with('msg_flash', success_message('Data artikel berhasil disimpan.'));
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        ArticleCategory::where('article_id', $article->article_id)->delete();
        $article->delete();

        return redirect(site_admin('article'))->with('msg_flash', success_message('Artikel berhasil dihapus.'));
    }

    public function publish($id)
    {
        Article::findOrFail($id)->update(['article_status' => 1]);

        return redirect(site_admin('article'))->with('msg_flash', success_message('Artikel berhasil dipublish.'));
    }

    public function draft($id)
    {
        Article::findOrFail($id)->update(['article_status' => 2]);

        return redirect(site_admin('article'))->with('msg_flash', success_message('Artikel diubah ke draft.'));
    }

    public function preview($uri)
    {
        return redirect(site_url('a/'.$uri));
    }

    public function comment($id)
    {
        return view('admin.article.comment', [
            'title' => 'Komentar Artikel',
            'article' => Article::findOrFail($id),
            'comments' => Comment::where('article_id', $id)->orderByDesc('comment_date')->get(),
        ]);
    }

    public function publishComment($id)
    {
        Comment::findOrFail($id)->update(['comment_status' => '1']);

        return back()->with('msg_flash', success_message('Komentar ditampilkan.'));
    }

    public function unpublishComment($id)
    {
        Comment::findOrFail($id)->update(['comment_status' => '0']);

        return back()->with('msg_flash', success_message('Komentar disembunyikan.'));
    }

    public function deleteComment($id)
    {
        Comment::findOrFail($id)->delete();

        return back()->with('msg_flash', success_message('Komentar dihapus.'));
    }

    public function referensi($id)
    {
        $article = Article::findOrFail($id);

        $list = Referensi::where('article_ref', $article->article_id)->orderBy('urutan')->get()->map(function ($r) {
            $r = $r->toArray();
            $art = Article::find($r['article_id']);
            $r['article_title'] = $art->article_title ?? '';
            $r['article_uri'] = $art->article_uri ?? '';

            return $r;
        });

        return view('admin.article.referensi', [
            'title' => 'Referensi Bacaan',
            'article' => $article,
            'datareferensi' => $list,
        ]);
    }

    public function referensiAdd(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $request->validate(['sumber' => 'required', 'urutan' => 'required']);

        $data = [
            'sumber' => $request->input('sumber'),
            'article_ref' => $article->article_id,
            'urutan' => (int) $request->input('urutan'),
        ];

        if ($request->input('sumber') === 'int') {
            $data['article_id'] = (int) $request->input('id_menu');
        } else {
            $data['judul'] = $request->input('judul');
            $data['link'] = $request->input('link');
        }

        Referensi::create($data);

        return redirect(site_admin('article/referensi/'.$article->article_id))->with('msg_flash', success_message('Data berhasil disimpan.'));
    }

    public function referensiDelete($id)
    {
        Referensi::findOrFail($id)->delete();

        return back()->with('msg_flash', success_message('Referensi berhasil dihapus.'));
    }

    private function payload(Request $request, ?Article $existing = null): array
    {
        $request->validate([
            'articleTitle' => ['required', 'string', 'max:'.self::TITLE_MAX],
            'labelId' => ['nullable', 'integer'],
            'categoryId' => ['nullable', 'integer'],
            'subCategoryId' => ['nullable', 'integer'],
            'articleAuthor' => ['required', 'string', 'max:100'],
            'articleDate' => ['nullable', 'date'],
            'createDate' => ['nullable', 'date'],
            'articleImage' => ['nullable', 'image', 'max:2048'],
            'articlePdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'articleContent' => ['nullable', 'string'],
            'articleStatus' => ['required', 'in:0,1,2'],
            'headline' => ['nullable', 'in:1'],
        ], [
            'articleTitle.required' => 'Judul artikel wajib diisi.',
            'articleTitle.string' => 'Judul artikel harus berupa teks.',
            'articleTitle.max' => 'Judul artikel maksimal '.self::TITLE_MAX.' karakter.',
            'labelId.integer' => 'Label yang dipilih tidak valid.',
            'categoryId.integer' => 'Kategori yang dipilih tidak valid.',
            'subCategoryId.integer' => 'Sub kategori yang dipilih tidak valid.',
            'articleAuthor.required' => 'Nama penulis wajib diisi.',
            'articleAuthor.string' => 'Nama penulis harus berupa teks.',
            'articleAuthor.max' => 'Nama penulis maksimal 100 karakter.',
            'articleDate.date' => 'Tanggal artikel tidak valid.',
            'createDate.date' => 'Tanggal buat tidak valid.',
            'articleImage.image' => 'File gambar harus berupa gambar (jpg, png, webp, dll).',
            'articleImage.max' => 'Ukuran gambar maksimal 2 MB.',
            'articlePdf.file' => 'Berkas PDF gagal diunggah.',
            'articlePdf.mimes' => 'File harus berformat PDF.',
            'articlePdf.max' => 'Ukuran PDF maksimal 10 MB.',
            'articleContent.string' => 'Konten artikel tidak valid.',
            'articleStatus.required' => 'Status artikel wajib dipilih.',
            'articleStatus.in' => 'Status artikel yang dipilih tidak valid.',
            'headline.in' => 'Nilai headline tidak valid.',
        ]);

        $title = $request->input('articleTitle');

        // URL artikel dibuat dari judul. Kolom article_uri hanya varchar(100),
        // jadi panjangnya divalidasi agar tidak memicu error database.
        $uri = urlencode(str_replace(' ', '-', strtolower($title)));
        if (strlen($uri) > self::TITLE_MAX) {
            throw ValidationException::withMessages([
                'articleTitle' => 'Judul menghasilkan URL artikel yang terlalu panjang (maksimal '.self::TITLE_MAX.' karakter). Silakan persingkat judul.',
            ]);
        }

        $img = $existing->article_img ?? null;
        if ($request->hasFile('articleImage')) {
            $f = $request->file('articleImage');
            $img = 'artikel_'.kode_unik().'_'.date('ymdHis').'.'.$f->getClientOriginalExtension();
            $f->move(public_path('uploads/img'), $img);
        }

        $pdf = $existing->article_pdf ?? null;
        if ($request->hasFile('articlePdf')) {
            $f = $request->file('articlePdf');
            $pdf = 'pdf_'.kode_unik().'_'.date('ymdHis').'.'.$f->getClientOriginalExtension();
            $f->move(public_path('uploads/pdf'), $pdf);
        }

        return [
            'label_id' => (int) $request->input('labelId', 0),
            'headline_news' => $request->input('headline') == '1' ? 1 : 0,
            'article_date' => $request->input('articleDate') ?: ($existing->article_date ?? date('Y-m-d H:i:s')),
            'article_uri' => $uri,
            'article_title' => $title,
            // Kolom teks NOT NULL: kosong tetap disimpan sebagai string kosong,
            // bukan null, agar tidak melanggar constraint.
            'article_content' => (string) $request->input('articleContent', ''),
            'article_img' => $img,
            'article_status' => (int) $request->input('articleStatus', 2),
            'article_pdf' => $pdf,
            'article_author' => (string) $request->input('articleAuthor', ''),
            'article_created_by' => auth()->id() ?? 0,
            'article_views' => $existing->article_views ?? 0,
            'article_created_date' => $existing->article_created_date ?? date('Y-m-d H:i:s'),
            'create_date' => $request->input('createDate') ?: ($existing->create_date ?? date('Y-m-d')),
        ];
    }

    /**
     * Ubah error database "Data too long" menjadi pesan validasi agar pengguna
     * tahu harus memperpendek isian, bukan mendapat halaman error 500.
     */
    private function failWhenDataTooLong(QueryException $e): never
    {
        $tooLong = (int) ($e->errorInfo[1] ?? 0) === 1406
            || str_contains($e->getMessage(), 'Data too long');

        if (! $tooLong) {
            throw $e;
        }

        throw ValidationException::withMessages([
            'articleTitle' => 'Judul artikel terlalu panjang untuk disimpan (melebihi batas kolom database). Silakan persingkat judul artikel.',
        ]);
    }

    private function syncCategory(Article $article, Request $request): void
    {
        $existing = ArticleCategory::where('article_id', $article->article_id)->first();

        ArticleCategory::where('article_id', $article->article_id)->delete();

        if ($request->filled('categoryId')) {
            $subCategoryId = (int) $request->input('subCategoryId', 0);

            // Pertahankan urutan bila sub-kategori tidak berubah; jika pindah,
            // letakkan di akhir daftar sub-kategori tersebut.
            $urutan = 0;
            if ($subCategoryId > 0) {
                $sameSub = $existing && (int) $existing->sub_category_id === $subCategoryId;
                $urutan = $sameSub
                    ? (int) ($existing->urutan ?? 0)
                    : ((int) ArticleCategory::where('sub_category_id', $subCategoryId)->max('urutan')) + 1;
            }

            ArticleCategory::create([
                'article_id' => $article->article_id,
                'category_id' => (int) $request->input('categoryId'),
                'sub_category_id' => $subCategoryId,
                'urutan' => $urutan,
            ]);
        }
    }
}
