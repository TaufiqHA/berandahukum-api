<?php

namespace App\Support;

/**
 * Mengubah HTML artikel menjadi teks biasa yang dipakai untuk pencarian isi.
 *
 * Kolom `article_content` menyimpan HTML impor yang panjang. Mencari langsung
 * pada HTML membuat kata di dalam nama tag/atribut (mis. "style", "width")
 * ikut cocok, sehingga perlu versi teks bersih tanpa markup.
 */
final class ArticleSearchText
{
    public static function fromHtml(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        // Isi <script>/<style> bukan konten yang terlihat, jadi dibuang lebih
        // dulu sebelum strip_tags (strip_tags hanya membuang tag, bukan isinya).
        $text = preg_replace('#<(script|style)\b[^>]*>.*?</\1\s*>#is', ' ', $html) ?? $html;
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Spasi non-breaking dan karakter tak terlihat dinormalisasi agar kata
        // yang dipecah markup tetap bisa dicari sebagai satu frasa.
        $text = str_replace(["\u{00A0}", "\u{200B}", "\u{FEFF}"], ' ', $text);
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim((string) $text);
    }
}
