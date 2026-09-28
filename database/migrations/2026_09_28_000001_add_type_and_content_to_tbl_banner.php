<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Banner beranda mendukung beberapa tipe:
 *   0 = Gambar (file_banner + link_url)
 *   1 = Iframe / Embed (banner_content berisi URL)
 *   2 = Script / HTML (banner_content berisi kode mentah)
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('tbl_banner', 'banner_type')) {
            Schema::table('tbl_banner', function (Blueprint $table) {
                $table->integer('banner_type')->default(0);
            });
        }

        if (! Schema::hasColumn('tbl_banner', 'banner_content')) {
            Schema::table('tbl_banner', function (Blueprint $table) {
                $table->longText('banner_content')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::table('tbl_banner', function (Blueprint $table) {
            $table->dropColumn(['banner_type', 'banner_content']);
        });
    }
};
