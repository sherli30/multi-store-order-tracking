<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\TrackingHistory;
use App\Services\BiteshipService;
use Illuminate\Support\Facades\Log;

class SyncBiteshipStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipments:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync active Biteship shipment statuses';

    /**
     * Execute the console command.
     */
    public function handle(BiteshipService $biteshipService)
    {
        // Only fetch orders that are in ready_to_ship or shipping status AND have a shipment_id or tracking_number
        $orders = Order::whereIn('status', [Order::STATUS_READY_TO_SHIP, Order::STATUS_SHIPPING])
            ->where(function ($q) {
                $q->whereNotNull('shipment_id')->orWhereNotNull('tracking_number');
            })
            ->get();

        $this->info("Found {$orders->count()} active shipments to sync.");

        foreach ($orders as $order) {
            try {
                // Only track if shipment_id exists
                if (!$order->shipment_id) continue;

                $response = $biteshipService->trackShipment($order->shipment_id);

                // Biteship returns status at the root level of the /v1/orders/{id} response
                $biteshipStatus = strtolower($response['status'] ?? '');
                if (empty($biteshipStatus)) continue;

                // Sync tracking history into database
                if (isset($response['courier']['history']) && is_array($response['courier']['history'])) {
                    foreach ($response['courier']['history'] as $historyItem) {
                        $trackedAt = \Carbon\Carbon::parse($historyItem['updated_at'])->setTimezone('Asia/Jakarta');
                        
                        \App\Models\ShipmentTrackingHistory::updateOrCreate([
                            'order_id' => $order->id,
                            'status' => $historyItem['status'],
                            'tracked_at' => $trackedAt,
                        ], [
                            'note' => $historyItem['note'] ?? null,
                        ]);
                    }
                }

                // Update local shipment status
                if ($order->shipment_status !== $biteshipStatus) {
                    $order->shipment_status = $biteshipStatus;
                    $order->save();
                    
                    $this->info("Order {$order->order_number}: shipment_status updated to {$biteshipStatus}");
                }

                // Map Biteship status to internal status progression
                $newStatus = null;
                $notes = "Status pengiriman diperbarui dari kurir: {$biteshipStatus}";

                if (in_array($biteshipStatus, ['picking_up', 'dropped', 'delivering'])) {
                    // Upgrade to shipping
                    if ($order->status === Order::STATUS_READY_TO_SHIP) {
                        $newStatus = Order::STATUS_SHIPPING;
                        $notes = "Pesanan telah diserahkan ke kurir (Status: {$biteshipStatus}).";
                    }
                } elseif ($biteshipStatus === 'delivered') {
                    // Upgrade to delivered
                    if (in_array($order->status, [Order::STATUS_READY_TO_SHIP, Order::STATUS_SHIPPING])) {
                        $newStatus = Order::STATUS_DELIVERED;
                        $notes = "Pesanan telah tiba di tujuan (Delivered). Menunggu konfirmasi penerimaan dari customer.";
                    }
                }

                if ($newStatus) {
                    $order->update(['status' => $newStatus]);
                    
                    TrackingHistory::create([
                        'order_id' => $order->id,
                        'admin_id' => null, // System
                        'status'   => $newStatus,
                        'notes'    => $notes,
                        'metadata' => ['biteship_sync' => true, 'biteship_status' => $biteshipStatus],
                    ]);

                    $this->info("Order {$order->order_number}: status upgraded to {$newStatus}");
                }

            } catch (\Exception $e) {
                Log::error("Failed to sync Biteship tracking for Order {$order->id}: " . $e->getMessage());
                $this->error("Failed Order {$order->order_number}: " . $e->getMessage());
            }
        }
    }
}
