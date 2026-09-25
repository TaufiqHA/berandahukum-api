<?php

namespace App\Http\Controllers\Api;

use App\Models\Ads;
use App\Models\Article;
use App\Models\Category;
use App\Models\Label;
use App\Models\Pilihan;
use App\Models\SubCategory;
use App\Models\SysSetting;

class HomeController extends BaseApiController
{
    public function index()
    {
        $slider = Article::select('tbl_article.*')
            ->join('tbl_pilihan as p', 'p.article_id', '=', 'tbl_article.article_id')
            ->where('tbl_article.article_status', 1)
            ->where('p.posisi', 'top')->orderBy('p.urutan')
            ->limit(6)->get()->map(fn ($a) => $this->articleSummary($a->toArray()))->all();

        $latest = Article::where('article_status', 1)->where('headline_news', 0)
            ->orderByDesc('article_date')->orderByDesc('article_id')
            ->limit(10)->get()->map(fn ($a) => $this->articleSummary($a->toArray()))->all();

        $headline = Article::where('article_status', 1)->where('headline_news', 1)
            ->orderByDesc('article_date')->limit(5)
            ->get()->map(fn ($a) => $this->articleSummary($a->toArray()))->all();

        $pilihan = fn ($pos) => Article::select('tbl_article.*')
            ->join('tbl_pilihan as p', 'p.article_id', '=', 'tbl_article.article_id')
            ->where('tbl_article.article_status', 1)->where('p.posisi', $pos)->orderBy('p.urutan')
            ->get()->map(fn ($a) => $this->articleSummary($a->toArray()))->all();

        // Kategori + sub-kategori (hanya yang ditampilkan) untuk kartu accordion.
        $categories = Category::where('category_show', 'yes')->orderBy('urutan')->get()->map(fn ($c) => [
            'id' => (int) $c->category_id,
            'name' => $c->category_name,
            'uri' => $c->category_uri,
            'sub_count' => SubCategory::where('category_id', $c->category_id)->where('sub_category_show', 'yes')->count(),
            'subs' => SubCategory::where('category_id', $c->category_id)->where('sub_category_show', 'yes')
                ->orderBy('sub_category_id')->get()->map(fn ($s) => [
                    'id' => (int) $s->sub_category_id,
                    'name' => $s->sub_category_name,
                    'uri' => $s->sub_category_uri,
                ])->all(),
        ])->all();

        $labels = Label::where('label_show', 'yes')->orderBy('label_id')->get()->map(fn ($l) => [
            'id' => (int) $l->label_id,
            'name' => $l->label_name,
            'uri' => $l->label_uri,
            'count' => Article::where('label_id', $l->label_id)->count(),
        ])->all();

        // Banner/iklan gambar (mengikuti penempatan pada situs mobile).
        // Posisi 8 (Google Play) dikecualikan khusus untuk aplikasi.
        $ad = fn ($pos) => $this->adImage(
            Ads::where('ads_position', $pos)->where('ads_status', '1')
                ->where('ads_type', 0)->where('ads_file_type', 0)->first()
        );
        $banners = collect([9, 10, 16, 17, 18, 19, 20])
            ->map(fn ($p) => $ad($p))->filter()->values()->all();

        $sys = SysSetting::first();

        return response()->json([
            'slider' => $slider,
            'latest' => $latest,
            'headline' => $headline,
            'pilihan_atas' => $pilihan('atas'),
            'pilihan_bawah' => $pilihan('bawah'),
            'categories' => $categories,
            'labels' => $labels,
            'ads_top' => $ad(2),
            'ads_middle' => $ad(12),
            'banners' => $banners,
            'ads_bottom' => $ad(7),
            'show_pertanyaan' => ($sys->show_pertanyaan ?? '1') === '1',
            'show_youtube' => ($sys->show_youtube ?? '1') === '1',
        ]);
    }
}
