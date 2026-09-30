<?php

namespace App\Services;

/**
 * Keterangan penempatan iklan (kolom tbl_ads.ads_position).
 *
 * Posisi 1–23 dipakai halaman situs web, sedangkan posisi 100–103 khusus
 * aplikasi mobile (dikelola dari panel mobile). Peta ini dipakai panel admin
 * web agar pengelola tahu tiap nomor posisi tampil di bagian mana.
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
            1 => 'Menu atas — di bawah logo, tampil di semua halaman',
            2 => 'Beranda — banner atas, tepat di bawah menu (juga banner atas mobile)',
            3 => 'Beranda — kolom kiri, di bawah artikel pilihan',
            4 => 'Beranda — kolom kanan, sejajar posisi 3',
            5 => 'Beranda — kolom kiri, setelah berita utama',
            6 => 'Beranda — kolom kanan, sejajar posisi 5',
            7 => 'Beranda — bawah, setelah bagian Quote (juga iklan bawah mobile)',
            8 => 'Sidebar & tile beranda — iklan 1',
            9 => 'Sidebar & tile beranda — iklan 2',
            10 => 'Sidebar & tile beranda — iklan 3',
            11 => 'Tidak digunakan',
            12 => 'Beranda — pembatas "#" (mobile)',
            13 => 'Tidak digunakan',
            14 => 'Beranda — bawah, baris penuh setelah posisi 5–6',
            15 => 'Footer — kolom Follow Us',
            16 => 'Sidebar & tile beranda — iklan 4',
            17 => 'Sidebar & tile beranda — iklan 5',
            18 => 'Sidebar & tile beranda — iklan 6',
            19 => 'Sidebar & tile beranda — iklan 7',
            20 => 'Sidebar & tile beranda — iklan 8',
            21 => 'Beranda — bawah, setelah posisi 7',
            22 => 'Beranda — bawah, setelah posisi 21',
            23 => 'Beranda — bawah, setelah posisi 22',
        ];
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
