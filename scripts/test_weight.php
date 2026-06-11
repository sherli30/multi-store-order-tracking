<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$p = App\Models\ProductVariant::first();
if ($p) {
    var_dump($p->weight);
    var_dump((int) $p->weight);
    var_dump(round($p->weight));
} else {
    echo "No variants\n";
}
