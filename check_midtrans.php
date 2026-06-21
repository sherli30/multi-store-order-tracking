<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$serverKey = env('MIDTRANS_SERVER_KEY');
$orderId = 'INV/20260618/01DC3658';

echo "--- Sandbox ---\n";
$url = 'https://api.sandbox.midtrans.com/v2/' . $orderId . '/status';
$res = Illuminate\Support\Facades\Http::withHeaders([
    'Accept' => 'application/json',
    'Content-Type' => 'application/json',
    'Authorization' => 'Basic ' . base64_encode($serverKey . ':'),
])->get($url);
echo 'Status Code: ' . $res->status() . "\n";
echo 'Response: ' . $res->body() . "\n\n";

echo "--- Production ---\n";
$urlProd = 'https://api.midtrans.com/v2/' . $orderId . '/status';
$resProd = Illuminate\Support\Facades\Http::withHeaders([
    'Accept' => 'application/json',
    'Content-Type' => 'application/json',
    'Authorization' => 'Basic ' . base64_encode($serverKey . ':'),
])->get($urlProd);
echo 'Status Code: ' . $resProd->status() . "\n";
echo 'Response: ' . $resProd->body() . "\n\n";
