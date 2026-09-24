<?php

use Illuminate\Support\Facades\Auth;

/*
 * Helper kompatibilitas dengan aplikasi CodeIgniter lama, agar view/fungsi
 * bisa dipakai ulang dengan perubahan minimal di Laravel.
 */

if (! function_exists('base_url')) {
    function base_url(string $path = ''): string
    {
        return url($path);
    }
}

if (! function_exists('site_url')) {
    function site_url(string $path = ''): string
    {
        return url($path);
    }
}

if (! function_exists('site_admin')) {
    function site_admin(string $path = ''): string
    {
        $base = url('admin');

        return $path === '' ? $base : $base.'/'.ltrim($path, '/');
    }
}

if (! function_exists('config_item')) {
    function config_item(string $key)
    {
        return config($key);
    }
}

if (! function_exists('kode_unik')) {
    function kode_unik(): string
    {
        return base_convert((string) microtime(false), 10, 36);
    }
}

if (! function_exists('tanggal')) {
    function tanggal($value, string $format = 'Y-m-d'): string
    {
        if (empty($value)) {
            return '';
        }

        try {
            return \Illuminate\Support\Carbon::parse($value)->format($format);
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }
}

if (! function_exists('short_name')) {
    function short_name($str, int $limit): string
    {
        if ($limit < 3) {
            $limit = 3;
        }

        return strlen($str) > $limit ? substr($str, 0, $limit - 3).'...' : $str;
    }
}

if (! function_exists('error_message')) {
    function error_message($msg, bool $remove = true): string
    {
        return '<div class="alert alert-danger alert-dismissible">'
            .'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'
            .$msg.'</div>';
    }
}

if (! function_exists('success_message')) {
    function success_message($msg, bool $remove = true): string
    {
        return '<div class="alert alert-success alert-dismissible">'
            .'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'
            .$msg.'</div>';
    }
}

if (! function_exists('format_tanggal')) {
    function format_tanggal($tgl, string $lang = 'in'): string
    {
        if (empty($tgl)) {
            return '-';
        }

        $bulan = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];

        $split = explode(' ', $tgl);
        $date = explode('-', $split[0]);
        if (count($date) < 3 || $date[2] === '00') {
            return '-';
        }

        $bulanNama = $bulan[$date[1]] ?? $date[1];

        if (isset($split[1])) {
            $jam = explode(':', $split[1]);
            if (isset($jam[0], $jam[1])) {
                return $date[2].' '.$bulanNama.' '.$date[0].' | '.$jam[0].':'.$jam[1];
            }
        }

        return $date[2].' '.$bulanNama.' '.$date[0];
    }
}

if (! function_exists('admin_user')) {
    function admin_user()
    {
        return Auth::user();
    }
}

if (! function_exists('article_image')) {
    /**
     * URL gambar artikel dengan fallback ramah (tanpa gambar rusak).
     */
    function article_image(?string $img): string
    {
        if (! empty($img) && is_file(public_path('uploads/img/'.$img))) {
            return url('uploads/img/'.$img);
        }

        if (is_file(public_path('uploads/img/beranda_hukum_square.jpg'))) {
            return url('uploads/img/beranda_hukum_square.jpg');
        }

        return url('img/placeholder.svg');
    }
}
