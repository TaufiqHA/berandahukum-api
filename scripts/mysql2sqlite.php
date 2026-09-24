<?php
/**
 * Konverter MariaDB -> SQLite untuk berandahukum.
 * - Introspeksi skema (SHOW FULL COLUMNS + SHOW INDEX) lalu buat CREATE TABLE SQLite.
 * - Salin data baris-per-baris via prepared statement (aman untuk encoding/escape).
 * Output: file SQLite target + schema.sql (untuk migrasi/reprodusibilitas).
 *
 * Jalankan: PHP_INI_SCAN_DIR=~/php-ini php convert.php
 */

$socket = '/tmp/mariadb-beranda.sock';
$dbname = 'berandah_compro';
$sqlitePath = $argv[1] ?? '/tmp/opencode/berandah_compro.sqlite';
$schemaPath = $argv[2] ?? '/tmp/opencode/schema.sql';

$mysql = new PDO("mysql:unix_socket={$socket};dbname={$dbname};charset=utf8mb4", 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

if (file_exists($sqlitePath)) {
    unlink($sqlitePath);
}
$sqlite = new PDO("sqlite:{$sqlitePath}", null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
$sqlite->exec('PRAGMA foreign_keys = OFF;');
$sqlite->exec('PRAGMA journal_mode = MEMORY;');

function sqliteType(string $mysqlType): string
{
    $t = strtolower($mysqlType);
    if (preg_match('/^(tiny|small|medium|big)?int/', $t)) {
        return 'INTEGER';
    }
    if (preg_match('/^(decimal|numeric|float|double|real)/', $t)) {
        return 'NUMERIC';
    }
    if (str_contains($t, 'blob') || str_contains($t, 'binary')) {
        return 'BLOB';
    }

    return 'TEXT'; // varchar/text/enum/set/date/datetime/timestamp/time/json
}

function quoteIdent(string $name): string
{
    return '"'.str_replace('"', '""', $name).'"';
}

$tables = $mysql->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
$schemaSql = "PRAGMA foreign_keys = OFF;\n\n";
$summary = [];

foreach ($tables as $table) {
    $columns = $mysql->query('SHOW FULL COLUMNS FROM `'.$table.'`')->fetchAll();

    // deteksi primary key
    $pk = [];
    foreach ($columns as $c) {
        if (($c['Key'] ?? '') === 'PRI') {
            $pk[] = $c['Field'];
        }
    }
    $singleIntPk = count($pk) === 1 && preg_match('/int/i', $columns[array_search($pk[0], array_column($columns, 'Field'), true)]['Type']);

    $defs = [];
    foreach ($columns as $c) {
        $field = $c['Field'];
        $type = sqliteType($c['Type']);
        $null = ($c['Null'] === 'YES');
        $default = $c['Default'];
        $extra = strtolower($c['Extra'] ?? '');

        // INTEGER PRIMARY KEY AUTOINCREMENT untuk PK integer tunggal
        if ($singleIntPk && $field === $pk[0]) {
            $defs[] = quoteIdent($field).' INTEGER PRIMARY KEY AUTOINCREMENT';
            continue;
        }

        $def = quoteIdent($field).' '.$type;
        if (! $null) {
            $def .= ' NOT NULL';
        }
        if ($default !== null && ! str_contains($extra, 'auto_increment')) {
            if (str_contains(strtolower((string) $default), 'current_timestamp')) {
                $def .= ' DEFAULT CURRENT_TIMESTAMP';
            } elseif (is_numeric($default)) {
                $def .= ' DEFAULT '.$default;
            } else {
                $def .= " DEFAULT '".str_replace("'", "''", (string) $default)."'";
            }
        }
        $defs[] = $def;
    }

    if (! $singleIntPk && $pk) {
        $defs[] = 'PRIMARY KEY ('.implode(', ', array_map('quoteIdent', $pk)).')';
    }

    $create = 'CREATE TABLE IF NOT EXISTS '.quoteIdent($table).' ('.implode(',', $defs).');';
    $sqlite->exec($create);
    $schemaSql .= $create."\n";

    // indeks (selain PRIMARY)
    $indexes = $mysql->query('SHOW INDEX FROM `'.$table.'`')->fetchAll();
    $grouped = [];
    foreach ($indexes as $idx) {
        if ($idx['Key_name'] === 'PRIMARY') {
            continue;
        }
        $grouped[$idx['Key_name']]['unique'] = ((int) $idx['Non_unique'] === 0);
        $grouped[$idx['Key_name']]['cols'][] = $idx['Column_name'];
    }
    foreach ($grouped as $name => $info) {
        $idxName = $table.'_'.$name;
        $sql = 'CREATE '.($info['unique'] ? 'UNIQUE ' : '').'INDEX IF NOT EXISTS '.quoteIdent($idxName)
            .' ON '.quoteIdent($table).' ('.implode(', ', array_map('quoteIdent', $info['cols'])).');';
        $sqlite->exec($sql);
        $schemaSql .= $sql."\n";
    }

    // salin data
    $rows = $mysql->query('SELECT * FROM `'.$table.'`')->fetchAll();
    if ($rows) {
        $cols = array_keys($rows[0]);
        $colList = implode(', ', array_map('quoteIdent', $cols));
        $placeholders = implode(', ', array_fill(0, count($cols), '?'));
        $stmt = $sqlite->prepare('INSERT INTO '.quoteIdent($table).' ('.$colList.') VALUES ('.$placeholders.')');
        $sqlite->beginTransaction();
        foreach ($rows as $r) {
            $vals = [];
            foreach ($cols as $col) {
                $vals[] = $r[$col];
            }
            $stmt->execute($vals);
        }
        $sqlite->commit();
    }
    $summary[] = sprintf('%-24s %d baris', $table, count($rows));
}

file_put_contents($schemaPath, $schemaSql);
echo implode("\n", $summary)."\n";
echo "SQLite: {$sqlitePath}\nSchema: {$schemaPath}\n";
