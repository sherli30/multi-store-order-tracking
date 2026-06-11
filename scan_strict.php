<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$dirs = ['models' => 'app/Models/', 'controllers' => 'app/Http/Controllers/', 'views' => 'resources/views/'];

function getFiles($dir) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    $files = [];
    foreach ($iterator as $file) {
        if ($file->isFile() && in_array($file->getExtension(), ['php', 'php'])) {
            $files[] = $file->getPathname();
        }
    }
    return $files;
}

$dbTables = array_map('current', DB::select('SHOW TABLES'));
$columnsByTable = [];
foreach ($dbTables as $table) {
    $columnsByTable[$table] = Schema::getColumnListing($table);
}

$missing = [];
$unused = [];
$usedByTable = [];

// Initialize used tracking
foreach ($dbTables as $table) {
    $usedByTable[$table] = [];
}

// Check fillable and casts
$models = getFiles($dirs['models']);
foreach ($models as $file) {
    $content = file_get_contents($file);
    preg_match('/class\s+([A-Za-z0-9_]+)/', $content, $m);
    if ($m) {
        $className = "App\\Models\\" . $m[1];
        if (class_exists($className)) {
            $instance = new $className;
            $table = $instance->getTable();
            if (isset($columnsByTable[$table])) {
                foreach ($instance->getFillable() as $col) {
                    $usedByTable[$table][$col] = true;
                    if (!in_array($col, $columnsByTable[$table])) {
                        $missing[$table][$col] = ['type' => 'fillable'];
                    }
                }
                foreach (array_keys($instance->getCasts()) as $col) {
                    $usedByTable[$table][$col] = true;
                    if (!in_array($col, $columnsByTable[$table]) && !isset($missing[$table][$col])) {
                        $missing[$table][$col] = ['type' => 'casts'];
                    }
                }
            }
        }
    }
}

// Regex to find ->where('col') and $model->col
$allFiles = array_merge($models, getFiles($dirs['controllers']), getFiles($dirs['views']));

// Map variable names to tables (simple heuristic)
$varToTable = [];
foreach ($models as $file) {
    preg_match('/class\s+([A-Za-z0-9_]+)/', file_get_contents($file), $m);
    if ($m) {
        $className = "App\\Models\\" . $m[1];
        if (class_exists($className)) {
            $instance = new $className;
            $table = $instance->getTable();
            $varName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $m[1]));
            $varToTable[$varName] = $table;
        }
    }
}

$accessorsByTable = [];
foreach ($models as $file) {
    $content = file_get_contents($file);
    preg_match('/class\s+([A-Za-z0-9_]+)/', $content, $m);
    if ($m) {
        $className = "App\\Models\\" . $m[1];
        if (class_exists($className)) {
            $instance = new $className;
            $table = $instance->getTable();
            
            // Get all methods to find accessors
            $methods = get_class_methods($instance);
            foreach ($methods as $method) {
                if (preg_match('/^get(.*)Attribute$/', $method, $match)) {
                    $attr = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $match[1]));
                    $accessorsByTable[$table][] = $attr;
                }
            }
        }
    }
}

foreach ($allFiles as $file) {
    $content = file_get_contents($file);
    
    // ->where('col'
    preg_match_all('/->(?:where|orWhere|whereNull|whereNotNull|whereIn|whereNotIn|orderBy)\s*\(\s*[\'"]([a-zA-Z0-9_]+)[\'"]/', $content, $matches);
    foreach ($matches[1] as $col) {
        // Can't easily tie to a table, so we check if this col exists in ANY table's missing or unused lists later
    }
    
    // $var->col
    preg_match_all('/\$([a-zA-Z0-9_]+)->([a-zA-Z0-9_]+)/', $content, $matches);
    foreach ($matches[1] as $i => $varName) {
        $col = $matches[2][$i];
        if (isset($varToTable[$varName])) {
            $table = $varToTable[$varName];
            
            // Ignore methods and relationships
            if (in_array($col, ['id', 'created_at', 'updated_at', 'deleted_at', 'where', 'get', 'first', 'find', 'update', 'save', 'delete', 'with', 'count'])) continue;
            
            $usedByTable[$table][$col] = true;
            
            $isAccessor = isset($accessorsByTable[$table]) && in_array($col, $accessorsByTable[$table]);
            $isRelation = method_exists("App\\Models\\" . str_replace(' ', '', ucwords(str_replace('_', ' ', $varName))), $col);
            
            if (!$isAccessor && !$isRelation && !in_array($col, $columnsByTable[$table])) {
                if (!isset($missing[$table][$col])) {
                    $missing[$table][$col] = ['type' => 'property_access', 'file' => $file];
                }
            }
        }
    }
}

foreach ($dbTables as $table) {
    foreach ($columnsByTable[$table] as $col) {
        if (!isset($usedByTable[$table][$col]) && !in_array($col, ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token', 'password', 'email_verified_at', 'uuid'])) {
            $unused[$table][] = $col;
        }
    }
}

echo "=== MISSING COLUMNS ===\n";
print_r($missing);
echo "\n=== UNUSED COLUMNS ===\n";
print_r($unused);
