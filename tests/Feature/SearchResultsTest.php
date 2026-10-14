<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchResultsTest extends TestCase
{
    use RefreshDatabase;

    private function publishedArticle(): Article
    {
        return Article::create([
            'article_uri' => 'berita-pencarian-unik',
            'article_title' => 'Berita Pencarian Unik',
            'article_author' => 'Admin',
            'article_content' => '<p>Isi artikel pencarian.</p>',
            'article_img' => 'foto-pencarian.jpg',
            'article_status' => 1,
            'article_views' => 0,
            'article_created_by' => 0,
            'article_date' => '2026-10-13 08:43:57',
        ]);
    }

    public function test_hasil_pencarian_menampilkan_judul_tanpa_gambar(): void
    {
        $this->publishedArticle();

        $this->get('/search?q=Pencarian')
            ->assertOk()
            ->assertSee('Berita Pencarian Unik')
            ->assertDontSee('story__media')
            ->assertDontSee('foto-pencarian.jpg');
    }
}
