<?php

use App\Models\Order;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Starting synchronization...\n";

// Opsi 1: Berdasarkan field payment_status di model Order
$affectedOrders = Order::where('status', 'pending')
    ->whereIn('payment_status', ['settlement', 'capture', 'paid', 'success'])
    ->get();

// Opsi 2: Berdasarkan relasi transaction (jika ada)
$affectedOrdersByTransaction = Order::where('status', 'pending')
    ->whereHas('transaction', function($q) {
        $q->whereIn('status', ['settlement', 'capture', 'paid', 'success']);
    })
    ->get();

$allOrders = $affectedOrders->merge($affectedOrdersByTransaction)->unique('id');

$count = 0;
foreach ($allOrders as $order) {
    $order->update(['status' => 'perlu_diproses']);
    echo "Order #{$order->order_number} updated to 'perlu_diproses'.\n";
    $count++;
}

echo "Synchronization complete. {$count} orders updated.\n";
