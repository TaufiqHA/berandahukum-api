<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Banner;
use App\Services\FrontService;

class HomeController extends Controller
{
    public function __construct(private FrontService $front)
    {
    }

    public function index()
    {
        $data = [
            'title' => 'Beranda Hukum',
            'label' => $this->front->labels(),
            'article' => Article::where('article_status', 1)
                ->where('headline_news', 0)
                ->orderByDesc('article_date')
                ->orderByDesc('article_id')
                ->limit(9)->get()->toArray(),
            'tmpHeadLine' => Article::where('article_status', 1)
                ->where('headline_news', 1)->orderByDesc('article_date')->limit(5)->get()->toArray(),
            'banner_home' => Banner::where('status', 'yes')->orderBy('urutan')->get()->toArray(),
            'article_pilihanatas' => $this->front->articlePilihan('atas'),
            'article_pilihanbawah' => $this->front->articlePilihan('bawah'),
            'art_pilihan_top' => $this->front->articlePilihan('top'),
        ];

        return view('front.home', $data);
    }
}
