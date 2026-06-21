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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Total amount for the entire group (subtotals + shipping costs)
            $table->integer('total_product_amount')->default(0);
            $table->integer('total_shipping_amount')->default(0);
            $table->integer('grand_total')->default(0);
            
            // Midtrans / Payment Tracking
            $table->string('payment_status')->default('pending');
            $table->string('payment_type')->nullable();
            $table->string('midtrans_order_id')->nullable()->unique();
            $table->string('snap_token')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
