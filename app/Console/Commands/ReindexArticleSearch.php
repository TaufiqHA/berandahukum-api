<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Support\ArticleSearchText;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('articles:reindex-search {--chunk=100}')]
#[Description('Bangun ulang teks pencarian artikel dari konten HTML')]
class ReindexArticleSearch extends Command
{
    public function handle(): int
    {
        $chunk = max(1, (int) $this->option('chunk'));
        $processed = 0;

        Article::query()
            ->select(['article_id', 'article_content', 'article_search_text'])
            ->orderBy('article_id')
            ->chunkById($chunk, function ($articles) use (&$processed): void {
                DB::transaction(function () use ($articles): void {
                    foreach ($articles as $article) {
                        $text = ArticleSearchText::fromHtml($article->article_content);

                        if ($article->article_search_text === $text) {
                            continue;
                        }

                        Article::whereKey($article->article_id)
                            ->update(['article_search_text' => $text]);
                    }
                });

                $processed += $articles->count();
                $this->output->write("\rDiproses: {$processed}");
            }, 'article_id');

        $this->newLine();
        $this->info('Teks pencarian artikel berhasil dibangun ulang.');

        return self::SUCCESS;
    }
}
