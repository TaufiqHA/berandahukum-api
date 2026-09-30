<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Hapus iklan pada penempatan section Mitra (posisi 24–35). Slot ini tidak
 * dipakai lagi, sehingga barisnya dibersihkan agar tidak menyisakan data usang.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('tbl_ads')->whereBetween('ads_position', [24, 35])->delete();
    }

    public function down(): void
    {
        // Data yang dihapus tidak dapat dikembalikan.
    }
};
