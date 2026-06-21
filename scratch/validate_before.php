<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\Transaction;

echo "Before Migration Status:\n";
echo "Total Orders: " . Order::count() . "\n";
echo "Total Transactions: " . Transaction::count() . "\n";
