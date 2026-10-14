<?php

namespace Tests\Feature;

use App\Models\Ads;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleAdSlotsTest extends TestCase
{
    use RefreshDatabase;

    private function publishedArticle(): Article
    {
        return Article::create([
            'article_uri' => 'ruang-lingkup-hukum-bisnis',
            'article_title' => 'Ruang Lingkup Hukum Bisnis',
            'article_author' => 'Admin',
            'article_content' => '<p>Isi artikel.</p>',
            'article_status' => 1,
            'article_views' => 0,
            'article_created_by' => 0,
        ]);
    }

    private function ad(int $position, string $file): Ads
    {
        return Ads::create([
            'ads_position' => $position,
            'ads_type' => '0',
            'ads_file_type' => '0',
            'ads_url' => $file,
            'ads_link' => '#',
            'ads_status' => '1',
        ]);
    }

    public function test_halaman_artikel_menampilkan_slot_iklan_atas_dan_bawah(): void
    {
        $this->publishedArticle();
        $this->ad(9, 'banner-atas.jpg');
        $this->ad(10, 'banner-bawah.jpg');

        $this->get('/a/ruang-lingkup-hukum-bisnis')
            ->assertOk()
            ->assertSee('uploads/i/banner-atas.jpg')
            ->assertSee('uploads/i/banner-bawah.jpg');
    }

    public function test_iklan_khusus_mobile_tidak_tampil_di_halaman_artikel_web(): void
    {
        $this->publishedArticle();
        $this->ad(103, 'mobile-atas.jpg');

        $this->get('/a/ruang-lingkup-hukum-bisnis')
            ->assertOk()
            ->assertDontSee('uploads/i/mobile-atas.jpg');
    }
}
