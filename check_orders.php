<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check invoices (V2)
$invoices = App\Models\Invoice::where('payment_status', 'pending')->orderBy('id', 'desc')->take(20)->get();
$serverKey = config('midtrans.server_key');
$baseUrlSandbox = 'https://api.sandbox.midtrans.com/v2/';
$baseUrlProd = 'https://api.midtrans.com/v2/';

$found = false;

echo "--- INVOICES (V2) ---\n";
foreach ($invoices as $inv) {
    if (!$inv->snap_token) continue;

    $midtransId = $inv->midtrans_order_id ?? $inv->invoice_number;
    
    // Check Sandbox
    $resSandbox = Illuminate\Support\Facades\Http::withHeaders([
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'Authorization' => 'Basic ' . base64_encode($serverKey . ':'),
    ])->get($baseUrlSandbox . $midtransId . '/status');

    if ($resSandbox->successful()) {
        $data = $resSandbox->json();
        $status = $data['transaction_status'] ?? 'unknown';
        if (in_array($status, ['settlement', 'capture'])) {
            echo "FOUND PAID IN SANDBOX -> Inv ID: {$inv->id} | Midtrans ID: {$midtransId} | Status: {$status}\n";
            $found = true;
        }
    }

    // Check Prod
    $resProd = Illuminate\Support\Facades\Http::withHeaders([
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'Authorization' => 'Basic ' . base64_encode($serverKey . ':'),
    ])->get($baseUrlProd . $midtransId . '/status');

    if ($resProd->successful()) {
        $data = $resProd->json();
        $status = $data['transaction_status'] ?? 'unknown';
        if (in_array($status, ['settlement', 'capture'])) {
            echo "FOUND PAID IN PROD -> Inv ID: {$inv->id} | Midtrans ID: {$midtransId} | Status: {$status}\n";
            $found = true;
        }
    }
}

// Check Orders (V1)
$orders = App\Models\Order::where('payment_status', 'pending')->whereNull('invoice_id')->orderBy('id', 'desc')->take(20)->get();
echo "--- ORDERS (V1) ---\n";
foreach ($orders as $order) {
    if (!$order->snap_token) continue;

    $midtransId = $order->midtrans_order_id ?? $order->order_number;
    
    // Check Sandbox
    $resSandbox = Illuminate\Support\Facades\Http::withHeaders([
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'Authorization' => 'Basic ' . base64_encode($serverKey . ':'),
    ])->get($baseUrlSandbox . $midtransId . '/status');

    if ($resSandbox->successful()) {
        $data = $resSandbox->json();
        $status = $data['transaction_status'] ?? 'unknown';
        if (in_array($status, ['settlement', 'capture'])) {
            echo "FOUND PAID IN SANDBOX -> Order ID: {$order->id} | Midtrans ID: {$midtransId} | Status: {$status}\n";
            $found = true;
        }
    }

    // Check Prod
    $resProd = Illuminate\Support\Facades\Http::withHeaders([
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'Authorization' => 'Basic ' . base64_encode($serverKey . ':'),
    ])->get($baseUrlProd . $midtransId . '/status');

    if ($resProd->successful()) {
        $data = $resProd->json();
        $status = $data['transaction_status'] ?? 'unknown';
        if (in_array($status, ['settlement', 'capture'])) {
            echo "FOUND PAID IN PROD -> Order ID: {$order->id} | Midtrans ID: {$midtransId} | Status: {$status}\n";
            $found = true;
        }
    }
}

if (!$found) {
    echo "No actual paid (settlement) invoices/orders found in Midtrans.\n";
}
