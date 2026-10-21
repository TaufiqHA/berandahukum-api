<?php

namespace Tests\Feature\Api;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    private function article(array $attributes = []): Article
    {
        return Article::create(array_merge([
            'article_uri' => 'putusan-nomor-1074',
            'article_title' => 'Putusan Perjanjian Jual Beli Tanah',
            'article_author' => 'Admin',
            'article_content' => '<p>No. 1074 K/PDT/1995 Tanggal 18 Mei 1996</p>',
            'article_img' => '10ccmrfrvuv_230224120303.jpg',
            'article_status' => 1,
            'article_views' => 0,
            'article_created_by' => 0,
            'article_date' => '2026-10-13 08:43:57',
        ], $attributes));
    }

    public function test_pencarian_api_mencari_kata_di_dalam_isi_artikel(): void
    {
        $this->article();

        $response = $this->getJson('/api/v1/search?q=1074')
            ->assertOk()
            ->assertJsonPath('data.0.title', 'Putusan Perjanjian Jual Beli Tanah')
            ->assertJsonMissingPath('data.0.image');

        $this->assertStringContainsString('1074', (string) $response->json('data.0.excerpt'));

        $highlighted = collect($response->json('data.0.excerpt_segments'))
            ->firstWhere('highlight', true);

        $this->assertSame('1074', $highlighted['text'] ?? null);
    }

    public function test_pencarian_api_tidak_mencocokkan_nama_tag_html(): void
    {
        $this->article([
            'article_uri' => 'artikel-markup',
            'article_title' => 'Artikel Markup Api',
            'article_content' => '<p class="rahasia-api">Isi biasa.</p>',
        ]);

        $this->getJson('/api/v1/search?q=rahasia-api')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_daftar_artikel_biasa_tetap_mengirim_gambar(): void
    {
        $this->article();

        $this->getJson('/api/v1/articles?q=1074')
            ->assertOk()
            ->assertJsonPath('data.0.image', 'uploads/img/10ccmrfrvuv_230224120303.jpg');
    }
}
