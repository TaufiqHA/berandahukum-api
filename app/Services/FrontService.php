<?php

namespace App\Services;

use App\Models\Ads;
use App\Models\AdsPop;
use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Label;
use App\Models\Quote;
use App\Models\Setting;
use App\Models\SubCategory;
use App\Models\SysSetting;

/**
 * Data bersama untuk halaman publik (padanan Display::view + model_welcome/model_side
 * pada aplikasi CodeIgniter lama).
 */
class FrontService
{
    /**
     * Posisi minimum iklan khusus aplikasi mobile. Iklan dengan posisi ini
     * (100–103) tidak pernah ditampilkan pada halaman situs web.
     */
    private const MOBILE_MIN_POSITION = 100;

    public function ad(int $position): ?array
    {
        if ($position >= self::MOBILE_MIN_POSITION) {
            return null;
        }

        $row = Ads::where('ads_position', $position)
            ->where('ads_status', '1')
            ->first();

        return $row ? $row->toArray() : null;
    }

    /** Padanan getArticleById($pos) lama: sebenarnya mengambil iklan berdasarkan posisi. */
    public function getArticleById($position): ?array
    {
        return $this->ad((int) $position);
    }

    public function adsHome(): array
    {
        return Ads::where('ads_status', '1')->whereIn('ads_position', [1, 2, 5, 6, 7])
            ->get()->toArray();
    }

    public function popup(): ?array
    {
        return AdsPop::first()?->toArray();
    }

    public function sysSettings(): ?array
    {
        return SysSetting::first()?->toArray();
    }

    public function categories(): array
    {
        return Category::orderBy('urutan')->orderBy('category_id')
            ->get()
            ->map(function ($c) {
                $c = $c->toArray();
                $c['sc_count'] = SubCategory::where('category_id', $c['category_id'])->count();

                return $c;
            })
            ->all();
    }

    public function subCategories(int $categoryId): array
    {
        return SubCategory::where('category_id', $categoryId)
            ->orderBy('urutan')->orderBy('sub_category_id')->get()->toArray();
    }

    public function articlesByCategory(int $categoryId): array
    {
        return Article::select('tbl_article.*')
            ->join('tbl_article_category as ac', 'ac.article_id', '=', 'tbl_article.article_id')
            ->where('ac.category_id', $categoryId)
            ->where('tbl_article.article_status', '1')
            ->orderByDesc('tbl_article.article_id')
            ->get()->toArray();
    }

    public function articlesBySubCategory(int $categoryId, int $subCategoryId): array
    {
        return Article::select('tbl_article.article_id', 'tbl_article.article_title', 'tbl_article.article_uri')
            ->join('tbl_article_category as ac', 'ac.article_id', '=', 'tbl_article.article_id')
            ->where('ac.category_id', $categoryId)
            ->where('ac.sub_category_id', $subCategoryId)
            ->where('tbl_article.article_status', '1')
            ->orderBy('ac.urutan')
            ->orderBy('tbl_article.article_id')
            ->get()->toArray();
    }

    /**
     * Sub-kategori untuk side menu, diurutkan sama dengan pengaturan urutan di
     * panel admin (tbl_sub_category.urutan, lalu sub_category_id).
     */
    public function sideSubCategories(int $categoryId): array
    {
        return SubCategory::where('category_id', $categoryId)
            ->orderBy('urutan')
            ->orderBy('sub_category_id')
            ->get()->toArray();
    }

    /**
     * Artikel per sub-kategori untuk side menu. Urutannya mengikuti pengaturan
     * "Urut Artikel" di panel admin (tbl_article_category.urutan, lalu tanggal
     * artikel), bukan lagi create_date.
     */
    public function sideSubCategoryArticles(int $categoryId, int $subCategoryId): array
    {
        return Article::select('tbl_article.article_date', 'tbl_article.article_uri', 'tbl_article.article_title', 'tbl_article.article_content', 'tbl_article.article_created_date')
            ->join('tbl_article_category as ac', 'ac.article_id', '=', 'tbl_article.article_id')
            ->where('tbl_article.article_status', '1')
            ->where('ac.category_id', $categoryId)
            ->where('ac.sub_category_id', $subCategoryId)
            ->orderBy('ac.urutan')
            ->orderByDesc('tbl_article.article_date')
            ->get()->toArray();
    }

    /** Artikel per kategori (tanpa sub-kategori) untuk side menu lama. */
    public function sideCategoryArticles(int $categoryId): array
    {
        return Article::select('tbl_article.article_date', 'tbl_article.article_uri', 'tbl_article.article_title', 'tbl_article.article_content', 'tbl_article.article_created_date')
            ->join('tbl_article_category as ac', 'ac.article_id', '=', 'tbl_article.article_id')
            ->where('tbl_article.article_status', '1')
            ->where('ac.category_id', $categoryId)
            ->orderByDesc('tbl_article.create_date')
            ->get()->toArray();
    }

    public function labels(): array
    {
        return Label::where('label_show', 'yes')->orderBy('label_id')->get()
            ->map(function ($l) {
                $l = $l->toArray();
                $l['jumlah'] = Article::where('label_id', $l['label_id'])->count();

                return $l;
            })->all();
    }

    public function articlesByLabel(int $labelId, int $limit = 5): array
    {
        return Article::where('article_status', '1')
            ->where('label_id', $labelId)
            ->orderByDesc('article_id')
            ->limit($limit)->get()->toArray();
    }

    public function footerInfo(): array
    {
        return Setting::where('tipe', 'info')->orderBy('urutan')->get()->toArray();
    }

    public function footerSosial(): array
    {
        return Setting::where('tipe', 'sosial')->orderBy('urutan')->get()->toArray();
    }

    public function articlePilihan(string $posisi): array
    {
        return Article::select('tbl_article.*')
            ->join('tbl_pilihan as p', 'p.article_id', '=', 'tbl_article.article_id')
            ->where('tbl_article.article_status', '1')
            ->where('p.posisi', $posisi)
            ->orderBy('p.urutan')
            ->get()->toArray();
    }

    /** Artikel yang ditandai Headline — dipakai slider/sorotan beranda. */
    public function headline(int $limit = 6): array
    {
        return Article::where('article_status', '1')
            ->where('headline_news', 1)
            ->orderByDesc('article_date')
            ->orderByDesc('article_id')
            ->limit($limit)
            ->get()->toArray();
    }

    public function quotes(): array
    {
        return Quote::where('quote_status', '1')->orderBy('urutan')->get()->toArray();
    }

    public function mostView(): array
    {
        return Article::where('article_status', '1')
            ->orderByDesc('article_views')->limit(6)->get()->toArray();
    }

    public function mostComment(): array
    {
        return Comment::select('tbl_article.*')
            ->join('tbl_article', 'tbl_comment.article_id', '=', 'tbl_article.article_id')
            ->where('tbl_article.article_status', '1')
            ->where('tbl_comment.comment_status', '1')
            ->orderByDesc('tbl_comment.comment_status')
            ->limit(6)->get()->toArray();
    }
}
