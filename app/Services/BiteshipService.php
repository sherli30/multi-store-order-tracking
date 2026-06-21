<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Exception;

class BiteshipService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.biteship.base_url', 'https://api.biteship.com/v1');
        $this->apiKey = config('services.biteship.api_key');
    }

    /**
     * Map internal courier string and shipping type to Biteship courier code and service type
     */
    protected function mapCourier(string $internalCourier, string $internalType): array
    {
        $courier = strtolower(trim($internalCourier));
        $type = strtolower(trim($internalType));
        
        $mappedCourier = 'jne'; // fallback
        $mappedType = 'reg';    // fallback

        // Determine Courier Company
        if (str_contains($courier, 'jne')) {
            $mappedCourier = 'jne';
            if (str_contains($type, 'cargo')) $mappedType = 'jtr';
            elseif (str_contains($type, 'yes')) $mappedType = 'yes';
            elseif (str_contains($type, 'oke')) $mappedType = 'oke';
            else $mappedType = 'reg';
            
        } elseif (str_contains($courier, 'j&t') || str_contains($courier, 'jnt')) {
            $mappedCourier = 'jnt';
            if (str_contains($type, 'cargo')) $mappedType = 'j&t_cargo';
            elseif (str_contains($type, 'eco')) $mappedType = 'j&t_eco';
            elseif (str_contains($type, 'super')) $mappedType = 'j&t_super';
            else $mappedType = 'ez'; // J&T Reguler is typically EZ
            
        } elseif (str_contains($courier, 'sicepat')) {
            $mappedCourier = 'sicepat';
            if (str_contains($type, 'cargo') || str_contains($type, 'gokil')) $mappedType = 'gokil';
            elseif (str_contains($type, 'best')) $mappedType = 'best';
            elseif (str_contains($type, 'halu')) $mappedType = 'halu';
            else $mappedType = 'reg';
            
        } elseif (str_contains($courier, 'anteraja')) {
            $mappedCourier = 'anteraja';
            if (str_contains($type, 'cargo')) $mappedType = 'cargo';
            elseif (str_contains($type, 'nextday')) $mappedType = 'nextday';
            else $mappedType = 'reg';
            
        } else {
            // Generic Fallback handling
            if (str_contains($type, 'cargo')) {
                // If they picked generic "Cargo", route via JNE JTR
                $mappedCourier = 'jne';
                $mappedType = 'jtr';
            }
        }
        
        return [$mappedCourier, $mappedType];
    }

    /**
     * Generate a new shipment (AWB) in Biteship
     */
    public function createShipment(Order $order): array
    {
        if (empty($this->apiKey)) {
            throw new Exception("Biteship API key is not configured.");
        }

        list($courierCode, $courierService) = $this->mapCourier(
            $order->shipping_courier ?? 'jne',
            $order->shipping_type ?? 'reguler'
        );

        // Extract items
        $items = [];
        $totalWeight = 0;
        foreach ($order->orderItems as $item) {
            $weight = ($item->product->weight ?? 1) * 1000; // Convert to grams (assuming internal is KG)
            if ($weight < 100) $weight = 100; // Minimum 100g per item
            
            $items[] = [
                'name' => $item->product_name,
                'description' => $item->product_name,
                'value' => (int) $item->price,
                'quantity' => $item->quantity,
                'weight' => $weight,
            ];
            $totalWeight += ($weight * $item->quantity);
        }

        if (empty($items)) {
            $items[] = [
                'name' => 'Barang Pesanan',
                'description' => 'Barang pesanan',
                'value' => (int) $order->total_amount,
                'quantity' => 1,
                'weight' => 1000,
            ];
            $totalWeight = 1000;
        }

        $store = $order->store;
        
        // Ensure city relationship is loaded
        if (!$store->relationLoaded('city')) {
            $store->load('city');
        }

        $payload = [
            'origin_contact_name' => $store->name ?? 'Toko',
            'origin_contact_phone' => $store->phone ?? '081234567890',
            'origin_address' => $store->address ?? 'Alamat Toko',
            'origin_postal_code' => $store->city->postal_code ?? '12345',
            'destination_contact_name' => $order->customer_name,
            'destination_contact_phone' => $order->customer_phone,
            'destination_contact_email' => $order->customer_email,
            'destination_address' => $order->shipping_address . ', ' . $order->city . ', ' . $order->province,
            'destination_postal_code' => $order->postal_code ?? '12345',
            'courier_company' => $courierCode,
            'courier_type' => $courierService,
            'delivery_type' => 'now', // Generate AWB now
            'order_note' => $order->notes ?? '',
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ],
            'items' => $items,
        ];

        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
            'Content-Type' => 'application/json'
        ])->post("{$this->baseUrl}/orders", $payload);

        if (!$response->successful()) {
            throw new Exception("Biteship Error: " . $response->body());
        }

        return $response->json();
    }

    /**
     * Get live tracking status and history for a shipment
     * Uses the /v1/orders endpoint which provides the status and tracking history
     */
    public function trackShipment(string $shipmentId, string $courierCode = null): array
    {
        if (empty($this->apiKey)) {
            throw new Exception("Biteship API key is not configured.");
        }

        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
        ])->get("{$this->baseUrl}/orders/{$shipmentId}");

        if (!$response->successful()) {
            throw new Exception("Biteship Tracking Error: " . $response->body());
        }

        return $response->json();
    }
}
