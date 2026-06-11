<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$dirs = [
    'migrations' => 'database/migrations/',
    'models' => 'app/Models/',
    'controllers' => 'app/Http/Controllers/',
    'views' => 'resources/views/'
];

function getFiles($dir) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    $files = [];
    foreach ($iterator as $file) {
        if ($file->isFile() && in_array($file->getExtension(), ['php', 'blade.php'])) {
            $files[] = $file->getPathname();
        }
    }
    return $files;
}

$migrations = getFiles($dirs['migrations']);
$models = getFiles($dirs['models']);
$controllers = getFiles($dirs['controllers']);
$views = getFiles($dirs['views']);

$tables = [];

$dbTables = array_map('current', DB::select('SHOW TABLES'));

foreach ($dbTables as $table) {
    $columns = Schema::getColumnListing($table);
    $tables[$table] = ['migration_columns' => array_flip($columns), 'used_columns' => []];
}

$modelTables = [];
foreach ($models as $file) {
    $content = file_get_contents($file);
    preg_match('/class\s+([A-Za-z0-9_]+)/', $content, $m);
    if ($m) {
        $modelName = $m[1];
        $className = "App\\Models\\" . $modelName;
        if (class_exists($className)) {
            $instance = new $className;
            $table = $instance->getTable();
            $modelTables[$modelName] = $table;
            
            // Get fillables
            foreach ($instance->getFillable() as $fillable) {
                if (isset($tables[$table])) {
                    $tables[$table]['used_columns'][$fillable] = true;
                }
            }
            // Get casts
            foreach (array_keys($instance->getCasts()) as $cast) {
                if (isset($tables[$table])) {
                    $tables[$table]['used_columns'][$cast] = true;
                }
            }
        }
    }
}

// Map variables in controllers and views to likely tables based on variable name
$allFiles = array_merge($models, $controllers, $views);
$potential_columns = [];
foreach ($allFiles as $file) {
    $content = file_get_contents($file);
    
    // ->where('col'
    preg_match_all('/->(?:where|orWhere|whereNull|whereNotNull|whereIn|whereNotIn|orderBy)\s*\(\s*[\'"]([a-zA-Z0-9_]+)[\'"]/', $content, $matches);
    foreach ($matches[1] as $col) $potential_columns[$col] = true;
    
    // ->where('col',
    preg_match_all('/->where\s*\(\s*[\'"]([a-zA-Z0-9_]+)[\'"]\s*,/', $content, $matches);
    foreach ($matches[1] as $col) $potential_columns[$col] = true;

    // $model->col
    // We try to match variable name to a table. e.g., $order->status -> order -> orders
    preg_match_all('/\$([a-zA-Z0-9_]+)->([a-zA-Z0-9_]+)/', $content, $matches);
    foreach ($matches[1] as $i => $varName) {
        $col = $matches[2][$i];
        if (!in_array($col, ['id', 'created_at', 'updated_at', 'deleted_at', 'where', 'get', 'first', 'find', 'update', 'save', 'delete', 'with', 'count'])) {
            $potential_columns[$col] = true;
            
            // Try to guess table
            $guessedTable = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $varName)) . 's';
            if (substr($guessedTable, -2) === 'ys') $guessedTable = substr($guessedTable, 0, -2) . 'ies';
            
            if (isset($tables[$guessedTable])) {
                $tables[$guessedTable]['used_columns'][$col] = true;
            }
        }
    }
}

$output = [];
foreach ($tables as $table => $data) {
    $missing = [];
    $unused = [];
    foreach ($data['used_columns'] as $c => $v) {
        if (!isset($data['migration_columns'][$c])) {
            $missing[] = $c;
        }
    }
    
    foreach ($data['migration_columns'] as $c => $v) {
        if (!isset($data['used_columns'][$c]) && !in_array($c, ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token', 'password', 'email_verified_at', 'uuid'])) {
            $unused[] = $c;
        }
    }
    
    $output[$table] = [
        'missing' => array_unique($missing),
        'unused' => $unused,
        'migration_columns' => array_keys($data['migration_columns']),
        'used_columns' => array_keys($data['used_columns']),
    ];
}

file_put_contents('scan_result2.json', json_encode([
    'tables' => $output,
    'potential_columns' => array_keys($potential_columns),
    'modelTables' => $modelTables
], JSON_PRETTY_PRINT));
echo "Done\n";
