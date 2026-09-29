<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Urutan tampil iklan mobile (per penempatan) agar bisa digeser dari panel
 * admin. Nilai kecil tampil lebih dulu; 0 = warisan (diurutkan lewat ads_id).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_ads', function (Blueprint $table) {
            $table->integer('ads_urutan')->default(0)->after('ads_category_id');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_ads', function (Blueprint $table) {
            $table->dropColumn('ads_urutan');
        });
    }
};
