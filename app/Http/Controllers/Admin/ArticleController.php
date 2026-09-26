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
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::orderByDesc('article_id');

        if ($request->filled('q')) {
            $query->where('article_title', 'like', '%'.$request->input('q').'%');
        }

        return view('admin.article.index', [
            'title' => 'Daftar Artikel',
            'articles' => $query->paginate(20)->withQueryString(),
            'labels' => Label::pluck('label_name', 'label_id'),
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
        $article = Article::create($this->payload($request));
        $this->syncCategory($article, $request);

        return redirect(site_admin('article'))->with('msg_flash', success_message('Data artikel berhasil disimpan.'));
    }

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $article->update($this->payload($request, $article));
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
        $request->validate(['articleTitle' => 'required']);

        $title = $request->input('articleTitle');

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
            'article_date' => $request->input('articleDate') ?: date('Y-m-d H:i:s'),
            'article_uri' => urlencode(str_replace(' ', '-', strtolower($title))),
            'article_title' => $title,
            'article_content' => $request->input('articleContent', ''),
            'article_img' => $img,
            'article_status' => (int) $request->input('articleStatus', 2),
            'article_pdf' => $pdf,
            'article_author' => $request->input('articleAuthor', ''),
            'article_created_by' => auth()->id(),
            'article_created_date' => $existing->article_created_date ?? date('Y-m-d H:i:s'),
            'create_date' => $request->input('createDate') ?: date('Y-m-d'),
        ];
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
