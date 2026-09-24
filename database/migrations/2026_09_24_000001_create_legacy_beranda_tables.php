<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Skema legacy berandahukum (nama tabel & kolom sama persis dengan database
 * produksi `berandah_compro`). DDL SQLite ada di database/schema.sql, hasil
 * introspeksi MariaDB (lihat scripts/mysql2sqlite.php).
 *
 * Catatan: database/database.sqlite sudah berisi data produksi; migrasi ini
 * hanya untuk mereproduksi skema pada database kosong (mis. migrate:fresh).
 */
return new class extends Migration
{
    public function up(): void
    {
        $schema = file_get_contents(database_path('schema.sql'));

        if ($schema !== false) {
            DB::unprepared($schema);
        }
    }

    public function down(): void
    {
        $tables = DB::select(
            "select name from sqlite_master where type = 'table' and name not in ('migrations', 'sqlite_sequence')"
        );

        DB::statement('PRAGMA foreign_keys = OFF');
        foreach ($tables as $table) {
            Schema::dropIfExists($table->name);
        }
        DB::statement('PRAGMA foreign_keys = ON');
    }
};
