<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Iklan antar-kategori (mobile) bisa dipasangkan ke kategori tertentu,
 * sehingga banner di setiap celah kategori bisa berbeda.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_ads', function (Blueprint $table) {
            $table->integer('ads_category_id')->nullable()->after('ads_position');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_ads', function (Blueprint $table) {
            $table->dropColumn('ads_category_id');
        });
    }
};
