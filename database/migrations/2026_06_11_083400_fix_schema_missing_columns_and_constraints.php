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
        // Add missing slug columns
        Schema::table('stores', function (Blueprint $table) {
            if (!Schema::hasColumn('stores', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('name');
            }
        });

        Schema::table('product_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('product_categories', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('name');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('name');
            }
        });



        // Fix constraints and nullability on identifiers
        Schema::table('orders', function (Blueprint $table) {
            $table->string('midtrans_order_id')->nullable()->change();
            $table->string('tracking_number')->nullable()->unique()->change();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('transaction_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('transaction_id')->nullable(false)->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['tracking_number']);
            $table->string('midtrans_order_id')->nullable(false)->change();
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
