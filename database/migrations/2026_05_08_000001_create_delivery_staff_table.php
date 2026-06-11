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
        if (!Schema::hasTable('delivery_staff')) {
            Schema::create('delivery_staff', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->enum('type', ['petugas', 'kurir'])->default('petugas'); // petugas = warehouse/admin, kurir = delivery person
                $table->string('phone')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        } else {
            Schema::table('delivery_staff', function (Blueprint $table) {
                if (!Schema::hasColumn('delivery_staff', 'type')) {
                    $table->enum('type', ['petugas', 'kurir'])->default('petugas')->after('name');
                }
                if (!Schema::hasColumn('delivery_staff', 'phone')) {
                    $table->string('phone')->nullable()->after('type');
                }
                if (!Schema::hasColumn('delivery_staff', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('phone');
                }
            });
        }

        // Add delivery_staff_id to tracking_histories to track who performed the action
        if (!Schema::hasColumn('tracking_histories', 'delivery_staff_id')) {
            Schema::table('tracking_histories', function (Blueprint $table) {
                $table->foreignId('delivery_staff_id')->nullable()->constrained('delivery_staff')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tracking_histories', function (Blueprint $table) {
            $table->dropConstrainedForeignId('delivery_staff_id');
        });
        Schema::dropIfExists('delivery_staff');
    }
};
