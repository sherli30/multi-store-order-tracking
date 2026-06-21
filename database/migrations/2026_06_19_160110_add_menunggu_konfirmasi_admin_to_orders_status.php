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
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'menunggu_konfirmasi_admin', 'perlu_diproses', 'processing', 'ready_to_ship', 'shipping', 'delivered', 'completed', 'cancelled', 'refunded') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'perlu_diproses', 'processing', 'ready_to_ship', 'shipping', 'delivered', 'completed', 'cancelled', 'refunded') NOT NULL DEFAULT 'pending'");
    }
};
