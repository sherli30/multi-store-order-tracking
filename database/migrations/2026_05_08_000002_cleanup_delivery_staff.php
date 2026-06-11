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
        // Remove foreign key and column from tracking_histories
        if (Schema::hasColumn('tracking_histories', 'delivery_staff_id')) {
            Schema::table('tracking_histories', function (Blueprint $table) {
                $table->dropConstrainedForeignId('delivery_staff_id');
            });
        }

        // Remove foreign key and column from orders
        if (Schema::hasColumn('orders', 'delivery_staff_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropConstrainedForeignId('delivery_staff_id');
            });
        }

        // Drop delivery_staff table
        Schema::dropIfExists('delivery_staff');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to revert cleanup for this specific request as the user wants it gone.
    }
};
