<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$dbTables = array_map('current', DB::select('SHOW TABLES'));

$violations = [];

// Get all foreign keys for all tables
$foreignKeys = [];
foreach ($dbTables as $table) {
    try {
        $keys = DB::select("SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL", [$table]);
        foreach ($keys as $k) {
            $foreignKeys[$table][$k->COLUMN_NAME] = true;
        }
    } catch (\Exception $e) {}
}

foreach ($dbTables as $table) {
    if (in_array($table, ['migrations', 'personal_access_tokens', 'password_reset_tokens', 'failed_jobs', 'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches'])) continue;

    $columns = DB::select("SHOW COLUMNS FROM {$table}");
    
    foreach ($columns as $col) {
        $name = $col->Field;
        $type = strtolower($col->Type);
        $nullable = strtoupper($col->Null) === 'YES';
        $key = strtoupper($col->Key); // PRI, UNI, MUL
        
        // 1. Foreign keys
        if (preg_match('/_id$/', $name) && $name !== 'midtrans_order_id' && $name !== 'transaction_id' && $name !== 'reference_id') {
            if (!isset($foreignKeys[$table][$name])) {
                $violations[] = "Table $table column $name is missing foreign key constraint.";
            }
        }
        
        // 2. Money columns use decimal
        if (preg_match('/price|cost|amount|revenue/i', $name)) {
            if (strpos($type, 'float') !== false || strpos($type, 'double') !== false) {
                $violations[] = "Table $table column $name uses float/double instead of decimal.";
            }
        }
        
        // 3. booleans use tinyint(1)
        if (preg_match('/^is_|^has_/', $name)) {
            if (strpos($type, 'tinyint(1)') === false && strpos($type, 'boolean') === false) {
                $violations[] = "Table $table column $name should be tinyint(1)/boolean, got $type.";
            }
        }
        
        // 4. identifiers like order_number, transaction_code
        if (preg_match('/_number$|_code$|_id$/', $name) && !preg_match('/_id$/', $name) || in_array($name, ['order_number', 'transaction_code', 'transaction_id', 'midtrans_order_id', 'idempotency_key'])) {
            // Must be unique()->nullable()
            if ($name !== 'midtrans_order_id' && $name !== 'transaction_id' && $name !== 'idempotency_key') {
                 // Check if it's unique
                 if ($key !== 'UNI' && $key !== 'PRI') {
                     $violations[] = "Table $table column $name should be unique.";
                 }
                 if (!$nullable) {
                     $violations[] = "Table $table column $name should be nullable.";
                 }
            } else {
                 if ($name === 'transaction_id' && $table === 'transactions') {
                     if ($key !== 'UNI') $violations[] = "Table $table column $name should be unique.";
                     if (!$nullable) $violations[] = "Table $table column $name should be nullable.";
                 }
                 if ($name === 'midtrans_order_id') {
                     if ($key !== 'UNI') $violations[] = "Table $table column $name should be unique.";
                     if (!$nullable) $violations[] = "Table $table column $name should be nullable.";
                 }
                 if ($name === 'idempotency_key') {
                     if ($key !== 'UNI') $violations[] = "Table $table column $name should be unique.";
                     if (!$nullable) $violations[] = "Table $table column $name should be nullable.";
                 }
            }
        }
    }
}

echo implode("\n", $violations);
