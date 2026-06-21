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
    $response = $biteshipService->createShipment($order);
    
    $order->update([
        'tracking_number' => $response['id'] ?? null, 
        'shipment_id' => $response['id'] ?? null,
        'shipment_status' => $response['status'] ?? 'allocated',
        'courier_name' => $response['courier']['company'] ?? null,
        'courier_service' => $response['courier']['type'] ?? null,
        'shipment_created_at' => now(),
        'status' => App\Models\Order::STATUS_READY_TO_SHIP,
    ]);
    
    if (!empty($response['courier']['waybill_id'])) {
        $order->update(['tracking_number' => $response['courier']['waybill_id']]);
    }

    echo json_encode(['success' => true, 'response' => $response, 'order' => $order->fresh()]);

} catch (\Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
