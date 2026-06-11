<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Admin actions audit table for all non-order entity changes
        Schema::create('admin_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('entity_type'); // 'Product', 'Store', 'Courier', 'Category', etc.
            $table->unsignedBigInteger('entity_id')->nullable(); // ID of the entity changed
            $table->string('action_type'); // 'create', 'update', 'delete', 'stock_adjustment', etc.
            $table->json('old_values')->nullable(); // Before state
            $table->json('new_values')->nullable(); // After state
            $table->text('reason')->nullable(); // Why the change was made
            $table->string('ip_address')->nullable(); // IP address of requester
            $table->string('user_agent')->nullable(); // User agent string
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->index(['admin_id', 'created_at']);
            $table->index('created_at');
        });

        // Enhance TrackingHistory for payment/refund details
        Schema::table('tracking_histories', function (Blueprint $table) {
            $table->string('refund_method')->nullable()->after('notes'); // 'manual', 'webhook', 'api'
            $table->string('refund_reason')->nullable()->after('refund_method');
            $table->string('payment_method')->nullable()->after('refund_reason');
            $table->json('metadata')->nullable()->after('payment_method'); // Extra context
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_actions');
        Schema::table('tracking_histories', function (Blueprint $table) {
            $table->dropColumn(['refund_method', 'refund_reason', 'payment_method', 'metadata']);
        });
    }
};
