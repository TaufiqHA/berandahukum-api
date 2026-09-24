<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Category;
use App\Models\Label;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends BaseApiController
{
    public function index(Request $request)
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
        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($w) use ($q) {
                $w->where('article_title', 'like', '%'.$q.'%')->orWhere('article_content', 'like', '%'.$q.'%');
            });
        }

        $sort = $request->input('sort', 'date');
        if ($sort === 'views') {
            $query->orderByDesc('article_views');
        } else {
            $query->orderByDesc('article_date')->orderByDesc('article_id');
        }

        $paginator = $query->paginate((int) $request->input('per_page', 12))
            ->through(fn ($a) => $this->articleSummary($a->toArray()));

        return response()->json($paginator);
    }

    public function show(string $uri)
    {
        DB::table('tbl_article')->where('article_uri', $uri)->increment('article_views');

        $article = Article::where('article_status', 1)->where('article_uri', $uri)->first();
        if (! $article) {
            return response()->json(['message' => 'Artikel tidak ditemukan'], 404);
        }

        return response()->json($this->articleDetail($article->toArray()));
    }
}
