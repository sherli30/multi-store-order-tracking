<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'paid', 'paid', 'paid', 'failed', 'refund']);
        
        return [
            'order_id'         => \App\Models\Order::factory(),
            'transaction_code' => 'TRX-' . fake()->unique()->numerify('######') . '-' . strtoupper(fake()->lexify('???')),
            'payment_method'   => fake()->randomElement(['Transfer Bank (BCA)', 'Transfer Bank (Mandiri)', 'OVO', 'GoPay', 'Qris']),
            'amount'           => 0, // This will be overriden in Seeder to match Order total
            'status'           => $status,
            'payment_date'     => $status === 'paid' ? fake()->dateTimeBetween('-1 month', 'now') : null,
            'notes'            => $status === 'refund' ? 'Refund dana karena pesanan batal' : null,
        ];
    }
}
