<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Label;
use App\Models\Referensi;
use App\Models\SubCategory;
use App\Services\FrontService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    public function __construct(private FrontService $front)
    {
    }

    public function detail(string $uri)
    {
        DB::table('tbl_article')->where('article_uri', $uri)->increment('article_views');

        $article = Article::where('article_status', 1)->where('article_uri', $uri)->first();
        if (! $article) {
            abort(404);
        }

        $a = $article->toArray();
        $authorName = optional(\App\Models\User::find($a['article_created_by']))->user_name ?? 'Admin';

        $referensi = Referensi::where('article_ref', $a['article_id'])
            ->orderBy('urutan')->get()->map(function ($r) {
                $r = $r->toArray();
                $art = Article::find($r['article_id']);
                $r['article_title'] = $art->article_title ?? '';
                $r['article_uri'] = $art->article_uri ?? '';

                return $r;
            })->all();

        $related = Article::where('article_status', 1)
            ->where('article_id', '!=', $a['article_id'])
            ->when($a['label_id'], fn ($q) => $q->where('label_id', $a['label_id']))
            ->inRandomOrder()->limit(6)->get()->toArray();

        $comments = Comment::where('article_id', $a['article_id'])
            ->where('comment_status', '1')->get()->toArray();

        return view('front.article_detail', [
            'title' => $a['article_title'].' - Beranda Hukum',
            'article' => $a,
            'admin_name' => $authorName,
            'articles' => Article::where('article_status', 1)->orderByDesc('article_id')->limit(6)->get()->toArray(),
            'referensibacaan' => $referensi,
            'related_post' => $related,
            'comments' => $comments,
            'og_type' => 'website',
            'og_title' => $a['article_title'].' - Beranda Hukum',
            'og_description' => substr(strip_tags($a['article_content'] ?? ''), 0, 250),
            'og_url' => site_url('a/'.$a['article_uri']),
        ]);
    }

    public function kategori(string $uri)
    {
        $cat = Category::where('category_uri', $uri)->first();
        if (! $cat) {
            abort(404);
        }

        $articles = Article::select('tbl_article.*')
            ->join('tbl_article_category as ac', 'ac.article_id', '=', 'tbl_article.article_id')
            ->where('ac.category_id', $cat->category_id)
            ->where('tbl_article.article_status', '1')
            ->orderByDesc('tbl_article.create_date')
            ->paginate(10);

        return view('front.list', [
            'title' => $cat->category_name.' - Beranda Hukum',
            'heading' => $cat->category_name,
            'articles' => $articles,
        ]);
    }

    public function subkategori(string $uri)
    {
        $sub = SubCategory::where('sub_category_uri', $uri)->first();
        if (! $sub) {
            abort(404);
        }

        $articles = Article::select('tbl_article.*')
            ->join('tbl_article_category as ac', 'ac.article_id', '=', 'tbl_article.article_id')
            ->where('ac.sub_category_id', $sub->sub_category_id)
            ->where('tbl_article.article_status', '1')
            ->orderByDesc('tbl_article.create_date')
            ->paginate(10);

        return view('front.list', [
            'title' => $sub->sub_category_name.' - Beranda Hukum',
            'heading' => $sub->sub_category_name,
            'articles' => $articles,
        ]);
    }

    public function label(string $uri)
    {
        $label = Label::where('label_uri', $uri)->first();
        if (! $label) {
            abort(404);
        }

        $articles = Article::where('article_status', 1)
            ->where('label_id', $label->label_id)
            ->orderByDesc('create_date')
            ->paginate(10);

        return view('front.list', [
            'title' => $label->label_name.' - Beranda Hukum',
            'heading' => $label->label_name,
            'articles' => $articles,
        ]);
    }

    public function addComment(Request $request)
    {
        Comment::create([
            'article_id' => $request->input('article_id'),
            'comment_name' => $request->input('comment_nama'),
            'comment_fill' => $request->input('comment_fill'),
            'comment_status' => '0',
        ]);

        return response()->json([
            'error' => false,
            'message' => success_message('Komentar anda berhasil disimpan dan akan ditampilkan setelah terverifikasi!'),
        ]);
    }

    public function loadComment(Request $request)
    {
        $comments = Comment::where('article_id', $request->input('article_id'))
            ->where('comment_status', '1')
            ->get(['comment_date', 'comment_name', 'comment_fill', 'comment_reply']);

        return response()->json(['comments' => $comments->toArray()]);
    }
}
