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
     */
    public static function findByUri(string $uri): ?self
    {
        $article = static::where('article_status', 1)->where('article_uri', $uri)->first();
        if ($article) {
            return $article;
        }

        $normalized = static::normalizeUri($uri);

        return static::where('article_status', 1)
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
