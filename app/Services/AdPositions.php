<?php

namespace App\Services;

/**
 * Keterangan penempatan iklan (kolom tbl_ads.ads_position).
 *
 * Posisi 1–23 dipakai halaman situs web, sedangkan posisi 100–103 khusus
 * aplikasi mobile (dikelola dari panel mobile). Sebagian nomor posisi web sudah
 * dihapus karena slotnya tidak dipakai lagi — hanya nomor pada web() yang aktif.
 *
 * Keterangan platform: "D" = desktop (situs web), "DM" = desktop + aplikasi
 * mobile. Peta ini dipakai panel admin web agar pengelola tahu tiap nomor
 * posisi tampil di bagian mana.
 */
class AdPositions
{
    /**
     * Posisi situs web: nomor => keterangan letaknya.
     *
     * @return array<int, string>
     */
    public static function web(): array
    {
        return [
            1 => 'Menu atas — di bawah logo, tampil di semua halaman (D: website)',
            2 => 'Beranda — banner atas, tepat di bawah menu (DM: website + aplikasi)',
            5 => 'Beranda — kolom kiri, setelah berita utama (D: website)',
            6 => 'Beranda — kolom kanan, sejajar posisi 5 (D: website)',
            7 => 'Beranda — bawah, setelah bagian Quote (D: website)',
            8 => 'Sidebar & tile beranda — iklan 1 (D: website)',
            17 => 'Sidebar & tile beranda — iklan 5 (D: website)',
            18 => 'Sidebar & tile beranda — iklan 6 (D: website)',
            19 => 'Sidebar & tile beranda — iklan 7 (DM: website + aplikasi)',
            20 => 'Sidebar & tile beranda — iklan 8 (DM: website + aplikasi)',
            21 => 'Beranda — bawah, setelah posisi 7 (D: website)',
            22 => 'Beranda — bawah, setelah posisi 21 (DM: website + aplikasi)',
        ];
    }

    /**
     * Posisi web yang juga dikirim ke aplikasi mobile (keterangan "DM").
     *
     * @return array<int, int>
     */
    public static function mobileApp(): array
    {
        return [2, 19, 20, 22];
    }

    /**
     * Posisi khusus aplikasi mobile: nomor => keterangan letaknya.
     *
     * @return array<int, string>
     */
    public static function mobile(): array
    {
        return [
            100 => 'Mobile — antar kategori beranda (bisa dipasangkan ke kategori)',
            101 => 'Mobile — bawah beranda, sebelum footer (maks. 3)',
            102 => 'Mobile — atas beranda, di bawah menu (maks. 2)',
            103 => 'Mobile — di atas artikel (maks. 2)',
        ];
    }

    /**
     * Seluruh posisi yang dikenal (web + mobile).
     *
     * @return array<int, string>
     */
    public static function all(): array
    {
        return self::web() + self::mobile();
    }

    /** Keterangan letak sebuah posisi (null bila tidak dikenal). */
    public static function label(int $position): ?string
    {
        return self::all()[$position] ?? null;
    }

    /** Keterangan letak singkat, "Posisi N (keterangan)". */
    public static function describe(int $position): string
    {
        $label = self::label($position);

        return $label === null ? 'Posisi '.$position : 'Posisi '.$position.' — '.$label;
    }
}
