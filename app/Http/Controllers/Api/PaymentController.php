<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\OrderService;

class PaymentController extends Controller
{
    private $serverKey;
    private $isProduction;
    private $snapUrl;

    private $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->serverKey = config('midtrans.server_key');
        $this->isProduction = config('midtrans.is_production');
        $this->snapUrl = $this->isProduction 
            ? 'https://app.midtrans.com/snap/v1/transactions' 
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
        $this->orderService = $orderService;
    }

    public function getSnapToken(Request $request)
    {
        // Load order with all necessary relationships including variants
        $order = Order::with(['orderItems.product', 'orderItems.productVariant', 'customer'])->findOrFail($request->order_id);

        $itemDetails = [];
        $itemsSum = 0;

        // 1. Loop through order items dynamically
        foreach ($order->orderItems as $item) {
            $price = (int) $item->price;
            $quantity = (int) $item->quantity;
            
            // Gabungkan nama produk dan varian untuk tampilan profesional
            $productName = $item->product->name ?? 'Produk';
            $variantName = $item->productVariant->name ?? '';
            $fullName = $variantName ? "$productName ($variantName)" : $productName;
            
            // Limit name length to 50 characters as per Midtrans best practices
            $finalName = strlen($fullName) > 50 ? substr($fullName, 0, 47) . '...' : $fullName;

            $itemDetails[] = [
                'id' => (string) ($item->product_id ?? $item->id),
                'price' => $price,
                'quantity' => $quantity,
                'name' => $finalName,
            ];
            
            $itemsSum += ($price * $quantity);
        }

        // Add Shipping Cost as an item if exists
        if ($order->shipping_cost > 0) {
            $shippingCost = (int) $order->shipping_cost;
            $itemDetails[] = [
                'id' => 'SHIPPING',
                'price' => $shippingCost,
                'quantity' => 1,
                'name' => 'Ongkos Kirim',
            ];
            $itemsSum += $shippingCost;
        }

        // Add Packing Cost as an item if exists
        if ($order->packing_cost > 0) {
            $packingCost = (int) $order->packing_cost;
            $itemDetails[] = [
                'id' => 'PACKING',
                'price' => $packingCost,
                'quantity' => 1,
                'name' => 'Biaya Packing',
            ];
            $itemsSum += $packingCost;
        }

        // 2. Prepare Addresses
        $shippingAddress = [
            'first_name' => $order->customer_name,
            'phone' => $order->customer_phone ?? '08123456789',
            'address' => $order->shipping_address ?? 'Alamat tidak lengkap',
            'city' => $order->city ?? 'Kota',
            'postal_code' => $order->postal_code ?? '12345',
            'country_code' => 'IDN',
        ];

        $billingAddress = $shippingAddress;

        // 3. Combine all params
        $midtransOrderId = $order->order_number . '-' . time();
        $params = [
            'transaction_details' => [
                'order_id' => $midtransOrderId,
                'gross_amount' => $itemsSum,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => $order->customer_name,
                'email' => $order->customer_email ?? 'customer@example.com',
                'phone' => $order->customer_phone ?? '08123456789',
                'shipping_address' => $shippingAddress,
                'billing_address' => $billingAddress,
            ],
            // Tambahkan Callbacks agar WebView bisa menangkap redirect dengan benar
            'callbacks' => [
                'finish' => 'https://ayam-bebek.mobi/payment/finish',
                'unfinish' => 'https://ayam-bebek.mobi/payment/unfinish',
                'error' => 'https://ayam-bebek.mobi/payment/error',
            ]
        ];

        // Log payload for debugging
        Log::info('Midtrans Request Payload:', $params);

        // Optional: Specific payment method selection
        if ($request->payment_method && $request->payment_method != 'midtrans') {
            $params['enabled_payments'] = [$request->payment_method];
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
            ])->post($this->snapUrl, $params);

            if ($response->failed()) {
                Log::error('Midtrans API Error:', $response->json());
                return response()->json([
                    'status' => 'error',
                    'message' => $response->json()['error_messages'][0] ?? 'Midtrans API Error'
                ], 400);
            }

            $snapToken = $response->json()['token'];
            $order->update([
                'snap_token' => $snapToken,
                'midtrans_order_id' => $midtransOrderId
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Token pembayaran berhasil dibuat. Silakan lanjutkan pembayaran melalui gerbang Midtrans.',
                'snap_token' => $snapToken
            ]);

        } catch (\Exception $e) {
            Log::error('PaymentController Exception:', ['message' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function callback(Request $request)
    {
        $serverKey = $this->serverKey;
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        
        if ($hashed !== $request->signature_key) {
            Log::warning('Midtrans Callback: Invalid Signature', ['payload' => $request->all()]);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // Try to find by midtrans_order_id first, then fallback to parsing order_id
        $order = Order::where('midtrans_order_id', $request->order_id)->first();
        
        if (!$order) {
            // Fallback: Parse order_number from order_id (Format: ORD-XXXX-TIMESTAMP)
            $orderId = $request->order_id;
            $lastDashPos = strrpos($orderId, '-');
            if ($lastDashPos !== false) {
                $orderNumber = substr($orderId, 0, $lastDashPos);
                $order = Order::where('order_number', $orderNumber)->first();
            }
        }

        if (!$order) {
            Log::error('Midtrans Callback: Order not found', ['midtrans_order_id' => $request->order_id]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        $transactionStatus = $request->transaction_status;
        $type = $request->payment_type;
        $fraud = $request->fraud_status;

        Log::info("Midtrans Callback received for Order #{$order->order_number}: Status={$transactionStatus}");

        if ($transactionStatus == 'capture') {
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    $order->update(['payment_status' => 'pending']);
                } else {
                    $this->markAsPaid($order, 'settlement');
                }
            }
        } elseif ($transactionStatus == 'settlement') {
            $this->markAsPaid($order, 'settlement');
        } elseif ($transactionStatus == 'pending') {
            $order->update(['payment_status' => 'pending']);
        } elseif ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
            $this->markAsCancelled($order, $transactionStatus);
        }

        // Map Midtrans status to Internal Transaction status
        $internalTrxStatus = 'pending';
        if (in_array($transactionStatus, ['settlement', 'capture'])) {
            $internalTrxStatus = 'paid';
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            $internalTrxStatus = 'failed';
        }

        // Update or create transaction log
        Transaction::updateOrCreate(
            ['order_id' => $order->id],
            [
                'transaction_code' => $request->transaction_id,
                'payment_method' => $request->payment_type,
                'status' => $internalTrxStatus,
                'amount' => $request->gross_amount,
                'payment_date' => $internalTrxStatus === 'paid' ? now() : null,
                'notes' => "Midtrans Status: {$transactionStatus}",
            ]
        );

        return response()->json(['message' => 'Callback handled successfully']);
    }

    /**
     * Mark order as paid and handle stock deduction.
     */
    private function markAsPaid(Order $order, string $paymentStatus)
    {
        $order->update([
            'payment_status' => $paymentStatus,
            'status' => 'perlu_diproses'
        ]);

        // Deduct stock if not already done
        try {
            $this->orderService->processOrderStock($order);
        } catch (\Exception $e) {
            Log::error("Failed to deduct stock for order #{$order->order_number} in callback: " . $e->getMessage());
        }
    }

    /**
     * Mark order as cancelled and handle stock restoration.
     */
    private function markAsCancelled(Order $order, string $paymentStatus)
    {
        $order->update([
            'payment_status' => $paymentStatus,
            'status' => 'cancelled'
        ]);

        // Restore stock if it was deducted
        try {
            $this->orderService->restoreOrderStock($order, 'midtrans_callback_' . $paymentStatus);
        } catch (\Exception $e) {
            Log::error("Failed to restore stock for order #{$order->order_number} in callback: " . $e->getMessage());
        }
    }

    public function checkStatus($id)
    {
        $order = Order::findOrFail($id);
        $midtransOrderId = $order->midtrans_order_id ?? $order->order_number;
        
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
        ])->get(($this->isProduction ? 'https://api.midtrans.com/v2/' : 'https://api.sandbox.midtrans.com/v2/') . $midtransOrderId . "/status");

        if ($response->successful()) {
            $data = $response->json();
            $transactionStatus = $data['transaction_status'];
            
            if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                $this->markAsPaid($order, 'settlement');
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Pembayaran berhasil diverifikasi!',
                    'order_status' => 'perlu_diproses'
                ]);
            }
            
            return response()->json([
                'status' => 'info',
                'message' => 'Status pembayaran: ' . $transactionStatus,
                'midtrans_status' => $transactionStatus
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal menghubungi Midtrans.'
        ], 500);
    }
}
