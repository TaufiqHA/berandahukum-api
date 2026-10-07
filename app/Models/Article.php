<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'tbl_article';

    protected $primaryKey = 'article_id';

    public $timestamps = false;

    protected $guarded = [];

    /**
     * Cari artikel terbit berdasarkan URI.
     *
     * Data lama menyimpan article_uri dalam Title Case tanpa slug (mis.
     * "Tanggung-Jawab-Perusahaan-PT-Terhadap-Pihak-Ketiga"), sedangkan data baru
     * memakai slug lowercase yang meng-encode tanda kurung
     * (mis. "tanggung-jawab-perusahaan-%28pt%29-..."). Agar kedua bentuk URL
     * tetap bisa dibuka, pencarian fallback membandingkan URI dalam bentuk
     * yang sudah dinormalisasi.
     *
     * Karena article_uri tidak unik (banyak data lama bertabrakan), pencarian
     * bersifat case-insensitive, tetapi kecocokan persis selalu diprioritaskan
     * dan kandidat terbaru dipilih bila tetap ambigu. Dengan begitu URL sebuah
     * artikel tidak "dibajak" artikel lain yang URI-nya kebetulan sama.
     */
    public static function findByUri(string $uri): ?self
    {
        $normalized = static::normalizeUri($uri);

        // Bandingkan memakai LOWER() agar hasilnya sama di database
        // case-sensitive (SQLite) maupun case-insensitive (MySQL). Banyak URI
        // lama Title Case bertabrakan dengan slug baru yang lowercase, sehingga
        // pencarian dengan "=" bisa mengembalikan artikel yang salah.
        $candidates = static::where('article_status', 1)
            ->whereRaw('LOWER(article_uri) = ?', [mb_strtolower($uri)])
            ->orderByDesc('article_id')
            ->get();

        // 1. Prioritaskan kecocokan persis (termasuk huruf besar/kecil) supaya
        //    tiap artikel tetap bisa dibuka lewat URL-nya sendiri.
        $exact = $candidates->first(fn (self $candidate) => $candidate->article_uri === $uri);
        if ($exact) {
            return $exact;
        }

        // 2. Cocok setelah dinormalisasi (encoding/tanda baca berbeda).
        $byNormalized = $candidates->first(
            fn (self $candidate) => static::normalizeUri($candidate->article_uri) === $normalized
        );
        if ($byNormalized) {
            return $byNormalized;
        }

        // 3. Fallback penuh untuk bentuk slug yang benar-benar berbeda
        //    (mis. slug baru yang meng-encode tanda kurung).
        return static::where('article_status', 1)
            ->orderByDesc('article_id')
            ->get()
            ->first(fn (self $candidate) => static::normalizeUri($candidate->article_uri) === $normalized);
    }

    /**
     * Samakan bentuk URI agar perbedaan huruf besar/kecil, URL-encoding, tanda
     * kurung, dan pemisah lain tidak dianggap berbeda. Contoh:
     * "Purchase-Order-(PO)-sebagai..." dan "purchase-order-po-sebagai..."
     * sama-sama menjadi "purchase-order-po-sebagai...".
     */
    public static function normalizeUri(string $uri): string
    {
        $lowered = mb_strtolower(rawurldecode($uri));

        return trim((string) preg_replace('/[^a-z0-9]+/', '-', $lowered), '-');
    }
}
