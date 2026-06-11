<?php

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
        if ($file->isFile() && in_array($file->getExtension(), ['php'])) {
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

// Parse migrations to find tables and columns
foreach ($migrations as $file) {
    $content = file_get_contents($file);
    // Find Schema::create('table'...) or Schema::table('table'...)
    preg_match_all('/Schema::(?:create|table)\s*\(\s*\'([^\']+)\'/', $content, $matches);
    foreach ($matches[1] as $table) {
        if (!isset($tables[$table])) {
            $tables[$table] = ['migration_columns' => [], 'used_columns' => []];
        }
    }
}

foreach ($migrations as $file) {
    $content = file_get_contents($file);
    preg_match_all('/Schema::(?:create|table)\s*\(\s*\'([^\']+)\'(.*?)(?=Schema::|\z)/s', $content, $blocks);
    foreach ($blocks[1] as $i => $table) {
        $block = $blocks[2][$i];
        preg_match_all('/\$table->(?:string|integer|text|boolean|decimal|float|double|date|dateTime|timestamp|json|enum|id|uuid|bigInteger|tinyInteger|foreignId)\s*\(\s*\'([^\']+)\'/', $block, $cols);
        foreach ($cols[1] as $col) {
            $tables[$table]['migration_columns'][$col] = true;
        }
        if (strpos($block, '$table->timestamps()') !== false) {
            $tables[$table]['migration_columns']['created_at'] = true;
            $tables[$table]['migration_columns']['updated_at'] = true;
        }
        if (strpos($block, '$table->softDeletes()') !== false) {
            $tables[$table]['migration_columns']['deleted_at'] = true;
        }
        if (strpos($block, '$table->id()') !== false) {
            $tables[$table]['migration_columns']['id'] = true;
        }
        if (strpos($block, '$table->uuid(') !== false && preg_match('/\$table->uuid\s*\(\s*\'([^\']+)\'/', $block) === 0) {
             // default uuid usually 'id' or 'uuid'
             $tables[$table]['migration_columns']['uuid'] = true;
        }
    }
}

// Map models to tables
$modelTables = [];
foreach ($models as $file) {
    $content = file_get_contents($file);
    preg_match('/class\s+([A-Za-z0-9_]+)/', $content, $m);
    if ($m) {
        $model = $m[1];
        // default table name
        $table = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $model)) . 's';
        if (substr($table, -2) === 'ys') {
            $table = substr($table, 0, -2) . 'ies';
        }
        if ($table === 'categorys') $table = 'categories';
        if ($table === 'historys') $table = 'histories';
        if ($table === 'user_s') $table = 'users'; // Edge case users

        // check if table is explicitly defined
        preg_match('/\$table\s*=\s*\'([^\']+)\'/', $content, $tm);
        if ($tm) {
            $table = $tm[1];
        }
        $modelTables[$model] = $table;
    }
}

// Find usages
function addUsage($table, $col) {
    global $tables;
    if (isset($tables[$table])) {
        $tables[$table]['used_columns'][$col] = true;
    }
}

$allFiles = array_merge($models, $controllers, $views);

// For each model, extract fillable, casts
foreach ($models as $file) {
    $content = file_get_contents($file);
    preg_match('/class\s+([A-Za-z0-9_]+)/', $content, $m);
    if ($m) {
        $model = $m[1];
        if (!isset($modelTables[$model])) continue;
        $table = $modelTables[$model];

        // Fillable
        if (preg_match('/\$fillable\s*=\s*\[(.*?)\];/s', $content, $fm)) {
            preg_match_all('/\'([^\']+)\'/', $fm[1], $cols);
            foreach ($cols[1] as $c) addUsage($table, $c);
        }
        // Casts
        if (preg_match('/\$casts\s*=\s*\[(.*?)\];/s', $content, $cm)) {
            preg_match_all('/\'([^\']+)\'\s*=>/', $cm[1], $cols);
            foreach ($cols[1] as $c) addUsage($table, $c);
        }
    }
}

// Scan all files for usages
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
    preg_match_all('/\$[a-zA-Z0-9_]+->([a-zA-Z0-9_]+)/', $content, $matches);
    foreach ($matches[1] as $col) {
        // Exclude common methods
        if (!in_array($col, ['id', 'created_at', 'updated_at', 'deleted_at', 'where', 'get', 'first', 'find', 'update', 'save', 'delete', 'with', 'count'])) {
            $potential_columns[$col] = true;
        }
    }
}

// Now we have potential columns used. But we need to map them to specific tables.
// This is hard to do statically perfectly, but we can do a best effort.
// For now, let's output all potential missing columns per table by checking if any potential column matches a model's table usage context,
// or we just output raw data and let the LLM figure it out.
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
            // Also check if it's in potential_columns
            if (!isset($potential_columns[$c])) {
                $unused[] = $c;
            }
        }
    }
    
    // For missing, we also need to check potential columns that might belong to this table.
    // If a word is used as a property AND is in $fillable, we already caught it.
    // What if it's only in ->where()? We can't be sure which table it's for statically.
    
    $output[$table] = [
        'missing_from_fillable_casts' => $missing,
        'unused' => $unused,
        'migration_columns' => array_keys($data['migration_columns']),
        'used_in_fillable_casts' => array_keys($data['used_columns']),
    ];
}

file_put_contents('scan_result.json', json_encode([
    'tables' => $output,
    'potential_columns' => array_keys($potential_columns),
    'modelTables' => $modelTables
]));

echo "Done\n";
