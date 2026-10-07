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

    public function test_uri_yang_bertabrakan_beda_huruf_besar_kecil_tetap_terpisah(): void
    {
        // Data lama memakai Title Case, sedangkan artikel baru memakai slug
        // lowercase. Keduanya harus tetap bisa dibuka lewat URL-nya sendiri dan
        // tidak saling menimpa.
        $lama = $this->publishedArticle('Bab-I-Ketentuan-Umum', 'Bab I - Ketentuan Umum');
        $lama->update(['article_content' => '<p>Isi lama.</p>']);

        $baru = $this->publishedArticle('bab-i-ketentuan-umum', 'BAB I Ketentuan Umum');
        $baru->update(['article_content' => '<p>Isi baru.</p>']);

        $this->getJson('/api/v1/articles/bab-i-ketentuan-umum')
            ->assertOk()
            ->assertJsonPath('id', $baru->article_id);

        $this->getJson('/api/v1/articles/Bab-I-Ketentuan-Umum')
            ->assertOk()
            ->assertJsonPath('id', $lama->article_id);
    }

    public function test_uri_duplikat_mengembalikan_artikel_terbaru(): void
    {
        $this->publishedArticle('Perkara-Gugur', 'Perkara Gugur');
        $terbaru = $this->publishedArticle('Perkara-Gugur', 'Perkara Gugur');

        $this->getJson('/api/v1/articles/Perkara-Gugur')
            ->assertOk()
            ->assertJsonPath('id', $terbaru->article_id);
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
