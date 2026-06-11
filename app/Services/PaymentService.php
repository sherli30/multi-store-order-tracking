<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * PaymentService — Handles payment operations like snap token generation
 * Separated from controllers for reusability in recovery scenarios
 */
class PaymentService
{
    private $serverKey;
    private $isProduction;
    private $snapUrl;

    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key');
        $this->isProduction = config('midtrans.is_production');
        $this->snapUrl = $this->isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    /**
     * Generate snap token for an order (extracted for reusability)
     * Returns token string on success, null on failure
     */
    public function generateSnapToken(Order $order): ?string
    {
        try {
            $order->load(['orderItems.product', 'customer']);

            $itemDetails = [];
            $itemsSum = 0;

            foreach ($order->orderItems as $item) {
                $price = (int) $item->price;
                $quantity = (int) $item->quantity;
                $productName = $item->product->name ?? 'Produk';
                $finalName = strlen($productName) > 50 ? substr($productName, 0, 47) . '...' : $productName;

                $itemDetails[] = [
                    'id' => (string) ($item->product_id ?? $item->id),
                    'price' => $price,
                    'quantity' => $quantity,
                    'name' => $finalName,
                ];

                $itemsSum += ($price * $quantity);
            }

            if ($order->shipping_cost > 0) {
                $itemDetails[] = [
                    'id' => 'SHIPPING',
                    'price' => (int) $order->shipping_cost,
                    'quantity' => 1,
                    'name' => 'Ongkos Kirim',
                ];
                $itemsSum += (int) $order->shipping_cost;
            }

            if ($order->packing_cost > 0) {
                $itemDetails[] = [
                    'id' => 'PACKING',
                    'price' => (int) $order->packing_cost,
                    'quantity' => 1,
                    'name' => 'Biaya Packing',
                ];
                $itemsSum += (int) $order->packing_cost;
            }

            $customerPhone = $order->customer_phone ?? $order->customer?->phone ?? null;
            $customerEmail = $order->customer_email ?? $order->customer?->email ?? null;

            if (empty($order->shipping_address) || empty($order->city) || empty($order->postal_code) || empty($customerPhone)) {
                Log::warning('[PaymentService] Incomplete address data for order #' . $order->id);
                return null;
            }

            $shippingAddress = [
                'first_name' => $order->customer_name,
                'phone' => $customerPhone,
                'address' => $order->shipping_address,
                'city' => $order->city,
                'postal_code' => $order->postal_code,
                'country_code' => 'IDN',
            ];

            $params = [
                'transaction_details' => [
                    'order_id' => $order->midtrans_order_id,
                    'gross_amount' => $itemsSum,
                ],
                'item_details' => $itemDetails,
                'customer_details' => [
                    'first_name' => $order->customer_name,
                    'email' => $customerEmail,
                    'phone' => $customerPhone,
                    'shipping_address' => $shippingAddress,
                    'billing_address' => $shippingAddress,
                ],
                'callbacks' => [
                    'finish' => 'https://ayam-bebek.mobi/payment/finish',
                    'unfinish' => 'https://ayam-bebek.mobi/payment/unfinish',
                    'error' => 'https://ayam-bebek.mobi/payment/error',
                ]
            ];

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
            ])->post($this->snapUrl, $params);

            if ($response->failed()) {
                Log::error('[PaymentService] Midtrans API error: ' . json_encode($response->json()));
                return null;
            }

            return $response->json()['token'] ?? null;

        } catch (\Exception $e) {
            Log::error('[PaymentService] Exception generating snap token: ' . $e->getMessage());
            return null;
        }
    }
}
