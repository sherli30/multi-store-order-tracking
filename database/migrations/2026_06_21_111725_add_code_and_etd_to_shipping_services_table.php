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
        Schema::table('shipping_services', function (Blueprint $table) {
            $table->string('service_code')->nullable()->after('service_name');
            $table->string('estimated_delivery')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_services', function (Blueprint $table) {
            //
        });
    }
};
