<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticlePreviewTest extends TestCase
{
    use RefreshDatabase;

    private function draftArticle(): Article
    {
        return Article::create([
            'article_uri' => 'artikel-draft-preview',
            'article_title' => 'Artikel Draft Preview',
            'article_author' => 'Admin',
            'article_content' => '<p>Isi draft.</p>',
            'article_status' => 2,
            'article_views' => 0,
            'article_created_by' => 0,
        ]);
    }

    private function admin(): User
    {
        return User::create([
            'user_name' => 'Admin',
            'user_email' => 'admin@example.test',
            'user_password' => 'secret',
            'user_level' => 'admin',
        ]);
    }

    public function test_admin_bisa_pratinjau_artikel_draft(): void
    {
        $article = $this->draftArticle();

        $this->actingAs($this->admin())
            ->get(route('article.preview', $article->article_id))
            ->assertOk()
            ->assertSee('Artikel Draft Preview');
    }

    public function test_halaman_pratinjau_butuh_login(): void
    {
        $article = $this->draftArticle();

        $this->get(route('article.preview', $article->article_id))
            ->assertRedirect(route('admin.login'));
    }

    public function test_artikel_draft_tidak_tampil_di_halaman_publik(): void
    {
        $this->draftArticle();

        $this->get('/a/artikel-draft-preview')->assertNotFound();
    }

    public function test_tautan_admin_mengarah_ke_halaman_pratinjau(): void
    {
        $article = $this->draftArticle();

        $this->actingAs($this->admin())
            ->get('/admin/article/preview/'.$article->article_id)
            ->assertRedirect(route('article.preview', $article->article_id));
    }
}
