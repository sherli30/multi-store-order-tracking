<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use Illuminate\Support\Facades\Http;

$order = Order::find(2);
$apiKey = config('services.biteship.api_key');
$baseUrl = config('services.biteship.base_url', 'https://api.biteship.com/v1');

try {
    $response = Http::withHeaders(['Authorization' => $apiKey])
        ->get("{$baseUrl}/orders/{$order->shipment_id}");
    
    echo "\n=== GET ORDER USING shipment_id ===\n";
    echo json_encode($response->json(), JSON_PRETTY_PRINT);
} catch (\Exception $e) {
    echo "\n=== ERROR ===\n";
    echo $e->getMessage();
}
