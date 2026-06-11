<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class CustomerSeeder extends Seeder
{
    /**
     * Seed realistic Indonesian customer data.
     *
     * Compatible with:
     *  - CustomerController filters  (search, status, date_from/to, store_id, sort_by)
     *  - Stats bar                   (total, active, blocked)
     *  - Table columns               (name, email, phone, orders_count, is_active, created_at)
     *  - Orders / transactions       (relationships resolved via orders_count withCount)
     */
    public function run(): void
    {
        // Shared hashed password — change before production use
        $password = Hash::make('password');

        $customers = $this->customerData();

        foreach ($customers as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['password' => $password])
            );
        }

        $this->command->info('CustomerSeeder: ' . count($customers) . ' customers seeded.');
    }

    // -------------------------------------------------------------------------
    // Data
    // -------------------------------------------------------------------------

    private function customerData(): array
    {
        return [
            // ── Active customers — spread across several registration dates ──

            [
                'name'       => 'Siti Rahayu',
                'email'      => 'siti.rahayu@gmail.com',
                'phone'      => '081234567890',
                'role'       => 'customer',
                'is_active'  => 1,
                'created_at' => Carbon::now()->subMonths(14),
                'updated_at' => Carbon::now()->subMonths(14),
            ],
            [
                'name'       => 'Budi Santoso',
                'email'      => 'budi.santoso@yahoo.com',
                'phone'      => '082345678901',
                'role'       => 'customer',
                'is_active'  => 1,
                'created_at' => Carbon::now()->subMonths(12),
                'updated_at' => Carbon::now()->subMonths(12),
            ],
            [
                'name'       => 'Dewi Kusuma',
                'email'      => 'dewi.kusuma@gmail.com',
                'phone'      => '083456789012',
                'role'       => 'customer',
                'is_active'  => 1,
                'created_at' => Carbon::now()->subMonths(11),
                'updated_at' => Carbon::now()->subMonths(11),
            ],
            [
                'name'       => 'Ahmad Fauzi',
                'email'      => 'ahmad.fauzi@gmail.com',
                'phone'      => '085678901234',
                'role'       => 'customer',
                'is_active'  => 1,
                'created_at' => Carbon::now()->subMonths(10),
                'updated_at' => Carbon::now()->subMonths(10),
            ],
            [
                'name'       => 'Rina Wulandari',
                'email'      => 'rina.wulandari@gmail.com',
                'phone'      => '087890123456',
                'role'       => 'customer',
                'is_active'  => 1,
                'created_at' => Carbon::now()->subMonths(9),
                'updated_at' => Carbon::now()->subMonths(9),
            ],
            [
                'name'       => 'Hendra Wijaya',
                'email'      => 'hendra.wijaya@outlook.com',
                'phone'      => '081345678901',
                'role'       => 'customer',
                'is_active'  => 1,
                'created_at' => Carbon::now()->subMonths(8),
                'updated_at' => Carbon::now()->subMonths(8),
            ],
            [
                'name'       => 'Fitri Handayani',
                'email'      => 'fitri.handayani@gmail.com',
                'phone'      => '082456789012',
                'role'       => 'customer',
                'is_active'  => 1,
                'created_at' => Carbon::now()->subMonths(7),
                'updated_at' => Carbon::now()->subMonths(7),
            ],
            [
                'name'       => 'Yoga Pratama',
                'email'      => 'yoga.pratama@gmail.com',
                'phone'      => '083567890123',
                'role'       => 'customer',
                'is_active'  => 1,
                'created_at' => Carbon::now()->subMonths(6),
                'updated_at' => Carbon::now()->subMonths(6),
            ],
            [
                'name'       => 'Lestari Ningrum',
                'email'      => 'lestari.ningrum@gmail.com',
                'phone'      => '085789012345',
                'role'       => 'customer',
                'is_active'  => 1,
                'created_at' => Carbon::now()->subMonths(5),
                'updated_at' => Carbon::now()->subMonths(5),
            ],
            [
                'name'       => 'Reza Firmansyah',
                'email'      => 'reza.firmansyah@gmail.com',
                'phone'      => '086890123456',
                'role'       => 'customer',
                'is_active'  => 1,
                'created_at' => Carbon::now()->subMonths(4),
                'updated_at' => Carbon::now()->subMonths(4),
            ],
            [
                'name'       => 'Nurul Hidayah',
                'email'      => 'nurul.hidayah@gmail.com',
                'phone'      => '081456789012',
                'role'       => 'customer',
                'is_active'  => 1,
                'created_at' => Carbon::now()->subMonths(3),
                'updated_at' => Carbon::now()->subMonths(3),
            ],
            [
                'name'       => 'Teguh Prabowo',
                'email'      => 'teguh.prabowo@yahoo.co.id',
                'phone'      => '082567890123',
                'role'       => 'customer',
                'is_active'  => 1,
                'created_at' => Carbon::now()->subMonths(2),
                'updated_at' => Carbon::now()->subMonths(2),
            ],
            [
                'name'       => 'Anita Sari',
                'email'      => 'anita.sari@gmail.com',
                'phone'      => '083678901234',
                'role'       => 'customer',
                'is_active'  => 1,
                'created_at' => Carbon::now()->subMonths(1),
                'updated_at' => Carbon::now()->subMonths(1),
            ],
            [
                'name'       => 'Doni Setiawan',
                'email'      => 'doni.setiawan@gmail.com',
                'phone'      => '084789012345',
                'role'       => 'customer',
                'is_active'  => 1,
                'created_at' => Carbon::now()->subWeeks(3),
                'updated_at' => Carbon::now()->subWeeks(3),
            ],
            [
                'name'       => 'Maya Anggraini',
                'email'      => 'maya.anggraini@gmail.com',
                'phone'      => '085890123456',
                'role'       => 'customer',
                'is_active'  => 1,
                'created_at' => Carbon::now()->subWeeks(2),
                'updated_at' => Carbon::now()->subWeeks(2),
            ],
            [
                'name'       => 'Irfan Hakim',
                'email'      => 'irfan.hakim@gmail.com',
                'phone'      => '086901234567',
                'role'       => 'customer',
                'is_active'  => 1,
                'created_at' => Carbon::now()->subWeeks(1),
                'updated_at' => Carbon::now()->subWeeks(1),
            ],
            [
                'name'       => 'Putri Maharani',
                'email'      => 'putri.maharani@gmail.com',
                'phone'      => '087012345678',
                'role'       => 'customer',
                'is_active'  => 1,
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(3),
            ],

            // ── Customers without phone numbers (tests "Belum diisi" display) ──

            [
                'name'       => 'Bambang Suryadi',
                'email'      => 'bambang.suryadi@hotmail.com',
                'phone'      => null,
                'role'       => 'customer',
                'is_active'  => 1,
                'created_at' => Carbon::now()->subMonths(5)->subDays(10),
                'updated_at' => Carbon::now()->subMonths(5)->subDays(10),
            ],
            [
                'name'       => 'Wulan Permata',
                'email'      => 'wulan.permata@gmail.com',
                'phone'      => null,
                'role'       => 'customer',
                'is_active'  => 1,
                'created_at' => Carbon::now()->subMonths(2)->subDays(5),
                'updated_at' => Carbon::now()->subMonths(2)->subDays(5),
            ],

            // ── Blocked customers — tests the "Diblokir" badge and blocked stat ──

            [
                'name'       => 'Eko Susanto',
                'email'      => 'eko.susanto@gmail.com',
                'phone'      => '081567890123',
                'role'       => 'customer',
                'is_active'  => 0,
                'created_at' => Carbon::now()->subMonths(9)->subDays(7),
                'updated_at' => Carbon::now()->subMonths(1),
            ],
            [
                'name'       => 'Citra Lestari',
                'email'      => 'citra.lestari@gmail.com',
                'phone'      => '082678901234',
                'role'       => 'customer',
                'is_active'  => 0,
                'created_at' => Carbon::now()->subMonths(6)->subDays(3),
                'updated_at' => Carbon::now()->subWeeks(2),
            ],
            [
                'name'       => 'Fajar Nugroho',
                'email'      => 'fajar.nugroho@yahoo.com',
                'phone'      => '083789012345',
                'role'       => 'customer',
                'is_active'  => 0,
                'created_at' => Carbon::now()->subMonths(4)->subDays(15),
                'updated_at' => Carbon::now()->subMonths(1)->subDays(5),
            ],
        ];
    }
}
