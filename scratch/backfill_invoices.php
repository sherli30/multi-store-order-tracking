<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\Invoice;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

echo "Starting Backfill...\n";

DB::transaction(function () {
    // We group by midtrans_order_id. If it's null, we group by order_number.
    $orders = Order::all();
    
    $groups = [];
    foreach ($orders as $order) {
        $key = $order->midtrans_order_id ?? $order->order_number;
        if (!isset($groups[$key])) {
            $groups[$key] = [];
        }
        $groups[$key][] = $order;
    }

    echo "Found " . count($groups) . " unique order groups out of " . $orders->count() . " orders.\n";

    foreach ($groups as $key => $groupOrders) {
        // Calculate totals
        $totalProductAmount = 0;
        $totalShippingAmount = 0;
        
        $firstOrder = $groupOrders[0];
        
        foreach ($groupOrders as $o) {
            // total_amount currently includes shipping cost
            $totalProductAmount += ($o->total_amount - $o->shipping_cost);
            $totalShippingAmount += $o->shipping_cost;
        }
        
        $grandTotal = $totalProductAmount + $totalShippingAmount;
        
        // Generate a new invoice number like INV/YYYYMMDD/midtrans_order_id
        $invoiceNumber = 'INV/' . date('Ymd') . '/' . strtoupper(substr(md5($key), 0, 8));

        $invoice = Invoice::create([
            'invoice_number'        => $invoiceNumber,
            'user_id'               => $firstOrder->user_id,
            'total_product_amount'  => $totalProductAmount,
            'total_shipping_amount' => $totalShippingAmount,
            'grand_total'           => $grandTotal,
            'payment_status'        => $firstOrder->payment_status ?? 'pending',
            'payment_type'          => $firstOrder->payment_type,
            'midtrans_order_id'     => $firstOrder->midtrans_order_id,
            'snap_token'            => $firstOrder->snap_token,
        ]);

        foreach ($groupOrders as $o) {
            $o->update(['invoice_id' => $invoice->id]);
            
            // If the order has a transaction, link it
            if ($o->transaction) {
                $o->transaction->update(['invoice_id' => $invoice->id]);
            }
        }
    }
});

echo "Backfill Complete!\n";
echo "Total Invoices Created: " . Invoice::count() . "\n";
echo "Total Orders Linked: " . Order::whereNotNull('invoice_id')->count() . "\n";
echo "Total Transactions Linked: " . Transaction::whereNotNull('invoice_id')->count() . "\n";

