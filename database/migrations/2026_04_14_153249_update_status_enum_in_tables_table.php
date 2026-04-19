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
        Schema::table('tables', function (Blueprint $table) {
            //
        });

        \DB::statement("ALTER TABLE tables MODIFY COLUMN status ENUM('available', 'occupied', 'nonaktif') DEFAULT 'available'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            //
        });

        \DB::statement("ALTER TABLE tables MODIFY COLUMN status ENUM('available', 'occupied') DEFAULT 'available'");
    }
};
