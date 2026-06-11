<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$dbTables = array_map('current', DB::select('SHOW TABLES'));

foreach ($dbTables as $table) {
    if (in_array($table, ['migrations', 'personal_access_tokens', 'password_reset_tokens', 'failed_jobs', 'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches'])) continue;
    $foreignKeys = [];
    $keys = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL", [$table]);
    foreach ($keys as $k) {
        $foreignKeys[$k->COLUMN_NAME] = true;
    }
    
    $columns = Schema::getColumnListing($table);
    foreach ($columns as $col) {
        if (preg_match('/_id$/', $col) && !isset($foreignKeys[$col])) {
            echo "Missing FK: $table.$col\n";
        }
    }
}
