<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orders = App\Models\Order::where('payment_status', 'pending')->whereNull('invoice_id')->orderBy('id', 'desc')->take(10)->get();
$serverKey = config('midtrans.server_key');
$isProd = config('midtrans.is_production');
$baseUrl = $isProd ? 'https://api.midtrans.com/v2/' : 'https://api.sandbox.midtrans.com/v2/';

foreach ($orders as $order) {
    $midtransId = $order->midtrans_order_id ?? $order->id;
    $response = Illuminate\Support\Facades\Http::withHeaders([
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'Authorization' => 'Basic ' . base64_encode($serverKey . ':'),
    ])->get($baseUrl . $midtransId . '/status');

    if ($response->successful()) {
        $status = $response->json()['transaction_status'] ?? 'unknown';
        if ($status === 'settlement' || $status === 'capture') {
            echo "FOUND PAID ORDER: {$order->id} | Midtrans ID: {$midtransId} | Status: {$status}\n";
            exit;
        }
    }
}
echo "No stuck paid orders found.\n";
