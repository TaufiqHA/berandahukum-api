<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

    public function test_kata_kunci_di_isi_artikel_ikut_menampilkan_hasil(): void
    {
        $this->article([
            'article_uri' => 'topik-lain',
            'article_title' => 'Topik Lain Sama Sekali',
            'article_content' => '<p>Artikel ini menyebut pencarian di dalam isinya.</p>',
        ]);

        $this->get('/search?q=pencarian')
            ->assertOk()
            ->assertSee('Topik Lain Sama Sekali')
            ->assertSee('menyebut <mark>pencarian</mark>', false);
    }

    public function test_kata_di_dalam_tag_html_tidak_ikut_dicari(): void
    {
        $this->article([
            'article_uri' => 'artikel-markup',
            'article_title' => 'Artikel Markup',
            'article_content' => '<p class="rahasia-tag">Isi biasa tanpa kata kunci.</p>',
        ]);

        $this->get('/search?q=rahasia-tag')
            ->assertOk()
            ->assertSee('Tidak ada hasil')
            ->assertDontSee('Artikel Markup');
    }

    public function test_pencarian_isi_tidak_case_sensitive(): void
    {
        $this->article([
            'article_uri' => 'artikel-huruf-kecil',
            'article_title' => 'Artikel Huruf Kecil',
            'article_content' => '<p>Putusan nomor 1074 k/pdt/1995.</p>',
        ]);

        $this->get('/search?q=1074')
            ->assertOk()
            ->assertSee('Artikel Huruf Kecil')
            ->assertSee('<mark>1074</mark>', false);
    }

    public function test_hasil_judul_didahulukan_dari_hasil_isi(): void
    {
        $this->article([
            'article_uri' => 'judul-cocok',
            'article_title' => 'Jaminan Tanah',
            'article_content' => '<p>Artikel lama.</p>',
            'article_date' => '2020-01-01 00:00:00',
        ]);
        $this->article([
            'article_uri' => 'isi-cocok',
            'article_title' => 'Artikel Lain',
            'article_content' => '<p>Membahas jaminan secara umum.</p>',
            'article_date' => '2026-01-01 00:00:00',
        ]);

        $response = $this->get('/search?q=jaminan')->assertOk();
        $content = $response->getContent();

        $this->assertLessThan(
            strpos($content, 'Artikel Lain'),
            strpos($content, 'Jaminan Tanah')
        );
    }

    public function test_query_wildcard_tidak_menampilkan_semua_artikel(): void
    {
        $this->article([
            'article_uri' => 'artikel-biasa',
            'article_title' => 'Artikel Biasa',
        ]);

        $this->get('/search?q=%25')
            ->assertOk()
            ->assertSee('Tidak ada hasil')
            ->assertDontSee('Artikel Biasa');
    }

    public function test_command_reindex_mengisi_teks_pencarian_artikel_lama(): void
    {
        DB::table('tbl_article')->insert([
            'article_uri' => 'artikel-impor-lama',
            'article_title' => 'Artikel Impor Lama',
            'article_author' => 'Admin',
            'article_content' => '<p>Warisan data yang belum tersinkronisasi.</p>',
            'article_search_text' => null,
            'article_status' => 1,
            'article_views' => 0,
            'article_created_by' => 0,
            'article_date' => '2026-10-13 08:43:57',
        ]);

        $this->artisan('articles:reindex-search')->assertSuccessful();

        $this->get('/search?q=tersinkronisasi')
            ->assertOk()
            ->assertSee('Artikel Impor Lama');
    }
}
