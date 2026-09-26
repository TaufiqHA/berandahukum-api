<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Label;
use App\Models\Referensi;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends AdminApiController
{
    public function index(Request $request)
    {
        $query = Article::query()->orderByDesc('article_id');

        if ($request->filled('q')) {
            $query->where('article_title', 'like', '%'.$request->input('q').'%');
        }
        if ($request->filled('status')) {
            $query->where('article_status', (int) $request->input('status'));
        }
        if ($request->filled('label')) {
            $query->where('label_id', (int) $request->input('label'));
        }

        $paginator = $query->paginate((int) $request->input('per_page', 15))
            ->through(fn ($a) => $this->mapArticle($a));

        return $this->ok([
            'data' => $paginator->items(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'total' => $paginator->total(),
        ]);
    }

    public function options()
    {
        return $this->ok([
            'labels' => Label::orderBy('urutan')->orderBy('label_id')->get()
                ->map(fn ($l) => ['id' => (int) $l->label_id, 'name' => $l->label_name, 'uri' => $l->label_uri])->all(),
            'categories' => Category::orderBy('urutan')->orderBy('category_id')->get()->map(fn ($c) => [
                'id' => (int) $c->category_id,
                'name' => $c->category_name,
                'uri' => $c->category_uri,
                'subs' => SubCategory::where('category_id', $c->category_id)->orderBy('urutan')->orderBy('sub_category_id')->get()
                    ->map(fn ($s) => ['id' => (int) $s->sub_category_id, 'name' => $s->sub_category_name, 'uri' => $s->sub_category_uri])->all(),
            ])->all(),
        ]);
    }

    public function show($id)
    {
        $article = Article::findOrFail($id);

        return $this->ok(['data' => $this->mapArticle($article, true)]);
    }

    public function store(Request $request)
    {
        $article = Article::create($this->payload($request));
        $this->syncCategory($article, $request);

        return $this->ok(['message' => 'Artikel berhasil disimpan.', 'data' => $this->mapArticle($article)], 201);
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $article->update($this->payload($request, $article));
        $this->syncCategory($article, $request);

        return $this->ok(['message' => 'Artikel berhasil diubah.', 'data' => $this->mapArticle($article)]);
    }

    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        DB::table('tbl_article_category')->where('article_id', $article->article_id)->delete();
        $article->delete();

        return $this->message('Artikel berhasil dihapus.');
    }

    public function publish($id)
    {
        Article::findOrFail($id)->update(['article_status' => 1]);

        return $this->message('Artikel berhasil dipublish.');
    }

    public function draft($id)
    {
        Article::findOrFail($id)->update(['article_status' => 2]);

        return $this->message('Artikel diubah ke draft.');
    }

    // ---- Komentar per artikel ----

    public function comments($id)
    {
        $article = Article::findOrFail($id);
        $comments = Comment::where('article_id', $id)->orderByDesc('comment_date')->get()
            ->map(fn ($c) => $this->mapComment($c))->all();

        return $this->ok([
            'article' => ['id' => (int) $article->article_id, 'title' => $article->article_title],
            'data' => $comments,
        ]);
    }

    public function publishComment($id)
    {
        Comment::findOrFail($id)->update(['comment_status' => 1]);

        return $this->message('Komentar ditampilkan.');
    }

    public function unpublishComment($id)
    {
        Comment::findOrFail($id)->update(['comment_status' => 0]);

        return $this->message('Komentar disembunyikan.');
    }

    public function deleteComment($id)
    {
        Comment::findOrFail($id)->delete();

        return $this->message('Komentar dihapus.');
    }

    // ---- Referensi bacaan ----

    public function referensi($id)
    {
        $article = Article::findOrFail($id);
        $list = Referensi::where('article_ref', $id)->orderBy('urutan')->get()->map(function ($r) {
            $art = $r->article_id ? Article::find($r->article_id) : null;

            return [
                'id' => (int) $r->id,
                'sumber' => $r->sumber,
                'title' => $r->sumber === 'int' ? ($art->article_title ?? '') : $r->judul,
                'uri' => $r->sumber === 'int' ? ($art->article_uri ?? null) : $r->link,
                'article_id' => $r->article_id ? (int) $r->article_id : null,
                'urutan' => (int) $r->urutan,
            ];
        })->all();

        return $this->ok([
            'article' => ['id' => (int) $article->article_id, 'title' => $article->article_title],
            'data' => $list,
        ]);
    }

    public function referensiStore(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $request->validate(['sumber' => 'required|in:int,ext', 'urutan' => 'required|integer']);

        $data = [
            'sumber' => $request->input('sumber'),
            'article_ref' => $article->article_id,
            'urutan' => (int) $request->input('urutan'),
        ];

        if ($request->input('sumber') === 'int') {
            $data['article_id'] = (int) $request->input('article_id');
        } else {
            $data['judul'] = $request->input('judul');
            $data['link'] = $request->input('link');
        }

        Referensi::create($data);

        return $this->message('Referensi berhasil disimpan.', 201);
    }

    public function referensiDestroy($id)
    {
        Referensi::findOrFail($id)->delete();

        return $this->message('Referensi dihapus.');
    }

    // ---- Helpers ----

    private function payload(Request $request, ?Article $existing = null): array
    {
        $request->validate(['title' => 'required|string|max:200']);

        $title = $request->input('title');
        $img = $existing->article_img ?? null;
        $uploadedImg = $this->storeUpload($request, 'image', 'artikel', 'img');
        if ($uploadedImg) {
            $img = $uploadedImg;
        } elseif ($request->filled('image_name')) {
            $img = $request->input('image_name');
        } elseif ($request->boolean('remove_image')) {
            $img = null;
        }

        $pdf = $existing->article_pdf ?? null;
        $uploadedPdf = $this->storeUpload($request, 'pdf', 'pdf', 'pdf');
        if ($uploadedPdf) {
            $pdf = $uploadedPdf;
        } elseif ($request->filled('pdf_name')) {
            $pdf = $request->input('pdf_name');
        } elseif ($request->boolean('remove_pdf')) {
            $pdf = null;
        }

        return [
            'label_id' => (int) $request->input('label_id', 0),
            'headline_news' => $request->input('headline') ? 1 : 0,
            'article_date' => $request->input('article_date') ?: date('Y-m-d H:i:s'),
            'article_uri' => urlencode(str_replace(' ', '-', strtolower($title))),
            'article_title' => $title,
            'article_content' => $request->input('content', ''),
            'article_img' => $img,
            'article_status' => (int) $request->input('status', 2),
            'article_pdf' => $pdf,
            'article_author' => $request->input('author', ''),
            'article_views' => $existing->article_views ?? 0,
            'article_created_by' => $existing->article_created_by ?? auth()->id(),
            'article_created_date' => $existing->article_created_date ?? date('Y-m-d H:i:s'),
            'create_date' => $request->input('create_date') ?: date('Y-m-d'),
        ];
    }

    private function syncCategory(Article $article, Request $request): void
    {
        $existing = DB::table('tbl_article_category')->where('article_id', $article->article_id)->first();

        DB::table('tbl_article_category')->where('article_id', $article->article_id)->delete();

        $categoryId = (int) $request->input('category_id', 0);
        if ($categoryId > 0) {
            $subCategoryId = (int) $request->input('sub_category_id', 0);

            // Pertahankan urutan bila sub-kategori tidak berubah; jika pindah
            // sub-kategori, letakkan di akhir daftar sub-kategori tersebut.
            $urutan = 0;
            if ($subCategoryId > 0) {
                $sameSub = $existing && (int) $existing->sub_category_id === $subCategoryId;
                $urutan = $sameSub
                    ? (int) ($existing->urutan ?? 0)
                    : ((int) DB::table('tbl_article_category')->where('sub_category_id', $subCategoryId)->max('urutan')) + 1;
            }

            DB::table('tbl_article_category')->insert([
                'article_id' => $article->article_id,
                'category_id' => $categoryId,
                'sub_category_id' => $subCategoryId,
                'urutan' => $urutan,
                'created_date' => now(),
            ]);
        }
    }

    private function mapArticle(Article $a, bool $withContent = false): array
    {
        $junction = DB::table('tbl_article_category')->where('article_id', $a->article_id)->first();
        $label = $a->label_id ? Label::find($a->label_id) : null;
        $cat = ($junction && $junction->category_id) ? Category::find($junction->category_id) : null;
        $sub = ($junction && $junction->sub_category_id) ? SubCategory::find($junction->sub_category_id) : null;
        $author = $a->article_created_by ? \App\Models\User::find($a->article_created_by) : null;

        $data = [
            'id' => (int) $a->article_id,
            'uri' => $a->article_uri,
            'title' => $a->article_title,
            'image' => $this->imageField($a->article_img),
            'pdf' => $a->article_pdf ?: null,
            'date' => $a->article_date,
            'author' => $a->article_author,
            'created_by' => $author->user_name ?? 'Admin',
            'status' => (int) $a->article_status,
            'headline' => (int) $a->headline_news === 1,
            'views' => (int) $a->article_views,
            'label_id' => (int) $a->label_id,
            'label_name' => $label->label_name ?? null,
            'category_id' => $cat->category_id ?? null,
            'category_name' => $cat->category_name ?? null,
            'sub_category_id' => $sub->sub_category_id ?? null,
            'sub_category_name' => $sub->sub_category_name ?? null,
        ];

        if ($withContent) {
            $data['content'] = $a->article_content;
        }

        return $data;
    }

    private function imageField(?string $file): ?string
    {
        if (empty($file)) {
            return null;
        }
        if (str_starts_with($file, 'http')) {
            return $file;
        }

        return 'uploads/img/'.$file;
    }

    private function mapComment(Comment $c): array
    {
        return [
            'id' => (int) $c->comment_id,
            'article_id' => (int) $c->article_id,
            'name' => $c->comment_name,
            'fill' => $c->comment_fill,
            'reply' => $c->comment_reply,
            'status' => (int) $c->comment_status,
            'date' => $c->comment_date,
        ];
    }
}
