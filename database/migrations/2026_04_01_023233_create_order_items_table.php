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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            
            // If product is deleted, we still want to keep order item history, so nullable & nullOnDelete
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            
            $table->integer('quantity')->default(1);
            $table->decimal('price', 15, 2); // Snapshot of price at the time of purchase
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
