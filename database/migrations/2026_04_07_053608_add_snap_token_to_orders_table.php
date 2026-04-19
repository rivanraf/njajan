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
            // TARUH DI SINI: Menambahkan kolom snap_token setelah total_price (opsional after-nya)
            $table->string('snap_token')->nullable()->after('total_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // TARUH DI SINI: Menghapus kolom jika migration di-cancel
            $table->dropColumn('snap_token');
        });
    }
};