<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dukungan iklan AdMob native (in-feed) untuk aplikasi mobile.
 *   - ads_kind       : 0 = gambar (bawaan), 1 = AdMob native
 *   - ads_admob_unit : Ad Unit ID AdMob (mis. ca-app-pub-xxxx/yyyy)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_ads', function (Blueprint $table) {
            $table->integer('ads_kind')->default(0)->after('ads_file_type');
            $table->string('ads_admob_unit')->nullable()->after('ads_kind');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_ads', function (Blueprint $table) {
            $table->dropColumn(['ads_kind', 'ads_admob_unit']);
        });
    }
};
