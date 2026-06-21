<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipment_id')->nullable()->after('tracking_number');
            $table->string('shipment_status')->nullable()->after('shipment_id');
            $table->string('courier_name')->nullable()->after('shipment_status');
            $table->string('courier_service')->nullable()->after('courier_name');
            $table->timestamp('shipment_created_at')->nullable()->after('courier_service');
        });

        // Modifikasi ENUM status untuk menambahkan ready_to_ship dan delivered
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'perlu_diproses', 'processing', 'ready_to_ship', 'shipping', 'delivered', 'completed', 'cancelled', 'refunded') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ENUM status ke aslinya
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'perlu_diproses', 'processing', 'shipping', 'completed', 'cancelled', 'refunded') NOT NULL DEFAULT 'pending'");

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipment_id',
                'shipment_status',
                'courier_name',
                'courier_service',
                'shipment_created_at'
            ]);
        });
    }
};
