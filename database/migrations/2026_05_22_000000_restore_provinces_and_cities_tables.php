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
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained('provinces')->onDelete('cascade');
            $table->string('name');
            $table->string('type')->nullable();
            $table->string('postal_code')->nullable();
            $table->timestamps();
        });

        Schema::table('stores', function (Blueprint $table) {
            if (Schema::hasColumn('stores', 'province_id')) {
                $table->foreign('province_id')->references('id')->on('provinces')->onDelete('set null');
            }
            if (Schema::hasColumn('stores', 'city_id')) {
                $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
            }
        });

        Schema::table('shipping_rates', function (Blueprint $table) {
            if (Schema::hasColumn('shipping_rates', 'origin_city_id')) {
                $table->foreign('origin_city_id')->references('id')->on('cities')->onDelete('cascade');
            }
            if (Schema::hasColumn('shipping_rates', 'destination_city_id')) {
                $table->foreign('destination_city_id')->references('id')->on('cities')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_rates', function (Blueprint $table) {
            if (Schema::hasColumn('shipping_rates', 'origin_city_id')) {
                $table->dropForeign(['origin_city_id']);
            }
            if (Schema::hasColumn('shipping_rates', 'destination_city_id')) {
                $table->dropForeign(['destination_city_id']);
            }
        });

        Schema::table('stores', function (Blueprint $table) {
            if (Schema::hasColumn('stores', 'province_id')) {
                $table->dropForeign(['province_id']);
            }
            if (Schema::hasColumn('stores', 'city_id')) {
                $table->dropForeign(['city_id']);
            }
        });

        Schema::dropIfExists('cities');
        Schema::dropIfExists('provinces');
    }
};
