<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$origin_id = 178; // Kediri
$destination_id = 444; // Surabaya
$weight = 15; // 15 kg
$courier = ''; // Optional

$request = Illuminate\Http\Request::create('/api/shipping/calculate', 'POST', [
    'store_id' => 1,
    'destination_city' => 'Surabaya',
    'items' => [
        ['product_id' => 1, 'quantity' => 1]
    ],
]);

$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
echo json_encode(json_decode($response->getContent()), JSON_PRETTY_PRINT) . "\n";
