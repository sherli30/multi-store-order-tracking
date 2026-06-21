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
            $table->dropColumn('packing_cost');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('packing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('packing_cost')->default(0)->after('shipping_cost');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('packing')->nullable()->after('price');
        });
    }
};
