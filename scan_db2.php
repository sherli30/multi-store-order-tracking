<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$modelsDir = 'app/Models';
$models = [];
foreach (glob($modelsDir . '/*.php') as $file) {
    $content = file_get_contents($file);
    if (preg_match('/class (\w+) extends Model/', $content, $m)) {
        $modelName = $m[1];
        $className = "App\\Models\\" . $modelName;
        if (!class_exists($className)) continue;
        
        try {
            $model = new $className;
            $tableName = $model->getTable();
            
            $fillable = $model->getFillable();
            $casts = array_keys($model->getCasts());
            
            $softDeletes = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive($className));
            
            $models[$modelName] = [
                'table' => $tableName,
                'fillable' => $fillable,
                'casts' => $casts,
                'softDeletes' => $softDeletes
            ];
        } catch (\Throwable $e) {
            // ignore
        }
    }
}

$missing = [];

foreach ($models as $modelName => $info) {
    $table = $info['table'];
    if (!Schema::hasTable($table)) continue;
    
    $existingColumns = Schema::getColumnListing($table);
    
    foreach ($info['fillable'] as $col) {
        if (!in_array($col, $existingColumns) && $col !== 'order_number' && $col !== 'transaction_code') {
            $missing[$table][$col] = ['type' => 'string', 'reason' => '$fillable'];
        }
    }
    
    foreach ($info['casts'] as $col) {
        if (!in_array($col, $existingColumns) && $col !== 'order_number' && $col !== 'transaction_code' && $col !== 'id') {
            // Some casts might be virtual, but let's list them
            $missing[$table][$col] = ['type' => 'string', 'reason' => '$casts'];
        }
    }
    
    if ($info['softDeletes'] && !in_array('deleted_at', $existingColumns)) {
        $missing[$table]['deleted_at'] = ['type' => 'timestamp', 'reason' => 'SoftDeletes trait'];
    }
}

// Add user's explicit request
if (!in_array('order_number', Schema::getColumnListing('orders'))) {
    $missing['orders']['order_number'] = ['type' => 'string', 'reason' => 'User Request (Identifier)'];
}
if (!in_array('transaction_code', Schema::getColumnListing('transactions'))) {
    $missing['transactions']['transaction_code'] = ['type' => 'string', 'reason' => 'User Request (Identifier)'];
}

echo json_encode($missing, JSON_PRETTY_PRINT);
