<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;

/**
 * Logika pencarian artikel berdasarkan judul dan isi.
 *
 * Isi artikel dicari pada kolom `article_search_text` (HTML sudah dibersihkan)
 * agar kata di dalam tag/atribut tidak menghasilkan kecocokan palsu.
 */
final class ArticleSearch
{
    public const MAX_QUERY_LENGTH = 100;

    /**
     * Batas jumlah kata kunci agar pencarian tetap ringan. Semua kata harus
     * ditemukan (AND) supaya hasil makin spesifik untuk query yang panjang.
     */
    public const MAX_TERMS = 5;

    /**
     * Jumlah karakter konteks di kiri dan kanan kata yang cocok.
     */
    private const SNIPPET_RADIUS = 110;

    /**
     * Pecah query menjadi kata kunci yang aman dipakai pada LIKE.
     *
     * @return list<string>
     */
    public function terms(string $query): array
    {
        $query = mb_substr(trim($query), 0, self::MAX_QUERY_LENGTH);
        $query = preg_replace('/\s+/u', ' ', $query) ?? $query;

        // Wildcard SQL dianggap sebagai pemisah, sehingga "%" atau "_" yang
        // diketik pengguna tidak mengubah arti pencarian.
        $query = str_replace(['%', '_', '\\'], ' ', $query);

        $terms = preg_split('/\s+/u', $query, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $terms = array_map(
            fn (string $term): string => trim($term, " \t\n\r\0\x0B.,;:!?\"'()[]{}"),
            $terms
        );
        $terms = array_values(array_unique(array_filter($terms, fn (string $term): bool => $term !== '')));

        return array_slice($terms, 0, self::MAX_TERMS);
    }

    /**
     * Terapkan semua kata kunci sebagai kondisi AND: tiap kata boleh berada di
     * judul atau di isi artikel.
     *
     * @param  list<string>  $terms
     */
    public function apply(Builder $query, array $terms): void
    {
        foreach ($terms as $term) {
            $like = '%'.$term.'%';

            $query->where(function (Builder $nested) use ($like): void {
                $nested->where('article_title', 'like', $like)
                    ->orWhere('article_search_text', 'like', $like);
            });
        }
    }

    /**
     * Artikel yang judulnya mengandung lebih banyak kata kunci ditampilkan
     * lebih dulu. Ini mencegah artikel kompilasi besar menenggelamkan artikel
     * yang memang spesifik membahas kata kunci tersebut.
     *
     * @param  list<string>  $terms
     */
    public function orderByRelevance(Builder $query, array $terms): void
    {
        if ($terms === []) {
            return;
        }

        $cases = implode(' + ', array_fill(0, count($terms), 'CASE WHEN article_title LIKE ? THEN 1 ELSE 0 END'));

        $query->orderByRaw('('.$cases.') DESC', array_map(fn (string $term): string => '%'.$term.'%', $terms));
    }

    /**
     * Potongan isi di sekitar kata pertama yang cocok.
     *
     * @param  list<string>  $terms
     */
    public function snippet(?string $text, array $terms): string
    {
        $text = trim((string) $text);
        if ($text === '') {
            return '';
        }

        $total = mb_strlen($text);
        $position = null;
        $matchLength = 0;

        foreach ($terms as $term) {
            $found = mb_stripos($text, $term);
            if ($found !== false && ($position === null || $found < $position)) {
                $position = $found;
                $matchLength = mb_strlen($term);
            }
        }

        if ($position === null) {
            return mb_substr($text, 0, 190).($total > 190 ? '…' : '');
        }

        $start = max(0, $position - self::SNIPPET_RADIUS);
        $end = min($total, $position + $matchLength + self::SNIPPET_RADIUS);

        // Jangan mulai/berhenti di tengah kata bila masih ada spasi terdekat.
        if ($start > 0) {
            $space = mb_strpos($text, ' ', $start);
            if ($space !== false && $space < $position) {
                $start = $space + 1;
            }
        }
        if ($end < $total) {
            $space = mb_strpos($text, ' ', $end);
            if ($space !== false && $space - $end <= 30) {
                $end = $space;
            }
        }

        $snippet = mb_substr($text, $start, $end - $start);

        if ($start > 0) {
            $snippet = '… '.$snippet;
        }
        if ($end < $total) {
            $snippet .= ' …';
        }

        return $snippet;
    }

    /**
     * Versi HTML snippet dengan kata kunci disorot. Teks di-escape sebelum
     * dibungkus <mark> agar aman dari XSS.
     *
     * @param  list<string>  $terms
     */
    public function toHtml(string $snippet, array $terms): string
    {
        if ($snippet === '') {
            return '';
        }
        if ($terms === []) {
            return e($snippet);
        }

        $terms = array_values(array_filter($terms, fn (string $term): bool => $term !== ''));
        if ($terms === []) {
            return e($snippet);
        }

        // Kata yang lebih panjang didahulukan agar "1074 K/PDT" tidak terpotong
        // menjadi kecocokan yang lebih pendek.
        usort($terms, fn (string $a, string $b): int => mb_strlen($b) <=> mb_strlen($a));

        $pattern = '/('.implode('|', array_map(fn (string $term): string => preg_quote($term, '/'), $terms)).')/iu';
        $parts = preg_split($pattern, $snippet, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) ?: [$snippet];

        $html = '';
        foreach ($parts as $part) {
            $html .= preg_match($pattern, $part) === 1
                ? '<mark>'.e($part).'</mark>'
                : e($part);
        }

        return $html;
    }
}
