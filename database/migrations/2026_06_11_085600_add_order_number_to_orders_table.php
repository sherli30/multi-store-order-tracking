<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'order_number')) {
                $table->string('order_number')->unique()->nullable()->after('id');
            }
        });

        // Seed existing rows with ORD-YYYYMMDD-XXXXX
        $orders = DB::table('orders')->whereNull('order_number')->get();
        foreach ($orders as $order) {
            $date = date('Ymd', strtotime($order->created_at ?? now()));
            $code = 'ORD-' . $date . '-' . str_pad($order->id, 5, '0', STR_PAD_LEFT);
            DB::table('orders')->where('id', $order->id)->update(['order_number' => $code]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'order_number')) {
                $table->dropUnique(['order_number']);
                $table->dropColumn('order_number');
            }
        });
    }
};
