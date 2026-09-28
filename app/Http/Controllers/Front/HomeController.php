<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Article;
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
            // Data untuk tampilan mobile (desain lama) — dipertahankan apa adanya.
            'article' => Article::where('article_status', 1)
                ->where('headline_news', 0)
                ->orderByDesc('article_date')
                ->orderByDesc('article_id')
                ->limit(9)->get()->toArray(),
            'art_headline' => $this->front->headline(6),
            // Data untuk tampilan desktop (desain referensi).
            'articleDesktop' => Article::where('article_status', '1')
                ->where('headline_news', 0)
                ->whereNotIn('article_id', function ($q) {
                    $q->select('article_id')->from('tbl_article_category');
                })
                ->orderByDesc('article_id')
                ->limit(6)->get()->toArray(),
            'tmpHeadLine' => $this->front->headline(5),
            'banner_home' => Banner::where('status', 'yes')->orderBy('urutan')->get()->toArray(),
            'article_pilihanatas' => $this->front->articlePilihan('atas'),
            'article_pilihanbawah' => $this->front->articlePilihan('bawah'),
            'art_pilihan_top' => $this->front->articlePilihan('top'),
        ];

        return view('front.home', $data);
    }
}
