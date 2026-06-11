<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Restore columns on products table
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'price')) {
                $table->decimal('price', 15, 2)->default(0)->after('name');
            }
            if (!Schema::hasColumn('products', 'stock')) {
                $table->integer('stock')->default(0)->after('price');
            }
            if (!Schema::hasColumn('products', 'weight')) {
                $table->decimal('weight', 10, 2)->default(0)->after('stock')->comment('Weight in grams');
            }
            if (!Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->nullable()->after('weight');
            }
        });

        // 2. Add product_id back to order_items and stock_movements if they don't exist
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'product_id')) {
                $table->foreignId('product_id')->nullable()->after('id')->constrained('products')->onDelete('set null');
            }
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_movements', 'product_id')) {
                $table->foreignId('product_id')->nullable()->after('id')->constrained('products')->onDelete('cascade');
            }
        });

        // 3. Migrate data from product_variants to products, order_items, and stock_movements
        // Since product_variants is still present, we can read from it
        if (Schema::hasTable('product_variants')) {
            // Re-populate products table from product_variants
            $variants = DB::table('product_variants')->orderBy('id')->get();
            $updatedProducts = [];

            foreach ($variants as $variant) {
                // If we haven't updated the product yet, or if this is the 'Default' variant, use it
                if (!in_array($variant->product_id, $updatedProducts) || $variant->name === 'Default') {
                    DB::table('products')
                        ->where('id', $variant->product_id)
                        ->update([
                            'price' => $variant->price,
                            'stock' => $variant->stock,
                            'weight' => $variant->weight,
                            'sku' => $variant->sku,
                        ]);
                    $updatedProducts[] = $variant->product_id;
                }
            }

            // Re-populate order_items.product_id from order_items.product_variant_id -> product_variants.product_id
            if (Schema::hasColumn('order_items', 'product_variant_id')) {
                DB::table('order_items')
                    ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
                    ->update(['order_items.product_id' => DB::raw('product_variants.product_id')]);
            }

            // Re-populate stock_movements.product_id from stock_movements.product_variant_id -> product_variants.product_id
            if (Schema::hasColumn('stock_movements', 'product_variant_id')) {
                DB::table('stock_movements')
                    ->join('product_variants', 'stock_movements.product_variant_id', '=', 'product_variants.id')
                    ->update(['stock_movements.product_id' => DB::raw('product_variants.product_id')]);
            }
        }

        // 4. Drop product_variant_id foreign keys and columns
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'product_variant_id')) {
                $table->dropForeign(['product_variant_id']);
                $table->dropColumn('product_variant_id');
            }
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            if (Schema::hasColumn('stock_movements', 'product_variant_id')) {
                $table->dropForeign(['product_variant_id']);
                $table->dropColumn('product_variant_id');
            }
        });

        // 5. Drop tables product_variants and packing_options
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('packing_options');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-creating the tables or columns in case of rollback is optional, but let's provide a basic down() for completeness
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('name');
            $table->decimal('price', 15, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->decimal('weight', 10, 2)->default(0);
            $table->string('sku')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('packing_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('name');
            $table->decimal('price', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('set null');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');
        });
    }
};
