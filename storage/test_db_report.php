<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\ShipmentTrackingHistory;
use Illuminate\Support\Facades\Schema;

$total = ShipmentTrackingHistory::count();
$samples = ShipmentTrackingHistory::all()->toArray();
$columns = Schema::getColumnListing('shipment_tracking_histories');

$order = Order::with('shipmentTrackingHistories')->find(2);
$relationshipWorking = $order && $order->shipmentTrackingHistories->count() > 0;

$flutterPayload = [
    'id' => $order->id,
    'tracking_number' => $order->tracking_number,
    'shipment_id' => $order->shipment_id,
    'shipment_status' => $order->shipment_status,
    'courier_name' => $order->courier_name ?? $order->shipping_courier,
    'courier_service' => $order->courier_service ?? $order->shipping_type,
    'tracking_history' => $order->shipmentTrackingHistories->map(function($history) {
        return [
            'status' => $history->status,
            'note' => $history->note,
            'tracked_at' => $history->tracked_at->toIso8601String(),
        ];
    })->toArray()
];

echo json_encode([
    'total_records' => $total,
    'samples' => $samples,
    'table_structure' => $columns,
    'relationship_working' => $relationshipWorking,
    'flutter_payload' => $flutterPayload
], JSON_PRETTY_PRINT);
