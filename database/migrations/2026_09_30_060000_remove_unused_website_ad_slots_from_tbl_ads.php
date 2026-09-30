<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Hapus iklan pada slot web yang tidak dipakai lagi (posisi 3, 4, 9–16, 23).
 * Slot ini dihapus beserta datanya agar tidak menyisakan iklan usang.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('tbl_ads')
            ->whereIn('ads_position', [3, 4, 9, 10, 11, 12, 13, 14, 15, 16, 23])
            ->delete();
    }

    public function down(): void
    {
        // Data yang dihapus tidak dapat dikembalikan.
    }
};
