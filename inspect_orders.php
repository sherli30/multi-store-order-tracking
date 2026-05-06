<?php
use App\Models\Order;
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$orders = Order::all(['order_number', 'status', 'payment_status', 'snap_token']);
print_r($orders->toArray());
