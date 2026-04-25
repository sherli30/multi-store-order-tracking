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
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['price', 'stock', 'weight', 'image', 'description']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            // Drop foreign key and column for product_id
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->decimal('weight', 10, 2)->default(0);
            $table->string('image')->nullable();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade');
        });
    }
};
