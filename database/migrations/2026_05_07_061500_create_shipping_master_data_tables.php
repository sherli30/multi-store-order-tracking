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
        // 1. Couriers Table
        Schema::create('couriers', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('name');
            $blueprint->string('code')->unique(); // e.g. jnt, jne, sicepat, internal
            $blueprint->string('logo')->nullable();
            $blueprint->boolean('is_active')->default(true);
            $blueprint->timestamps();
        });

        // 2. Shipping Services Table (Reguler, Cargo, etc.)
        Schema::create('shipping_services', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('courier_id')->constrained()->onDelete('cascade');
            $blueprint->string('service_name'); // e.g. Reguler, Cargo, OKE, YES
            $blueprint->integer('min_weight')->default(0); // in grams
            $blueprint->text('description')->nullable();
            $blueprint->boolean('is_active')->default(true);
            $blueprint->timestamps();
        });

        // 3. Shipping Rates Table
        Schema::create('shipping_rates', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('shipping_service_id')->constrained()->onDelete('cascade');
            $blueprint->unsignedBigInteger('origin_city_id');
            $blueprint->unsignedBigInteger('destination_city_id');
            $blueprint->decimal('cost_per_kg', 12, 2);
            $blueprint->integer('etd_min')->nullable(); // Estimated Time of Delivery (Min Days)
            $blueprint->integer('etd_max')->nullable(); // Estimated Time of Delivery (Max Days)
            $blueprint->timestamps();

            // Foreign keys to cities (assuming cities table exists)
            // $blueprint->foreign('origin_city_id')->references('id')->on('cities')->onDelete('cascade');
            // $blueprint->foreign('destination_city_id')->references('id')->on('cities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');
        Schema::dropIfExists('shipping_services');
        Schema::dropIfExists('couriers');
    }
};
