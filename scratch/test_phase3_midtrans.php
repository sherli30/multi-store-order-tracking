<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Spoof Auth
$user = \App\Models\User::first();
auth()->login($user);

// Setup dummy Midtrans webhook payload generator
function createPayload($orderId, $status) {
    return [
        'order_id' => $orderId,
        'status_code' => '200',
        'gross_amount' => '100000.00',
        'transaction_status' => $status,
        'transaction_id' => 'TRX-' . uniqid(),
        'payment_type' => 'gopay',
        'signature_key' => hash('sha512', $orderId . '200' . '100000.00' . config('midtrans.server_key')),
        'transaction_time' => now()->toDateTimeString()
    ];
}

// ----------------------------------------------------
// TEST V2: MULTI-STORE CHECKOUT -> SNAP -> WEBHOOK
// ----------------------------------------------------
echo "=== TESTING V2 INVOICE FLOW ===\n";

$p1 = \App\Models\Product::where('store_id', 1)->first();
$p2 = \App\Models\Product::where('store_id', 2)->first();

// 1. Create Checkout
$orderController = app()->make(\App\Http\Controllers\Api\OrderController::class);
$orderReq = Request::create('/api/v2/orders', 'POST', [
    'customer_name' => 'John V2',
    'customer_phone' => '08123456789',
    'shipping_address' => 'Jl. V2',
    'province' => 'Jawa Timur',
    'city' => 'Surabaya',
    'postal_code' => '60111',
    'payment_method' => 'midtrans',
    'store_orders' => [
        [
            'store_id' => 1,
            'shipping_type' => 'J&T Express - Reguler',
            'shipping_cost' => 11800,
            'items' => [['product_id' => $p1->id, 'quantity' => 1]]
        ]
    ]
]);
$response = json_decode($orderController->storeMulti($orderReq)->getContent());
if ($response->status !== 'success') {
    die("Checkout failed: " . json_encode($response));
}

$invoiceId = $response->data->invoice_id;
$invoiceMidtransId = $response->data->midtrans_order_id;
echo "1. Created Invoice ID: $invoiceId (Midtrans: $invoiceMidtransId)\n";

// 2. Generate Snap Token V2
$paymentController = app()->make(\App\Http\Controllers\Api\PaymentController::class);
$snapReq = Request::create('/api/v2/payment/snap-token', 'POST', ['invoice_id' => $invoiceId]);
$snapResponse = json_decode($paymentController->getSnapTokenV2($snapReq)->getContent());
echo "2. Snap Token V2 generated: " . ($snapResponse->snap_token ?? 'FAILED') . "\n";

// 3. Simulate Settlement Webhook V2
$webhookController = app()->make(\App\Http\Controllers\MidtransWebhookController::class);
$payload = createPayload($invoiceMidtransId, 'settlement');
$webhookReq = Request::create('/api/midtrans/callback', 'POST', $payload);
$webhookResponse = $webhookController->handle($webhookReq);
echo "3. Webhook settlement response: " . $webhookResponse->getContent() . "\n";

// Check DB
$invoice = \App\Models\Invoice::find($invoiceId);
echo "   Invoice status: " . $invoice->payment_status . "\n";
foreach ($invoice->orders as $o) {
    echo "   Child Order {$o->id} status: {$o->status}, payment: {$o->payment_status}, stock_deducted: {$o->is_stock_deducted}\n";
}

// 4. Simulate Expire Webhook V2 (Should be ignored because already settled)
echo "4. Simulating late EXPIRE webhook...\n";
$payloadExpire = createPayload($invoiceMidtransId, 'expire');
$webhookReqExp = Request::create('/api/midtrans/callback', 'POST', $payloadExpire);
$webhookRespExp = $webhookController->handle($webhookReqExp);
echo "   Response: " . $webhookRespExp->getContent() . "\n";

// Check DB again
$invoice->refresh();
echo "   Invoice status after expire: " . $invoice->payment_status . "\n";

// ----------------------------------------------------
// TEST V1: LEGACY CHECKOUT -> SNAP -> WEBHOOK
// ----------------------------------------------------
echo "\n=== TESTING V1 ORDER FLOW ===\n";
$v1Req = Request::create('/api/orders', 'POST', [
    'store_id' => 1,
    'customer_name' => 'John V1',
    'customer_phone' => '08123456789',
    'shipping_address' => 'Jl. V1',
    'province' => 'Jawa Timur',
    'city' => 'Surabaya',
    'postal_code' => '60111',
    'shipping_type' => 'J&T Express - Reguler',
    'shipping_cost' => 11800,
    'items' => [['product_id' => $p1->id, 'quantity' => 1]]
]);
$v1Resp = json_decode(app()->handle($v1Req)->getContent());
if ($v1Resp->status !== 'success') {
    die("V1 Checkout failed: " . json_encode($v1Resp));
}
$v1OrderId = $v1Resp->data->id;
$v1MidtransId = $v1Resp->data->midtrans_order_id;
echo "1. Created Legacy Order ID: $v1OrderId (Midtrans: $v1MidtransId)\n";

// 2. Generate Snap V1
$snapReqV1 = Request::create('/api/payment/snap-token', 'POST', ['order_id' => $v1OrderId]);
$snapRespV1 = json_decode($paymentController->getSnapToken($snapReqV1)->getContent());
echo "2. Snap Token V1 generated: " . ($snapRespV1->snap_token ?? 'FAILED') . "\n";

// 3. Simulate Settlement Webhook V1
$payloadV1 = createPayload($v1MidtransId, 'settlement');
$webhookReqV1 = Request::create('/api/midtrans/callback', 'POST', $payloadV1);
$webhookRespV1 = $webhookController->handle($webhookReqV1);
echo "3. Webhook settlement response: " . $webhookRespV1->getContent() . "\n";

// Check DB
$orderV1 = \App\Models\Order::find($v1OrderId);
echo "   Order V1 status: {$orderV1->status}, payment: {$orderV1->payment_status}, stock_deducted: {$orderV1->is_stock_deducted}\n";

// 4. Simulate Cancel Webhook V1 (Should be ignored)
$payloadCancelV1 = createPayload($v1MidtransId, 'cancel');
$webhookReqCancelV1 = Request::create('/api/midtrans/callback', 'POST', $payloadCancelV1);
$webhookRespCancelV1 = $webhookController->handle($webhookReqCancelV1);
echo "4. Late Cancel response: " . $webhookRespCancelV1->getContent() . "\n";

// 5. Simulate Failed Payment for V2 to check stock restoration
echo "\n=== TESTING V2 EXPIRE FLOW (STOCK RESTORE) ===\n";
$orderReqFailed = Request::create('/api/v2/orders', 'POST', [
    'customer_name' => 'John Failed',
    'customer_phone' => '081234',
    'shipping_address' => 'Jl',
    'province' => 'Jawa Timur',
    'city' => 'Surabaya',
    'postal_code' => '60111',
    'store_orders' => [
        [
            'store_id' => 1,
            'shipping_type' => 'J&T Express - Reguler',
            'shipping_cost' => 11800,
            'items' => [['product_id' => $p1->id, 'quantity' => 1]]
        ]
    ]
]);
$failResp = json_decode($orderController->storeMulti($orderReqFailed)->getContent());
$invFailMidtrans = $failResp->data->midtrans_order_id;
$invFailId = $failResp->data->invoice_id;

$failWebhookReq = Request::create('/api/midtrans/callback', 'POST', createPayload($invFailMidtrans, 'expire'));
$failWebhookResp = $webhookController->handle($failWebhookReq);
echo "1. Webhook Expire Response: " . $failWebhookResp->getContent() . "\n";

$invFail = \App\Models\Invoice::find($invFailId);
echo "   Invoice status: " . $invFail->payment_status . "\n";
foreach ($invFail->orders as $o) {
    echo "   Child Order {$o->id} status: {$o->status}, stock_deducted: {$o->is_stock_deducted}\n";
}
