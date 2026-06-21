<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\Illuminate\Support\Facades\DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'perlu_diproses', 'processing', 'ready_to_ship', 'shipping', 'delivered', 'completed', 'cancelled', 'refunded') NOT NULL DEFAULT 'pending'");
echo "ENUM modified successfully.\n";
