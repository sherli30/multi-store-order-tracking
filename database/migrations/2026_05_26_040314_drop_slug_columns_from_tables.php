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
            if (Schema::hasColumn('stores', 'slug')) {
                $table->dropColumn('slug');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'slug')) {
                // Ignore unique constraint error if it's already dropped or missing
                try {
                    $table->dropUnique(['store_id', 'slug']);
                } catch (\Exception $e) {}
                $table->dropColumn('slug');
            }
        });

        Schema::table('product_categories', function (Blueprint $table) {
            if (Schema::hasColumn('product_categories', 'slug')) {
                $table->dropColumn('slug');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->string('slug')->nullable();
            $table->unique(['store_id', 'slug'], 'product_categories_store_id_slug_unique');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('slug')->nullable();
            $table->unique(['store_id', 'slug']);
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->string('slug')->nullable();
            $table->unique('slug');
        });
    }
};
