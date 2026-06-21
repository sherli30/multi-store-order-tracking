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
        // Alter columns to DECIMAL(8,2) using raw SQL for compatibility
        DB::statement('ALTER TABLE products MODIFY weight DECIMAL(8,2) DEFAULT 1.00');
        DB::statement('ALTER TABLE shipping_services MODIFY min_weight DECIMAL(8,2) DEFAULT 0.00');

        // Convert existing data from grams to kilograms
        DB::statement('UPDATE products SET weight = weight / 1000.0');
        DB::statement('UPDATE shipping_services SET min_weight = min_weight / 1000.0');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('UPDATE products SET weight = weight * 1000');
        DB::statement('UPDATE shipping_services SET min_weight = min_weight * 1000');
        
        DB::statement('ALTER TABLE products MODIFY weight INT DEFAULT 1000');
        DB::statement('ALTER TABLE shipping_services MODIFY min_weight INT DEFAULT 0');
    }
};
