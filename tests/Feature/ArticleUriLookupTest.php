<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleUriLookupTest extends TestCase
{
    use RefreshDatabase;

    private function publishedArticle(string $uri, string $title): Article
    {
        return Article::create([
            'article_uri' => $uri,
            'article_title' => $title,
            'article_author' => 'Admin',
            'article_content' => '<p>Isi artikel.</p>',
            'article_status' => 1,
            'article_views' => 0,
            'article_created_by' => 0,
        ]);
    }

    public function test_uri_tersimpan_bisa_dibuka_persis(): void
    {
        $article = $this->publishedArticle(
            'Tanggung-Jawab-Perusahaan-PT-Terhadap-Pihak-Ketiga',
            'Tanggung Jawab Perusahaan (PT) Terhadap Pihak Ketiga',
        );

        $this->getJson('/api/v1/articles/Tanggung-Jawab-Perusahaan-PT-Terhadap-Pihak-Ketiga')
            ->assertOk()
            ->assertJsonPath('id', $article->article_id);
    }

    public function test_uri_gaya_baru_lowercase_dan_kurung_mengarah_ke_artikel_yang_sama(): void
    {
        $article = $this->publishedArticle(
            'Tanggung-Jawab-Perusahaan-PT-Terhadap-Pihak-Ketiga',
            'Tanggung Jawab Perusahaan (PT) Terhadap Pihak Ketiga',
        );

        $this->getJson('/api/v1/articles/tanggung-jawab-perusahaan-(pt)-terhadap-pihak-ketiga')
            ->assertOk()
            ->assertJsonPath('id', $article->article_id);

        $this->getJson('/api/v1/articles/tanggung-jawab-perusahaan-%28pt%29-terhadap-pihak-ketiga')
            ->assertOk()
            ->assertJsonPath('id', $article->article_id);
    }

    public function test_uri_tidak_dikenal_mengembalikan_404(): void
    {
        $this->publishedArticle(
            'Tidak-Sama-Sekali',
            'Tidak Sama Sekali',
        );

        $this->getJson('/api/v1/articles/slug-yang-tidak-ada')
            ->assertNotFound();
    }

    public function test_artikel_non_terbit_tidak_bisa_dibuka(): void
    {
        Article::create([
            'article_uri' => 'Artikel-Draft',
            'article_title' => 'Artikel Draft',
            'article_author' => 'Admin',
            'article_content' => '<p>Draft.</p>',
            'article_status' => 2,
            'article_views' => 0,
            'article_created_by' => 0,
        ]);

        $this->getJson('/api/v1/articles/Artikel-Draft')->assertNotFound();
    }
}
