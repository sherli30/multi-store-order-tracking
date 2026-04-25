<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * This migration was previously empty (1 byte).
 * It safely ensures store_id exists on product_categories.
 *
 * The column was already added inline in the 2026_04_07 migration,
 * but that migration lacked proper foreign key & index definitions.
 * This migration completes those safely using hasColumn() guards.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {

            // Guard: only add store_id if it doesn't already exist
            if (!Schema::hasColumn('product_categories', 'store_id')) {
                $table->foreignId('store_id')
                      ->nullable()
                      ->after('id')
                      ->constrained('stores')
                      ->nullOnDelete();
            }

            // Guard: only add index if the column now exists
            if (Schema::hasColumn('product_categories', 'store_id')) {
                // Composite index for store-scoped active queries
                // (avoids duplicate if already exists — index name is explicit)
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $existingIndexes = array_keys(
                    $sm->listTableIndexes('product_categories')
                );

                if (!in_array('product_categories_store_id_is_active_index', $existingIndexes)) {
                    $table->index(['store_id', 'is_active'], 'product_categories_store_id_is_active_index');
                }

                // Store-scoped unique slug (if not already there)
                $slugIndexName = 'product_categories_store_id_slug_unique';
                if (!in_array($slugIndexName, $existingIndexes) &&
                    Schema::hasColumn('product_categories', 'slug')) {
                    $table->unique(['store_id', 'slug'], $slugIndexName);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            if (Schema::hascolumn('product_categories', 'store_id')) {
                $table->dropForeign(['store_id']);
                $table->dropColumn('store_id');
            }
        });
    }
};
