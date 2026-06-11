<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$tables = Schema::getTableListing();
foreach ($tables as $table) {
    if (in_array($table, ['migrations', 'personal_access_tokens', 'failed_jobs', 'password_reset_tokens'])) continue;
    echo "TABLE: $table\n";
    foreach (Schema::getColumns($table) as $col) {
        echo "  {$col['name']} ({$col['type_name']}) nullable: " . ($col['nullable'] ? 'yes' : 'no') . "\n";
    }
}
