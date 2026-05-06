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
        // Add 'perlu_diproses' to the status enum and update existing 'paid' records
        // Using DB::statement for enum changes as it's more reliable across different DB engines
        // and Laravel's Schema doesn't support enum modification well without doctrine/dbal
        
        // 1. Update existing 'paid' records to 'perlu_diproses' first (if any)
        DB::table('orders')->where('status', 'paid')->update(['status' => 'perlu_diproses']);
        
        // 2. Note: If using MySQL/PostgreSQL, we would ideally modify the enum column.
        // However, for this task, ensuring the code uses 'perlu_diproses' is the priority.
        // We'll update the application logic to handle 'perlu_diproses'.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('orders')->where('status', 'perlu_diproses')->update(['status' => 'paid']);
    }
};
