<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$token = '13|5cd9ybHP6jFZafkRsV0XVcHoECfDDdS3fMwloiYDe9ae92fe';

$ch = curl_init('http://127.0.0.1:8000/api/v2/orders');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'customer_name' => 'Tester',
    'customer_phone' => '081234567890',
    'shipping_address' => 'Jl. Test No. 123',
    'province' => 'Jawa Timur',
    'city' => 'Surabaya',
    'postal_code' => '60111',
    'payment_method' => 'midtrans',
    'notes' => 'Testing V2 Checkout',
    'store_orders' => [
        [
            'store_id' => 3,
            'shipping_type' => 'jne - reguler',
            'shipping_cost' => 15000,
            'items' => [
                ['product_id' => 1, 'quantity' => 1]
            ]
        ],
        [
            'store_id' => 1,
            'shipping_type' => 'jnt - ez',
            'shipping_cost' => 12000,
            'items' => [
                ['product_id' => 3, 'quantity' => 2]
            ]
        ]
    ]
]));
$response = curl_exec($ch);
echo "RESPONSE BODY:\n" . $response . "\n";
die();


$p1 = \App\Models\Product::where('store_id', 1)->first();
$p2 = \App\Models\Product::where('store_id', 2)->first();

$request = Illuminate\Http\Request::create('/api/v2/orders', 'POST', [
    'customer_name' => 'Tester',
    'customer_phone' => '081234567890',
    'shipping_address' => 'Jl. Test No. 123',
    'province' => 'Jawa Timur',
    'city' => 'Surabaya',
    'postal_code' => '60111',
    'payment_method' => 'midtrans',
    'notes' => 'Testing V2 Checkout',
    'store_orders' => [
        [
            'store_id' => 1,
            'shipping_type' => 'jne - reguler',
            'shipping_cost' => 15000,
            'items' => [
                ['product_id' => $p1->id, 'quantity' => 1]
            ]
        ],
        [
            'store_id' => 2,
            'shipping_type' => 'jnt - ez',
            'shipping_cost' => 12000,
            'items' => [
                ['product_id' => $p2->id, 'quantity' => 2]
            ]
        ]
    ]
]);

$response = $kernel->handle($request);
echo "STATUS CODE: " . $response->getStatusCode() . "\n";
echo "RESPONSE BODY:\n" . $response->getContent() . "\n";
