<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchResultsTest extends TestCase
{
    use RefreshDatabase;

    private function article(array $attributes): Article
    {
        return Article::create(array_merge([
            'article_author' => 'Admin',
            'article_content' => '<p>Isi artikel.</p>',
            'article_status' => 1,
            'article_views' => 0,
            'article_created_by' => 0,
            'article_date' => '2026-10-13 08:43:57',
        ], $attributes));
    }

    public function test_hasil_pencarian_menampilkan_judul_tanpa_gambar(): void
    {
        $this->article([
            'article_uri' => 'berita-pencarian-unik',
            'article_title' => 'Berita Pencarian Unik',
            'article_img' => 'foto-pencarian.jpg',
        ]);

        $this->get('/search?q=Pencarian')
            ->assertOk()
            ->assertSee('Berita Pencarian Unik')
            ->assertDontSee('story__media')
            ->assertDontSee('foto-pencarian.jpg');
    }

    public function test_kata_kunci_di_isi_artikel_tidak_ikut_menampilkan_hasil(): void
    {
        $this->article([
            'article_uri' => 'topik-lain',
            'article_title' => 'Topik Lain Sama Sekali',
            'article_content' => '<p>Artikel ini menyebut pencarian di dalam isinya.</p>',
        ]);

        $this->get('/search?q=pencarian')
            ->assertOk()
            ->assertSee('Tidak ada hasil')
            ->assertDontSee('Topik Lain Sama Sekali');
    }
}
