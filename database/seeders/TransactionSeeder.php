<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = \App\Models\Order::doesntHave('transaction')->get();

        foreach ($orders as $order) {
            // Determine transaction status based on order status to keep it realistic
            if ($order->status == 'pending') {
                $status = 'pending';
            } elseif ($order->status == 'cancelled') {
                $status = fake()->randomElement(['refund', 'failed']);
            } elseif ($order->status == 'perlu_diproses' || $order->status == 'processing' || $order->status == 'shipping' || $order->status == 'completed') {
                $status = 'paid';
            } else {
                $status = 'pending';
            }

            \App\Models\Transaction::factory()->create([
                'order_id' => $order->id,
                'amount' => $order->total_amount,
                'status' => $status,
                'payment_date' => $status == 'paid' ? $order->created_at->addDays(rand(0, 1)) : null,
            ]);
        }
    }
}
