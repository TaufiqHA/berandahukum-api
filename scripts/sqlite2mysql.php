<?php

/**
 * Konversi database SQLite (skema legacy berandahukum + tabel tambahan
 * Laravel) menjadi dump MySQL siap impor lewat phpMyAdmin.
 *
 * Pemakaian:
 *   php scripts/sqlite2mysql.php database/database.sqlite berandah_compro.sql
 *
 * Skema MySQL mengikuti struktur database produksi `berandah_compro`
 * (nama tabel/kolom sama), ditambah:
 *   - PRIMARY KEY + AUTO_INCREMENT yang benar,
 *   - kolom baru app: tbl_ads.ads_category_id, tbl_sub_category.urutan,
 *     tbl_article_category.urutan, tbl_settings.tipe 'layout',
 *     tbl_banner.banner_type, tbl_banner.banner_content,
 *   - tabel app: migrations, tbl_api_token.
 */

$sqlitePath = $argv[1] ?? __DIR__.'/../database/database.sqlite';
$outPath = $argv[2] ?? __DIR__.'/../build/berandah_compro.sql';

if (! is_file($sqlitePath)) {
    fwrite(STDERR, "SQLite tidak ditemukan: {$sqlitePath}\n");
    exit(1);
}

@mkdir(dirname($outPath), 0755, true);

$pdo = new PDO('sqlite:'.$sqlitePath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/**
 * Definisi skema MySQL. Urutan kolom mengikuti produksi.
 * 'primary' & 'indexes' ditulis apa adanya (sudah berupa klausa MySQL).
 */
$schemas = [
    'settings' => [
        'columns' => [
            'id' => 'bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT',
            'cara_pembayaran' => 'longtext DEFAULT NULL',
            'created_at' => 'timestamp NULL DEFAULT NULL',
            'updated_at' => 'timestamp NULL DEFAULT NULL',
            'deleted_at' => 'timestamp NULL DEFAULT NULL',
        ],
        'primary' => ['id'],
    ],
    'sys_settings_m' => [
        'columns' => [
            'smtp_host' => "varchar(50) NOT NULL DEFAULT ''",
            'smtp_port' => 'varchar(10) NOT NULL',
            'smtp_username' => "varchar(50) NOT NULL DEFAULT ''",
            'smtp_password' => "varchar(100) NOT NULL DEFAULT ''",
            'smtp_secure' => 'varchar(5) DEFAULT NULL',
            'show_pertanyaan' => "enum('yes','no') NOT NULL DEFAULT 'yes'",
            'show_youtube' => "enum('yes','no') NOT NULL DEFAULT 'yes'",
            'mdb' => 'int(10) UNSIGNED DEFAULT NULL',
            'mdd' => 'datetime DEFAULT NULL',
        ],
    ],
    'tbl_ads' => [
        'columns' => [
            'ads_id' => 'int(11) NOT NULL AUTO_INCREMENT',
            'ads_position' => "int(11) NOT NULL COMMENT '0=top,1=bottom'",
            'ads_category_id' => 'int(11) DEFAULT NULL',
            'ads_type' => "int(11) NOT NULL COMMENT '0=upload,1=embed'",
            'ads_file_type' => "int(11) NOT NULL COMMENT '0=foto,1=video'",
            'ads_url' => 'text NOT NULL',
            'ads_link' => 'varchar(191) NOT NULL',
            'ads_status' => 'int(11) NOT NULL',
            'ads_created_date' => 'timestamp NOT NULL DEFAULT current_timestamp()',
        ],
        'primary' => ['ads_id'],
    ],
    'tbl_ads_pop' => [
        'columns' => [
            'ads_id' => 'int(11) NOT NULL AUTO_INCREMENT',
            'ads_position' => "int(11) NOT NULL COMMENT '0=top,1=bottom'",
            'ads_type' => "int(11) NOT NULL COMMENT '0=upload,1=embed'",
            'ads_file_type' => "int(11) NOT NULL COMMENT '0=foto,1=video'",
            'ads_url' => 'text NOT NULL',
            'ads_link' => 'text NOT NULL',
            'ads_content' => 'text DEFAULT NULL',
            'ads_status' => "varchar(10) NOT NULL",
            'ads_created_date' => 'timestamp NOT NULL DEFAULT current_timestamp()',
        ],
        'primary' => ['ads_id'],
    ],
    'tbl_article' => [
        'columns' => [
            'article_id' => 'int(11) NOT NULL AUTO_INCREMENT',
            'headline_news' => 'int(11) NOT NULL DEFAULT 0',
            'label_id' => 'int(11) NOT NULL DEFAULT 0',
            // Tanpa ON UPDATE: jangan biarkan MySQL menimpa tanggal artikel
            // setiap kali baris di-update (mis. view count, publish/draft).
            'article_date' => 'datetime NOT NULL DEFAULT current_timestamp()',
            'article_uri' => 'varchar(100) NOT NULL',
            'article_title' => 'varchar(100) NOT NULL',
            'article_author' => 'varchar(255) NOT NULL',
            'article_content' => 'mediumtext NOT NULL',
            'article_img' => 'varchar(100) DEFAULT NULL',
            'article_pdf' => 'varchar(255) DEFAULT NULL',
            'article_status' => 'tinyint(4) NOT NULL',
            'article_views' => 'int(11) NOT NULL',
            'article_created_by' => 'int(11) NOT NULL',
            'article_created_date' => 'timestamp NULL DEFAULT current_timestamp()',
            'create_date' => 'date DEFAULT NULL',
        ],
        'primary' => ['article_id'],
    ],
    'tbl_article_category' => [
        'columns' => [
            'article_id' => 'int(11) NOT NULL',
            'category_id' => 'int(11) NOT NULL',
            'sub_category_id' => 'int(11) NOT NULL',
            'created_date' => 'datetime DEFAULT NULL',
            'urutan' => 'int(11) NOT NULL DEFAULT 0',
        ],
        'primary' => ['article_id', 'category_id', 'sub_category_id'],
    ],
    'tbl_banner' => [
        'columns' => [
            'id_banner' => 'int(10) UNSIGNED NOT NULL AUTO_INCREMENT',
            'nama_banner' => 'varchar(255) DEFAULT NULL',
            'file_banner' => 'varchar(255) DEFAULT NULL',
            'link_url' => 'text DEFAULT NULL',
            'status' => "enum('yes','no') DEFAULT 'yes'",
            'urutan' => 'tinyint(4) DEFAULT NULL',
            'banner_type' => 'int(11) NOT NULL DEFAULT 0',
            'banner_content' => 'longtext DEFAULT NULL',
        ],
        'primary' => ['id_banner'],
    ],
    'tbl_category' => [
        'columns' => [
            'category_id' => 'int(11) NOT NULL AUTO_INCREMENT',
            'category_uri' => 'varchar(100) NOT NULL',
            'category_name' => 'varchar(100) NOT NULL',
            'category_show' => "enum('yes','no') NOT NULL DEFAULT 'yes'",
            'urutan' => 'int(11) DEFAULT 0',
        ],
        'primary' => ['category_id'],
        'indexes' => ['UNIQUE KEY `category_uri` (`category_uri`)'],
    ],
    'tbl_comment' => [
        'columns' => [
            'comment_id' => 'int(11) NOT NULL AUTO_INCREMENT',
            'article_id' => 'int(11) NOT NULL',
            'comment_date' => 'timestamp NOT NULL DEFAULT current_timestamp()',
            'comment_name' => 'varchar(50) NOT NULL',
            'comment_fill' => 'text NOT NULL',
            'comment_status' => 'tinyint(4) NOT NULL',
            'comment_reply' => 'text DEFAULT NULL',
        ],
        'primary' => ['comment_id'],
    ],
    'tbl_contact' => [
        'columns' => [
            'contact_id' => 'int(11) NOT NULL AUTO_INCREMENT',
            'contact_name' => 'varchar(50) NOT NULL',
            'contact_email' => 'varchar(50) NOT NULL',
            'contact_hp' => 'varchar(20) NOT NULL',
            'contact_desc' => 'varchar(500) NOT NULL',
            'contact_created_date' => 'timestamp NOT NULL DEFAULT current_timestamp()',
        ],
        'primary' => ['contact_id'],
    ],
    'tbl_label' => [
        'columns' => [
            'label_id' => 'int(11) NOT NULL AUTO_INCREMENT',
            'label_uri' => 'varchar(50) NOT NULL',
            'label_name' => 'varchar(50) NOT NULL',
            'label_show' => "enum('yes','no') NOT NULL DEFAULT 'no'",
            'urutan' => 'int(10) UNSIGNED NOT NULL DEFAULT 0',
        ],
        'primary' => ['label_id'],
    ],
    'tbl_menu' => [
        'columns' => [
            'id' => 'int(10) UNSIGNED NOT NULL AUTO_INCREMENT',
            'tipe' => "enum('kategori','subkategori','label','artikel') DEFAULT NULL",
            'id_menu' => 'int(11) DEFAULT NULL',
            'uri_menu' => 'varchar(255) DEFAULT NULL',
            'urutan' => 'tinyint(4) DEFAULT NULL',
        ],
        'primary' => ['id'],
    ],
    'tbl_pertanyaan' => [
        'columns' => [
            'pertanyaan_id' => 'int(11) NOT NULL AUTO_INCREMENT',
            'pertanyaan_date' => 'datetime NOT NULL',
            'pertanyaan_nama' => 'varchar(150) NOT NULL',
            'pertanyaan_email' => 'varchar(150) NOT NULL',
            'pertanyaan' => 'text NOT NULL',
            'pertanyaan_status' => 'tinyint(4) NOT NULL',
            'pertanyaan_jawaban' => 'text DEFAULT NULL',
        ],
        'primary' => ['pertanyaan_id'],
    ],
    'tbl_pilihan' => [
        'columns' => [
            'id' => 'int(10) UNSIGNED NOT NULL AUTO_INCREMENT',
            'article_id' => 'int(11) NOT NULL',
            'posisi' => "enum('atas','bawah','top') DEFAULT NULL",
            'urutan' => 'int(11) DEFAULT NULL',
        ],
        'primary' => ['id'],
    ],
    'tbl_quotes' => [
        'columns' => [
            'quote_id' => 'int(11) NOT NULL AUTO_INCREMENT',
            'quote_image' => "tinytext NOT NULL COMMENT '0=foto,1=video'",
            'quote_status' => 'int(11) NOT NULL',
            'urutan' => 'int(11) DEFAULT NULL',
        ],
        'primary' => ['quote_id'],
    ],
    'tbl_referensi' => [
        'columns' => [
            'id' => 'int(10) UNSIGNED NOT NULL AUTO_INCREMENT',
            'sumber' => "enum('int','ext') NOT NULL DEFAULT 'int'",
            'article_ref' => 'int(11) DEFAULT NULL',
            'article_id' => 'int(11) DEFAULT NULL',
            'judul' => 'text DEFAULT NULL',
            'link' => 'text DEFAULT NULL',
            'urutan' => 'tinyint(4) DEFAULT NULL',
        ],
        'primary' => ['id'],
    ],
    'tbl_settings' => [
        'columns' => [
            'setting_id' => 'int(11) NOT NULL AUTO_INCREMENT',
            'setting_name' => 'varchar(50) NOT NULL',
            'setting_content' => 'mediumtext NOT NULL',
            'setting_last_updated' => 'timestamp NOT NULL DEFAULT current_timestamp()',
            'tipe' => "enum('sosial','info','layout') DEFAULT 'info'",
            'urutan' => 'tinyint(4) DEFAULT NULL',
        ],
        'primary' => ['setting_id'],
    ],
    'tbl_sub_category' => [
        'columns' => [
            'sub_category_id' => 'int(11) NOT NULL AUTO_INCREMENT',
            'category_id' => 'int(11) NOT NULL',
            'sub_category_uri' => 'varchar(100) NOT NULL',
            'sub_category_name' => 'varchar(100) NOT NULL',
            'sub_category_show' => "enum('yes','no') NOT NULL DEFAULT 'yes'",
            'create_date' => 'date DEFAULT NULL',
            'urutan' => 'int(11) NOT NULL DEFAULT 0',
        ],
        'primary' => ['sub_category_id'],
        'indexes' => ['UNIQUE KEY `sub_category_uri` (`sub_category_uri`)'],
    ],
    'tbl_user' => [
        'columns' => [
            'user_id' => 'int(11) NOT NULL AUTO_INCREMENT',
            'user_name' => 'varchar(100) NOT NULL',
            'user_email' => 'varchar(100) NOT NULL',
            'user_password' => 'varchar(100) NOT NULL',
            'user_last_login' => 'timestamp NULL DEFAULT NULL',
            'user_level' => "enum('admin','penulis') DEFAULT NULL",
        ],
        'primary' => ['user_id'],
        'indexes' => ['UNIQUE KEY `admin_email` (`user_email`)'],
    ],
    'migrations' => [
        'columns' => [
            'id' => 'bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT',
            'migration' => 'varchar(255) NOT NULL',
            'batch' => 'int(11) NOT NULL',
        ],
        'primary' => ['id'],
    ],
    'tbl_api_token' => [
        'columns' => [
            'id' => 'bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT',
            'user_id' => 'int(10) UNSIGNED NOT NULL',
            'token' => 'varchar(80) NOT NULL',
            'created_at' => 'timestamp NULL DEFAULT NULL',
            'last_used_at' => 'timestamp NULL DEFAULT NULL',
        ],
        'primary' => ['id'],
        'indexes' => [
            'UNIQUE KEY `tbl_api_token_token_unique` (`token`)',
            'KEY `tbl_api_token_user_id_index` (`user_id`)',
        ],
    ],
];

function ident(string $n): string
{
    return '`'.str_replace('`', '``', $n).'`';
}

/** Literal string gaya MySQL (aman terhadap backslash, kutip, newline, dsb). */
function mysqlString(string $v): string
{
    return "'".str_replace(
        ["\\", "\0", "\n", "\r", "'", '"', "\x1a"],
        ["\\\\", "\\0", "\\n", "\\r", "\\'", '\\"', "\\Z"],
        $v
    )."'";
}

/** Jenis nilai untuk formatting: int | float | date | datetime | str. */
function valueKind(string $definition): string
{
    $d = strtolower($definition);
    if (str_contains($d, 'int')) {
        return 'int';
    }
    if (str_contains($d, 'double') || str_contains($d, 'float') || str_contains($d, 'decimal')) {
        return 'float';
    }
    if (str_starts_with($d, 'date ')) {
        return 'date';
    }
    if (str_contains($d, 'datetime') || str_contains($d, 'timestamp')) {
        return 'datetime';
    }

    return 'str';
}

$out = [];
$out[] = '-- Beranda Hukum — dump MySQL';
$out[] = '-- Sumber: database/database.sqlite (data produksi).';
$out[] = '-- Skema disesuaikan ke format MySQL (PRIMARY KEY + AUTO_INCREMENT).';
$out[] = '';
$out[] = 'SET NAMES utf8mb4;';
$out[] = 'SET FOREIGN_KEY_CHECKS = 0;';
$out[] = "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';";
$out[] = 'SET time_zone = "+00:00";';
$out[] = 'START TRANSACTION;';
$out[] = '';

$summary = [];

foreach ($schemas as $table => $schema) {
    $sqliteCols = $pdo->query('PRAGMA table_info('.ident($table).')')->fetchAll(PDO::FETCH_COLUMN, 1);
    if ($sqliteCols === []) {
        fwrite(STDERR, "Lewati (tidak ada di SQLite): {$table}\n");
        continue;
    }

    // Pastikan kolom SQLite selaras dengan skema MySQL.
    $missing = array_diff($sqliteCols, array_keys($schema['columns']));
    if ($missing !== []) {
        fwrite(STDERR, "PERINGATAN: kolom tak terpetakan di {$table}: ".implode(', ', $missing)."\n");
    }

    $definitions = [];
    foreach ($schema['columns'] as $col => $def) {
        $definitions[] = '  '.ident($col).' '.$def;
    }
    if (! empty($schema['primary'])) {
        $definitions[] = '  PRIMARY KEY ('.implode(', ', array_map('ident', $schema['primary'])).')';
    }
    foreach ($schema['indexes'] ?? [] as $idx) {
        $definitions[] = '  '.$idx;
    }

    $out[] = '-- --------------------------------------------------------';
    $out[] = '-- Tabel `'.$table.'`';
    $out[] = '-- --------------------------------------------------------';
    $out[] = '';
    $out[] = 'DROP TABLE IF EXISTS '.ident($table).';';
    $out[] = 'CREATE TABLE '.ident($table).' (';
    $out[] = implode(",\n", $definitions);
    $out[] = ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';
    $out[] = '';

    $rowCount = (int) $pdo->query('select count(*) from '.ident($table))->fetchColumn();
    $summary[$table] = $rowCount;

    if ($rowCount === 0) {
        continue;
    }

    $colList = implode(', ', array_map('ident', $sqliteCols));
    $kinds = [];
    foreach ($sqliteCols as $col) {
        $kinds[$col] = valueKind($schema['columns'][$col] ?? 'text');
    }

    $batch = [];
    $flush = function () use (&$out, &$batch, $table, $colList) {
        if ($batch === []) {
            return;
        }
        $out[] = 'INSERT INTO '.ident($table).' ('.$colList.') VALUES';
        $out[] = implode(",\n", $batch).';';
        $out[] = '';
        $batch = [];
    };

    $stmt = $pdo->query('select '.$colList.' from '.ident($table));
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $vals = [];
        foreach ($row as $i => $v) {
            $col = $sqliteCols[$i];
            $kind = $kinds[$col];
            if ($v === null) {
                $vals[] = 'NULL';
            } elseif ($kind === 'int') {
                $vals[] = is_numeric($v) ? (string) (0 + $v) : mysqlString((string) $v);
            } elseif ($kind === 'float') {
                $vals[] = is_numeric($v) ? (string) (0 + $v) : mysqlString((string) $v);
            } elseif ($kind === 'date') {
                $vals[] = mysqlString(substr((string) $v, 0, 10));
            } else {
                $vals[] = mysqlString((string) $v);
            }
        }
        $batch[] = '('.implode(', ', $vals).')';
        if (count($batch) >= 100) {
            $flush();
        }
    }
    $flush();
}

$out[] = 'COMMIT;';
$out[] = 'SET FOREIGN_KEY_CHECKS = 1;';
$out[] = '';

file_put_contents($outPath, implode("\n", $out));

echo 'Tabel: '.count($summary)."\n";
foreach ($summary as $t => $n) {
    echo '  '.str_pad($t, 24).$n." baris\n";
}
echo 'Output: '.$outPath.' ('.number_format(filesize($outPath) / 1048576, 2)." MB)\n";
