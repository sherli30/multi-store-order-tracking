<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Services\BiteshipService;

$order = Order::find(2);

if (!$order) {
    echo json_encode(['error' => 'Order not found']);
    exit;
}

$biteshipService = app(BiteshipService::class);

try {
    // Test 1: Using tracking_number (waybill_id)
    $responseWaybill = $biteshipService->trackShipment($order->tracking_number, 'jne');
    echo "\n=== TRACKING USING tracking_number (waybill_id) ===\n";
    echo json_encode($responseWaybill, JSON_PRETTY_PRINT);
} catch (\Exception $e) {
    echo "\n=== TRACKING USING tracking_number ERROR ===\n";
    echo $e->getMessage();
}

try {
    // Test 2: Using shipment_id
    $responseShipment = $biteshipService->trackShipment($order->shipment_id, 'jne');
    echo "\n=== TRACKING USING shipment_id ===\n";
    echo json_encode($responseShipment, JSON_PRETTY_PRINT);
} catch (\Exception $e) {
    echo "\n=== TRACKING USING shipment_id ERROR ===\n";
    echo $e->getMessage();
}
