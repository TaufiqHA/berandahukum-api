<?php

use App\Support\ArticleSearchText;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('tbl_article', 'article_search_text')) {
            Schema::table('tbl_article', function (Blueprint $table) {
                $table->longText('article_search_text')->nullable()->after('article_content');
            });
        }

        // Isi kolom untuk artikel lama. Dikerjakan per batch agar tidak memuat
        // seluruh HTML (ada artikel berukuran >2 MB) sekaligus ke memori.
        DB::table('tbl_article')
            ->select(['article_id', 'article_content'])
            ->orderBy('article_id')
            ->chunkById(100, function ($articles): void {
                // Satu transaksi per batch agar ribuan UPDATE tidak memicu
                // fsync satu per satu (migrasi jadi jauh lebih cepat).
                DB::transaction(function () use ($articles): void {
                    foreach ($articles as $article) {
                        DB::table('tbl_article')
                            ->where('article_id', $article->article_id)
                            ->update([
                                'article_search_text' => ArticleSearchText::fromHtml($article->article_content),
                            ]);
                    }
                });
            }, 'article_id');
    }

    public function down(): void
    {
        if (Schema::hasColumn('tbl_article', 'article_search_text')) {
            Schema::table('tbl_article', function (Blueprint $table) {
                $table->dropColumn('article_search_text');
            });
        }
    }
};
