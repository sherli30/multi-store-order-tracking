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
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'transaction_code')) {
                $table->string('transaction_code')->unique()->nullable()->after('id');
            }
        });

        // Seed existing rows with TRX-YYYYMMDD-XXXXX
        $transactions = DB::table('transactions')->whereNull('transaction_code')->get();
        foreach ($transactions as $transaction) {
            $date = date('Ymd', strtotime($transaction->created_at ?? now()));
            $code = 'TRX-' . $date . '-' . str_pad($transaction->id, 5, '0', STR_PAD_LEFT);
            DB::table('transactions')->where('id', $transaction->id)->update(['transaction_code' => $code]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'transaction_code')) {
                $table->dropUnique(['transaction_code']);
                $table->dropColumn('transaction_code');
            }
        });
    }
};
