<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Urutan sub-kategori di dalam kategori, dan urutan artikel di dalam
 * sub-kategori (pada tabel relasi tbl_article_category).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('tbl_sub_category', 'urutan')) {
            Schema::table('tbl_sub_category', function (Blueprint $table) {
                $table->integer('urutan')->default(0);
            });
        }

        if (! Schema::hasColumn('tbl_article_category', 'urutan')) {
            Schema::table('tbl_article_category', function (Blueprint $table) {
                $table->integer('urutan')->default(0);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tbl_sub_category', 'urutan')) {
            Schema::table('tbl_sub_category', function (Blueprint $table) {
                $table->dropColumn('urutan');
            });
        }

        if (Schema::hasColumn('tbl_article_category', 'urutan')) {
            Schema::table('tbl_article_category', function (Blueprint $table) {
                $table->dropColumn('urutan');
            });
        }
    }
};
