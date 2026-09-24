<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q', '');

        $articles = Article::where('article_status', 1)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('article_title', 'like', '%'.$q.'%')
                        ->orWhere('article_content', 'like', '%'.$q.'%');
                });
            })
            ->orderByDesc('article_id')
            ->paginate(10)
            ->withQueryString();

        return view('front.search_list', [
            'title' => 'Pencarian - Beranda Hukum',
            'heading' => 'Hasil Pencarian: '.$q,
            'q' => $q,
            'articles' => $articles,
        ]);
    }
}
