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
        Schema::table('shipping_rates', function (Blueprint $table) {
            $table->foreignId('origin_province_id')->nullable()->constrained('provinces')->nullOnDelete();
            $table->foreignId('destination_province_id')->nullable()->constrained('provinces')->nullOnDelete();
            $table->decimal('min_weight', 8, 2)->default(1.00);
            $table->decimal('max_weight', 8, 2)->nullable();
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_rates', function (Blueprint $table) {
            $table->dropForeign(['origin_province_id']);
            $table->dropForeign(['destination_province_id']);
            $table->dropColumn(['origin_province_id', 'destination_province_id', 'min_weight', 'max_weight', 'is_active']);
        });
    }
};
