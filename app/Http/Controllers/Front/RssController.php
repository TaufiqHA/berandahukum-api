<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Label;

class RssController extends Controller
{
    public function index()
    {
        return $this->feed(null);
    }

    public function getrss(string $uri)
    {
        return $this->feed($uri);
    }

    private function feed(?string $labelUri)
    {
        $query = Article::where('article_status', 1)->orderByDesc('article_id');

        if ($labelUri) {
            $label = Label::where('label_uri', $labelUri)->first();
            if ($label) {
                $query->where('label_id', $label->label_id);
            }
        }

        $articles = $query->limit(20)->get();

        $content = view('front.rss_view', [
            'articles' => $articles,
            'siteTitle' => 'Beranda Hukum',
            'siteUrl' => url('/'),
        ])->render();

        return response($content, 200)->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }
}
