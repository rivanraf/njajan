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
        Schema::table('orders', function (Blueprint $table) {
            // 1. Cek device_id
            if (!Schema::hasColumn('orders', 'device_id')) {
                $table->string('device_id')->nullable()->index()->after('id');
            }
            
            // 2. Cek total_price
            if (!Schema::hasColumn('orders', 'total_price')) {
                $table->decimal('total_price', 12, 2)->default(0)->after('status');
            }
            
            // 3. Cek timestamps (created_at & updated_at)
            // Karena $table->timestamps() membuat dua kolom, kita cek salah satunya
            if (!Schema::hasColumn('orders', 'created_at')) {
                $table->timestamps(); 
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Biarkan kosong agar tidak merusak data saat rollback
        });
    }
};