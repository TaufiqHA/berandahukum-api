<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Label;
use App\Models\Referensi;
use App\Models\SubCategory;
use Illuminate\Support\Str;

/**
 * Basis API publik untuk aplikasi mobile.
 * URL gambar dikembalikan relatif (mis. "uploads/img/x.jpg"); app mobile
 * menempelkan base URL-nya sendiri.
 */
abstract class BaseApiController extends Controller
{
    protected function image(?string $file): ?string
    {
        if (empty($file)) {
            return null;
        }
        if (str_starts_with($file, 'http')) {
            return $file;
        }
        if (! is_file(public_path('uploads/img/'.$file))) {
            return null;
        }

        return 'uploads/img/'.$file;
    }

    protected function excerpt(?string $html, int $limit = 190): string
    {
        $text = html_entity_decode(strip_tags($html ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = trim(preg_replace('/\s+/', ' ', $text));

        return Str::limit($text, $limit);
    }

    protected function articleSummary(array $a): array
    {
        $label = null;
        if (! empty($a['label_id'])) {
            $l = Label::find($a['label_id']);
            $label = $l ? ['id' => (int) $l->label_id, 'name' => $l->label_name, 'uri' => $l->label_uri] : null;
        }

        return [
            'id' => (int) $a['article_id'],
            'uri' => $a['article_uri'],
            'title' => $a['article_title'],
            'excerpt' => $this->excerpt($a['article_content'] ?? ''),
            'image' => $this->image($a['article_img'] ?? null),
            'date' => $a['article_date'],
            'author' => $a['article_author'] ?: 'Admin',
            'views' => (int) ($a['article_views'] ?? 0),
            'label' => $label,
        ];
    }

    protected function articleDetail(array $a): array
    {
        $data = $this->articleSummary($a);
        $data['content'] = $a['article_content'] ?? '';
        $data['pdf'] = $a['article_pdf'] ?: null;

        $data['categories'] = \Illuminate\Support\Facades\DB::table('tbl_article_category as ac')
            ->leftJoin('tbl_category as c', 'ac.category_id', '=', 'c.category_id')
            ->leftJoin('tbl_sub_category as sc', 'ac.sub_category_id', '=', 'sc.sub_category_id')
            ->where('ac.article_id', $a['article_id'])
            ->select('c.category_id', 'c.category_name', 'c.category_uri', 'sc.sub_category_id', 'sc.sub_category_name', 'sc.sub_category_uri')
            ->get()
            ->map(fn ($r) => [
                'category' => $r->category_id ? ['name' => $r->category_name, 'uri' => $r->category_uri] : null,
                'sub_category' => $r->sub_category_id ? ['name' => $r->sub_category_name, 'uri' => $r->sub_category_uri] : null,
            ])->all();

        $data['referensi'] = Referensi::where('article_ref', $a['article_id'])->orderBy('urutan')->get()
            ->map(function ($r) {
                $art = Article::find($r->article_id);

                return [
                    'sumber' => $r->sumber,
                    'title' => $r->sumber === 'int' ? ($art->article_title ?? '') : $r->judul,
                    'uri' => $r->sumber === 'int' ? ($art->article_uri ?? null) : $r->link,
                    'external' => $r->sumber !== 'int',
                ];
            })->all();

        $data['related'] = Article::where('article_status', 1)
            ->where('article_id', '!=', $a['article_id'])
            ->when($a['label_id'], fn ($q) => $q->where('label_id', $a['label_id']))
            ->inRandomOrder()->limit(6)->get()->map(fn ($x) => $this->articleSummary($x->toArray()))->all();

        $data['comments'] = Comment::where('article_id', $a['article_id'])
            ->where('comment_status', '1')->orderByDesc('comment_date')->get()
            ->map(fn ($c) => [
                'name' => $c->comment_name,
                'fill' => $c->comment_fill,
                'reply' => $c->comment_reply,
                'date' => $c->comment_date,
            ])->all();

        return $data;
    }

    protected function categoryRef(?int $id, ?string $name, ?string $uri): ?array
    {
        return $id ? ['id' => (int) $id, 'name' => $name, 'uri' => $uri] : null;
    }
}
