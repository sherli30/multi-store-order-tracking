<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add webhook failure tracking table
        Schema::create('webhook_failures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('webhook_type')->default('midtrans_payment'); // payment, stock, etc.
            $table->string('failure_reason');
            $table->json('payload')->nullable();
            $table->integer('attempt_count')->default(1);
            $table->timestamp('first_failed_at');
            $table->timestamp('last_failed_at');
            $table->boolean('resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'resolved']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_failures');
    }
};
