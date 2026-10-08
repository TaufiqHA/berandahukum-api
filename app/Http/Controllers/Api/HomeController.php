<?php

namespace App\Http\Controllers\Api;

use App\Models\Ads;
use App\Models\Article;
use App\Models\Category;
use App\Models\Label;
use App\Models\SubCategory;
use App\Models\SysSetting;
use App\Services\AdPositions;

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

        // Banner/iklan gambar. Hanya posisi web berketerangan "DM" (website +
        // aplikasi) yang dikirim ke aplikasi: posisi 2 (banner atas), 19–20
        // (tile), dan 22 (bawah). Posisi "D" hanya tampil di situs web.
        $ad = function ($pos) {
            if (! in_array($pos, AdPositions::mobileApp(), true)) {
                return null;
            }

            return $this->adImage(
                Ads::where('ads_position', $pos)->where('ads_status', '1')
                    ->where('ads_type', 0)->where('ads_file_type', 0)->first()
            );
        };
        $banners = collect([19, 20])
            ->map(fn ($p) => $ad($p))->filter()->values()->all();

        // Iklan antar-kategori (khusus aplikasi mobile, posisi 100). Mendukung
        // gambar maupun AdMob (ads_kind=1).
        $adsKategori = Ads::where('ads_position', 100)->where('ads_status', '1')
            ->orderBy('ads_urutan')->orderBy('ads_id')
            ->get()->map(function ($a) {
                $data = $this->adMobile($a);
                if ($data === null) {
                    return null;
                }
                $data['category_id'] = $a->ads_category_id ? (int) $a->ads_category_id : null;

                return $data;
            })->filter()->values()->all();

        // Iklan atas beranda (antara banner atas & carousel), maksimal 2.
        $adsAtas = Ads::where('ads_position', 102)->where('ads_status', '1')
            ->orderBy('ads_urutan')->orderBy('ads_id')
            ->get()->map(fn ($a) => $this->adMobile($a))->filter()->take(2)->values()->all();

        // Iklan bawah beranda (sebelum footer), maksimal 3.
        $adsBawah = Ads::where('ads_position', 101)->where('ads_status', '1')
            ->orderBy('ads_urutan')->orderBy('ads_id')
            ->get()->map(fn ($a) => $this->adMobile($a))->filter()->take(3)->values()->all();

        // Iklan full-screen (tidak dirender di feed): interstitial, app open,
        // dan reward. Masing-masing satu Ad Unit.
        $adFull = fn (int $pos) => $this->adMobile(
            Ads::where('ads_position', $pos)->where('ads_status', '1')
                ->orderBy('ads_urutan')->orderBy('ads_id')->first()
        );

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
            'banners' => $banners,
            'ads_kategori' => $adsKategori,
            'ads_atas' => $adsAtas,
            'ads_bawah' => $adsBawah,
            'ads_bottom' => $ad(22),
            'ads_interstitial' => $adFull(104),
            'ads_app_open' => $adFull(105),
            'ads_reward' => $adFull(106),
            'show_pertanyaan' => ($sys->show_pertanyaan ?? '1') === '1',
            'show_youtube' => ($sys->show_youtube ?? '1') === '1',
        ]);
    }
}
