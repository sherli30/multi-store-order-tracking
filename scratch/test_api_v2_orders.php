<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;

// 1. Test /api/v2/shipping/calculate
$request = Request::create('/api/v2/shipping/calculate', 'POST', [
    'destination_city' => 'Surabaya',
    'store_groups' => [
        [
            'store_id' => 1,
            'items' => [
                ['product_id' => 1, 'quantity' => 1]
            ]
        ],
        [
            'store_id' => 2,
            'items' => [
                ['product_id' => 2, 'quantity' => 2]
            ]
        ]
    ]
]);

$response = app()->handle($request);
echo "Shipping Response:\n";
echo $response->getContent() . "\n\n";

// 2. Test /api/v2/orders
// Since it's authenticated, we bypass auth or just run the controller directly.
$controller = app()->make(\App\Http\Controllers\Api\OrderController::class);
// Spoof Auth
$user = \App\Models\User::first();
auth()->login($user);

// Fetch a valid product for store 1
$p1 = \App\Models\Product::where('store_id', 1)->first();

$orderReq = Request::create('/api/v2/orders', 'POST', [
    'customer_name' => 'John Doe',
    'customer_phone' => '08123456789',
    'shipping_address' => 'Jl. Mawar No 1',
    'province' => 'Jawa Timur',
    'city' => 'Surabaya',
    'postal_code' => '60111',
    'payment_method' => 'midtrans',
    'store_orders' => [
        [
            'store_id' => 1,
            'shipping_type' => 'J&T Express - Reguler',
            'shipping_cost' => 11800,
            'items' => [
                ['product_id' => $p1->id, 'quantity' => 1]
            ]
        ]
    ]
]);

$response = $controller->storeMulti($orderReq);
echo "Order Response:\n";
echo $response->getContent() . "\n";
