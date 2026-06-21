<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('role', 'customer')->first();
auth()->login($user);

// Get a store
$store = App\Models\Store::first();
$product = App\Models\Product::where('store_id', $store->id)->available()->first();
$initialSoldCount = $product->sold_count;
// Get initial Dashboard Metrics
$initialDashboardRevenue = \App\Models\Order::whereIn('payment_status', ['settlement', 'capture', 'paid'])->sum('total_amount');
$initialDashboardCount = \App\Models\Order::whereIn('payment_status', ['settlement', 'capture', 'paid'])->count();

// Get initial Product total_sold from ReportController logic
$initialReportSold = \App\Models\OrderItem::whereHas('order', function ($q) {
    $q->whereIn('payment_status', ['settlement', 'capture', 'paid']);
})->where('product_id', $product->id)->sum('quantity');

echo "\n--- BEFORE WEBHOOK ---\n";
echo "Initial sold_count for Product ID {$product->id}: {$initialSoldCount}\n";
echo "Initial Report total_sold for Product ID {$product->id}: {$initialReportSold}\n";
echo "Initial Dashboard Revenue: {$initialDashboardRevenue}\n";
echo "Initial Dashboard Order Count: {$initialDashboardCount}\n";

$request = new Illuminate\Http\Request();
$request->merge([
    'customer_name' => 'Test User',
    'customer_phone' => '081234567890',
    'shipping_address' => 'Test Address',
    'province' => 'Test Province',
    'city' => 'Test City',
    'postal_code' => '12345',
    'payment_method' => 'midtrans',
    'store_orders' => [
        [
            'store_id' => $store->id,
            'shipping_type' => 'Reguler - JNE',
            'shipping_cost' => 15000,
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1
                ]
            ]
        ]
    ]
]);

$controller = new App\Http\Controllers\Api\OrderController(new App\Services\OrderService());
$response = $controller->storeMulti($request);
$data = $response->getData(true)['data'];
$invoiceId = $data['id'];
$invoice = App\Models\Invoice::find($invoiceId);

echo "\n--- ORDER CREATED ---\n";
echo "Order Created! Invoice ID: {$invoiceId}\n";
echo "Midtrans Order ID: {$invoice->midtrans_order_id}\n";
echo "Initial Invoice Status: {$invoice->payment_status}\n";

// Now simulate Webhook
$webhookRequest = new Illuminate\Http\Request();
$orderId = $invoice->midtrans_order_id;
$statusCode = '200';
$grossAmount = $invoice->grand_total;
$serverKey = config('midtrans.server_key');
$signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

$webhookRequest->merge([
    'order_id' => $orderId,
    'status_code' => $statusCode,
    'gross_amount' => $grossAmount,
    'transaction_status' => 'settlement',
    'transaction_id' => 'TRX-' . time(),
    'payment_type' => 'credit_card',
    'signature_key' => $signature,
    'transaction_time' => now()->format('Y-m-d H:i:s')
]);

echo "\n--- FIRING WEBHOOK ---\n";
$webhookController = new App\Http\Controllers\MidtransWebhookController();
$webhookResponse = $webhookController->handle($webhookRequest);

echo "Webhook Response: " . $webhookResponse->getContent() . "\n";

echo "\n--- AFTER WEBHOOK ---\n";
$invoice->refresh();
echo "Updated Invoice Status: {$invoice->payment_status}\n";
$order = $invoice->orders()->first();
echo "Updated Child Order Status: {$order->status}\n";
echo "Updated Child Order Payment Status: {$order->payment_status}\n";
echo "Updated Child Order Stock Deducted: " . ($order->is_stock_deducted ? 'Yes' : 'No') . "\n";

$product->refresh();
echo "Updated DB sold_count for Product ID {$product->id}: {$product->sold_count} (Expected: " . ($initialSoldCount + 1) . ")\n";

$trackingHistories = \App\Models\TrackingHistory::where('order_id', $order->id)->get();
echo "Tracking Histories Count: {$trackingHistories->count()}\n";
foreach ($trackingHistories as $th) {
    echo " - [{$th->status}] {$th->notes}\n";
}

$transaction = \App\Models\Transaction::where('invoice_id', $invoice->id)->first();
echo "Transaction created: " . ($transaction ? 'Yes' : 'No') . " | Status: " . ($transaction ? $transaction->status : 'N/A') . " | Amount: " . ($transaction ? $transaction->amount : 'N/A') . "\n";

$finalDashboardRevenue = \App\Models\Order::whereIn('payment_status', ['settlement', 'capture', 'paid'])->sum('total_amount');
$finalDashboardCount = \App\Models\Order::whereIn('payment_status', ['settlement', 'capture', 'paid'])->count();
echo "Final Dashboard Revenue: {$finalDashboardRevenue} (Increased by: " . ($finalDashboardRevenue - $initialDashboardRevenue) . ", Expected: {$order->total_amount})\n";
echo "Final Dashboard Order Count: {$finalDashboardCount} (Increased by: " . ($finalDashboardCount - $initialDashboardCount) . ", Expected: 1)\n";

$finalReportSold = \App\Models\OrderItem::whereHas('order', function ($q) {
    $q->whereIn('payment_status', ['settlement', 'capture', 'paid']);
})->where('product_id', $product->id)->sum('quantity');
echo "Final Report total_sold for Product ID {$product->id}: {$finalReportSold} (Increased by: " . ($finalReportSold - $initialReportSold) . ", Expected: 1)\n";

