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
        Schema::table('stores', function (Blueprint $table) {
            $table->unsignedBigInteger('province_id')->nullable()->after('address');
            $table->unsignedBigInteger('city_id')->nullable()->after('province_id');

            // Foreign keys
            // $table->foreign('province_id')->references('id')->on('provinces')->onDelete('set null');
            // $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['province_id', 'city_id']);
        });
    }
};
