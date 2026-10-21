<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Category;
use App\Models\Label;
use App\Models\SubCategory;
use App\Services\ArticleSearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends BaseApiController
{
    public function index(Request $request, ArticleSearch $search)
    {
        return $this->listing($request, $search, false);
    }

    /**
     * Pencarian artikel untuk aplikasi mobile. Berbeda dari daftar artikel
     * biasa: gambar tidak dikirim dan excerpt dilengkapi segmen highlight.
     */
    public function search(Request $request, ArticleSearch $search)
    {
        return $this->listing($request, $search, true);
    }

    private function listing(Request $request, ArticleSearch $search, bool $searchMode)
    {
        $query = Article::query()->where('article_status', 1);

        if ($request->filled('category')) {
            $cat = Category::where('category_uri', $request->input('category'))->first();
            $query->whereIn('article_id', ArticleCategory::where('category_id', $cat->category_id ?? 0)->pluck('article_id'));
        }
        if ($request->filled('sub_category')) {
            $sub = SubCategory::where('sub_category_uri', $request->input('sub_category'))->first();
            $query->whereIn('article_id', ArticleCategory::where('sub_category_id', $sub->sub_category_id ?? 0)->pluck('article_id'));
        }
        if ($request->filled('label')) {
            $label = Label::where('label_uri', $request->input('label'))->first();
            $query->where('label_id', $label->label_id ?? 0);
        }

        $rawQuery = $request->input('q');
        $q = is_string($rawQuery) ? mb_substr(trim($rawQuery), 0, ArticleSearch::MAX_QUERY_LENGTH) : '';
        $terms = $search->terms($q);

        if ($terms !== []) {
            // Cari di judul dan isi artikel (isi sudah berupa teks bersih).
            $search->apply($query, $terms);
            $search->orderByRelevance($query, $terms);
        } elseif ($q !== '') {
            // Query hanya berisi wildcard/tanda baca: hasil kosong, bukan semua.
            $query->whereRaw('1 = 0');
        }

        $sort = $request->input('sort', 'date');
        if ($sort === 'views') {
            $query->orderByDesc('article_views');
        } else {
            $query->orderByDesc('article_date')->orderByDesc('article_id');
        }

        if ($terms !== []) {
            // article_content tidak diperlukan karena excerpt hasil pencarian
            // diambil dari article_search_text yang jauh lebih ringkas.
            $query->select([
                'article_id',
                'label_id',
                'article_uri',
                'article_title',
                'article_date',
                'article_author',
                'article_img',
                'article_views',
                'article_search_text',
            ]);
        }

        $paginator = $query->paginate((int) $request->input('per_page', 12))
            ->through(function (Article $article) use ($search, $terms, $searchMode): array {
                $summary = $this->articleSummary($article->toArray());

                if ($searchMode) {
                    unset($summary['image']);
                }

                if ($terms !== []) {
                    $excerpt = $search->snippet($article->article_search_text, $terms);
                    $summary['excerpt'] = $excerpt;
                    $summary['excerpt_segments'] = $search->segments($excerpt, $terms);
                }

                return $summary;
            });

        return response()->json($paginator);
    }

    public function show(string $uri)
    {
        $article = Article::findByUri($uri);
        if (! $article) {
            return response()->json(['message' => 'Artikel tidak ditemukan'], 404);
        }

        DB::table('tbl_article')->where('article_id', $article->article_id)->increment('article_views');

        return response()->json($this->articleDetail($article->toArray()));
    }
}
