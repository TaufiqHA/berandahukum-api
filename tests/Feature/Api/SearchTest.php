<?php

namespace Tests\Feature\Api;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_pencarian_api_mencari_kata_di_dalam_isi_artikel(): void
    {
        Article::create([
            'article_uri' => 'putusan-nomor-1074',
            'article_title' => 'Putusan Perjanjian Jual Beli Tanah',
            'article_author' => 'Admin',
            'article_content' => '<p>No. 1074 K/PDT/1995 Tanggal 18 Mei 1996</p>',
            'article_status' => 1,
            'article_views' => 0,
            'article_created_by' => 0,
            'article_date' => '2026-10-13 08:43:57',
        ]);

        $response = $this->getJson('/api/v1/search?q=1074')
            ->assertOk()
            ->assertJsonPath('data.0.title', 'Putusan Perjanjian Jual Beli Tanah');

        $this->assertStringContainsString('1074', (string) $response->json('data.0.excerpt'));
    }

    public function test_pencarian_api_tidak_mencocokkan_nama_tag_html(): void
    {
        Article::create([
            'article_uri' => 'artikel-markup',
            'article_title' => 'Artikel Markup Api',
            'article_author' => 'Admin',
            'article_content' => '<p class="rahasia-api">Isi biasa.</p>',
            'article_status' => 1,
            'article_views' => 0,
            'article_created_by' => 0,
            'article_date' => '2026-10-13 08:43:57',
        ]);

        $this->getJson('/api/v1/search?q=rahasia-api')
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }
}
