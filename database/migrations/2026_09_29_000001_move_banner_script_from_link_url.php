<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Sebelum kolom banner_type/banner_content ada, kode script/iframe banner
 * disimpan di link_url. Migrasi ini memindahkannya ke banner_content agar
 * form admin dan halaman beranda menampilkan/menjalankannya dengan benar.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('tbl_banner', 'banner_type')
            || ! Schema::hasColumn('tbl_banner', 'banner_content')) {
            return;
        }

        $rows = DB::table('tbl_banner')
            ->whereIn('banner_type', [1, 2])
            ->get(['id_banner', 'link_url', 'banner_content']);

        foreach ($rows as $row) {
            $content = trim((string) $row->banner_content);
            $link = (string) $row->link_url;

            if ($link === '') {
                continue;
            }

            // Hanya sentuh bila link_url memang berisi markup/kode, bukan URL gambar.
            if (! preg_match('/<[a-z!\/]/i', $link)) {
                continue;
            }

            $update = ['link_url' => null];

            if ($content === '') {
                $update['banner_content'] = $link;
            }

            DB::table('tbl_banner')->where('id_banner', $row->id_banner)->update($update);
        }
    }

    public function down(): void
    {
        // Tidak mengembalikan data: banner_content tetap dipakai.
    }
};
