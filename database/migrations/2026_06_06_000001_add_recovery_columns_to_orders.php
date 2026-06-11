<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add idempotency and recovery columns to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->string('idempotency_key')->nullable()->after('midtrans_order_id')->unique();
            $table->integer('webhook_attempts')->default(0)->after('payment_status');
            $table->timestamp('last_webhook_attempt')->nullable()->after('webhook_attempts');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['idempotency_key', 'webhook_attempts', 'last_webhook_attempt']);
        });
    }
};
