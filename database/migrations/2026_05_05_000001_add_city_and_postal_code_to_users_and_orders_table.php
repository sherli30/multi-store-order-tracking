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
        Schema::table('users', function (Blueprint $table) {
            $table->string('city')->nullable()->after('address');
            $table->string('postal_code', 10)->nullable()->after('city');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('city')->nullable()->after('shipping_address');
            $table->string('postal_code', 10)->nullable()->after('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['city', 'postal_code']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['city', 'postal_code']);
        });
    }
};
