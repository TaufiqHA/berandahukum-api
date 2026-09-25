<?php

namespace App\Services;

use App\Models\Setting;

/**
 * Tata letak halaman beranda (urutan + tampil/sembunyikan section).
 * Disimpan pada tbl_settings (tipe "layout", nama "home") sebagai JSON.
 */
class HomeLayout
{
    public const TIPE = 'layout';

    public const NAME = 'home';

    /**
     * Daftar section beranda.
     * scope: "both" | "mobile" | "desktop" — hanya memengaruhi di mana section
     * dirender (mobile/desktop), bukan pengaturannya.
     */
    public static function sections(): array
    {
        return [
            'banner_atas' => ['label' => 'Banner Atas', 'scope' => 'mobile'],
            'slider' => ['label' => 'Carousel Sorotan', 'scope' => 'both'],
            'iklan_atas' => ['label' => 'Iklan Atas', 'scope' => 'desktop'],
            'hash' => ['label' => 'Pembatas # + Iklan', 'scope' => 'mobile'],
            'terbaru' => ['label' => 'Terbaru', 'scope' => 'desktop'],
            'tiles' => ['label' => 'Tile Banner / Buku & Layanan', 'scope' => 'both'],
            'categories' => ['label' => 'Kartu Kategori', 'scope' => 'both'],
            'headline' => ['label' => 'Headline', 'scope' => 'desktop'],
            'pilihan_editor' => ['label' => 'Pilihan Editor', 'scope' => 'desktop'],
            'label' => ['label' => 'Section Label', 'scope' => 'desktop'],
            'most_read' => ['label' => 'Paling Banyak Dibaca/Dikomentari', 'scope' => 'desktop'],
            'quote' => ['label' => 'Quote', 'scope' => 'desktop'],
            'youtube' => ['label' => 'Youtube', 'scope' => 'desktop'],
            'mitra' => ['label' => 'Mitra', 'scope' => 'desktop'],
            'iklan_bawah' => ['label' => 'Iklan Bawah', 'scope' => 'mobile'],
        ];
    }

    /** Urutan default — menjaga tampilan beranda seperti sekarang. */
    public static function default(): array
    {
        return array_map(
            fn ($key) => ['key' => $key, 'enabled' => true],
            array_keys(self::sections())
        );
    }

    public static function get(): array
    {
        $row = Setting::where('tipe', self::TIPE)->where('setting_name', self::NAME)->first();

        if ($row && ! empty($row->setting_content)) {
            $decoded = json_decode($row->setting_content, true);
            if (is_array($decoded) && $decoded) {
                return self::normalize($decoded);
            }
        }

        return self::default();
    }

    public static function save(array $items): void
    {
        Setting::updateOrCreate(
            ['tipe' => self::TIPE, 'setting_name' => self::NAME],
            [
                'setting_content' => json_encode(self::normalize($items)),
                'urutan' => 0,
                'setting_last_updated' => date('Y-m-d H:i'),
            ]
        );
    }

    /** Buang key tak dikenal, hilangkan duplikat, tambahkan section baru di akhir. */
    private static function normalize(array $items): array
    {
        $valid = array_keys(self::sections());
        $out = [];
        $seen = [];

        foreach ($items as $it) {
            $key = is_array($it) ? ($it['key'] ?? null) : $it;
            if (! $key || ! in_array($key, $valid, true) || isset($seen[$key])) {
                continue;
            }
            $out[] = ['key' => $key, 'enabled' => (bool) (is_array($it) ? ($it['enabled'] ?? true) : true)];
            $seen[$key] = true;
        }

        foreach ($valid as $key) {
            if (! isset($seen[$key])) {
                $out[] = ['key' => $key, 'enabled' => true];
            }
        }

        return $out;
    }
}
