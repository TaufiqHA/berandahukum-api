<?php

namespace App\Services;

use App\Models\Ads;
use App\Models\AdsPop;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Label;
use App\Models\Menu;
use App\Models\Pilihan;
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
    public function ad(int $position): ?array
    {
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
        return Ads::where('ads_status', '1')->whereIn('ads_position', [1, 2, 3, 4, 5, 6, 7])
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
            ->orderBy('sub_category_id')->get()->toArray();
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
            ->orderBy('tbl_article.article_id')
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

    public function menuTop(): array
    {
        $links = [];
        $totalLen = 0;

        foreach (Menu::orderBy('urutan')->get() as $mn) {
            $row = null;
            $title = null;
            $url = '#';

            switch ($mn->tipe) {
                case 'artikel':
                    $row = Article::find($mn->id_menu);
                    $title = $row?->article_title;
                    $url = site_url('a/'.$mn->uri_menu);
                    break;
                case 'label':
                    $row = Label::find($mn->id_menu);
                    $title = $row?->label_name;
                    if ($row && $row->label_show === 'yes') {
                        $url = site_url('l/'.$mn->uri_menu);
                    } else {
                        $url = 'hide';
                    }
                    break;
                case 'kategori':
                    $row = Category::find($mn->id_menu);
                    $title = $row?->category_name;
                    if ($row && $row->category_show === 'yes') {
                        $url = site_url('k/'.$mn->uri_menu);
                    } else {
                        $url = 'hide';
                    }
                    break;
                case 'subkategori':
                    $row = SubCategory::find($mn->id_menu);
                    $title = $row?->sub_category_name;
                    if ($row && $row->sub_category_show === 'yes') {
                        $url = site_url('s/'.$mn->uri_menu);
                    } else {
                        $url = 'hide';
                    }
                    break;
            }

            if ($row && $title !== null && $url !== 'hide') {
                $active = request()->segment(2) === $mn->uri_menu;
                $links[] = ['title' => $title, 'url' => $url, 'active' => $active];
                $totalLen += strlen($title);
            }
        }

        return [$links, $totalLen];
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
