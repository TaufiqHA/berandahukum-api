<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminArticleSortTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'user_name' => 'Admin',
            'user_email' => 'admin@example.test',
            'user_password' => 'secret',
            'user_level' => 'admin',
        ]);
    }

    private function article(string $title, int $views): Article
    {
        return Article::create([
            'article_uri' => strtolower(str_replace(' ', '-', $title)),
            'article_title' => $title,
            'article_author' => 'Admin',
            'article_content' => '<p>Isi.</p>',
            'article_status' => 1,
            'article_views' => $views,
            'article_created_by' => 0,
        ]);
    }

    public function test_daftar_artikel_bisa_diurutkan_berdasar_jumlah_view(): void
    {
        $this->article('Artikel Jarang', 1);
        $this->article('Artikel Populer', 5);
        $this->article('Artikel Sedang', 3);

        $admin = $this->admin();

        $this->actingAs($admin)
            ->get('/admin/article?sort=views_desc')
            ->assertOk()
            ->assertSeeInOrder(['Artikel Populer', 'Artikel Sedang', 'Artikel Jarang']);

        $this->actingAs($admin)
            ->get('/admin/article?sort=views_asc')
            ->assertOk()
            ->assertSeeInOrder(['Artikel Jarang', 'Artikel Sedang', 'Artikel Populer']);
    }

    public function test_tanpa_sort_tetap_urut_artikel_terbaru(): void
    {
        $this->article('Artikel Jarang', 1);
        $this->article('Artikel Populer', 5);
        $this->article('Artikel Sedang', 3);

        $this->actingAs($this->admin())
            ->get('/admin/article')
            ->assertOk()
            ->assertSeeInOrder(['Artikel Sedang', 'Artikel Populer', 'Artikel Jarang']);
    }
}
