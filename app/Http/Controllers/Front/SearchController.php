<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\ArticleSearch;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request, ArticleSearch $search)
    {
        $rawQuery = $request->query('q');
        $q = is_string($rawQuery) ? mb_substr(trim($rawQuery), 0, ArticleSearch::MAX_QUERY_LENGTH) : '';
        $terms = $search->terms($q);

        $articles = Article::query()
            ->where('article_status', 1)
            ->when($terms !== [], function ($query) use ($search, $terms): void {
                // Cari di judul dan isi artikel (isi sudah berupa teks bersih).
                $search->apply($query, $terms);
                $search->orderByRelevance($query, $terms);
                $query->orderByDesc('article_date')->orderByDesc('article_id');
            })
            ->when($q !== '' && $terms === [], function ($query): void {
                // Query hanya berisi wildcard/tanda baca: jangan tampilkan
                // seluruh artikel seolah-olah semuanya cocok.
                $query->whereRaw('1 = 0');
            })
            ->when($terms === [], function ($query): void {
                $query->orderByDesc('article_id');
            })
            ->select(['article_id', 'article_uri', 'article_title', 'article_date', 'article_search_text'])
            ->paginate(10)
            ->withQueryString()
            ->through(function (Article $article) use ($search, $terms): array {
                $excerpt = $terms === [] ? '' : $search->snippet($article->article_search_text, $terms);

                return [
                    'article_id' => $article->article_id,
                    'article_uri' => $article->article_uri,
                    'article_title' => $article->article_title,
                    'article_date' => $article->article_date,
                    'excerpt_html' => $terms === [] ? '' : $search->toHtml($excerpt, $terms),
                ];
            });

        return view('front.search_list', [
            'title' => 'Pencarian - Beranda Hukum',
            'heading' => 'Hasil Pencarian: '.$q,
            'q' => $q,
            'articles' => $articles,
        ]);
    }
}
