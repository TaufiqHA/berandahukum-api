<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Category;
use App\Models\Label;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class TaxonomyController extends BaseApiController
{
    public function categories()
    {
        return response()->json(Category::where('category_show', 'yes')->orderBy('urutan')->get()->map(fn ($c) => [
            'id' => (int) $c->category_id,
            'name' => $c->category_name,
            'uri' => $c->category_uri,
            'subs' => SubCategory::where('category_id', $c->category_id)->where('sub_category_show', 'yes')
                ->orderBy('sub_category_id')->get()->map(fn ($s) => [
                    'id' => (int) $s->sub_category_id,
                    'name' => $s->sub_category_name,
                    'uri' => $s->sub_category_uri,
                ])->all(),
        ])->all());
    }

    public function category(Request $request, string $uri)
    {
        $cat = Category::where('category_uri', $uri)->first();
        if (! $cat) {
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }

        $paginator = Article::where('article_status', 1)
            ->whereIn('article_id', ArticleCategory::where('category_id', $cat->category_id)->pluck('article_id'))
            ->orderByDesc('article_date')
            ->paginate((int) $request->input('per_page', 12))
            ->through(fn ($a) => $this->articleSummary($a->toArray()));

        return response()->json([
            'category' => ['id' => (int) $cat->category_id, 'name' => $cat->category_name, 'uri' => $cat->category_uri],
            'articles' => $paginator,
        ]);
    }

    public function subcategory(Request $request, string $uri)
    {
        $sub = SubCategory::where('sub_category_uri', $uri)->first();
        if (! $sub) {
            return response()->json(['message' => 'Sub kategori tidak ditemukan'], 404);
        }

        $paginator = Article::where('article_status', 1)
            ->whereIn('article_id', ArticleCategory::where('sub_category_id', $sub->sub_category_id)->pluck('article_id'))
            ->orderByDesc('article_date')
            ->paginate((int) $request->input('per_page', 12))
            ->through(fn ($a) => $this->articleSummary($a->toArray()));

        return response()->json([
            'sub_category' => ['id' => (int) $sub->sub_category_id, 'name' => $sub->sub_category_name, 'uri' => $sub->sub_category_uri],
            'articles' => $paginator,
        ]);
    }

    public function labels()
    {
        return response()->json(Label::where('label_show', 'yes')->orderBy('label_id')->get()->map(fn ($l) => [
            'id' => (int) $l->label_id,
            'name' => $l->label_name,
            'uri' => $l->label_uri,
            'count' => Article::where('label_id', $l->label_id)->count(),
        ])->all());
    }

    public function label(Request $request, string $uri)
    {
        $label = Label::where('label_uri', $uri)->first();
        if (! $label) {
            return response()->json(['message' => 'Label tidak ditemukan'], 404);
        }

        $paginator = Article::where('article_status', 1)->where('label_id', $label->label_id)
            ->orderByDesc('article_date')
            ->paginate((int) $request->input('per_page', 12))
            ->through(fn ($a) => $this->articleSummary($a->toArray()));

        return response()->json([
            'label' => ['id' => (int) $label->label_id, 'name' => $label->label_name, 'uri' => $label->label_uri],
            'articles' => $paginator,
        ]);
    }
}
