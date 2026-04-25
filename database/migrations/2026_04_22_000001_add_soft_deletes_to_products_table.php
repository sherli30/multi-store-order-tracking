<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add soft delete support to the products table.
 * Existing queries are NOT affected unless they explicitly call ->withTrashed().
 * The Product model's SoftDeletes trait will automatically exclude deleted rows.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Only add if the column doesn't already exist (safe re-run guard)
            if (!Schema::hasColumn('products', 'deleted_at')) {
                $table->softDeletes()->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
