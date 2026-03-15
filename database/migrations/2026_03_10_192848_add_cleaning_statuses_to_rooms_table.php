<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            DB::statement("ALTER TABLE rooms MODIFY COLUMN status ENUM('available', 'occupied', 'maintenance', 'reserved', 'dirty', 'cleaning') DEFAULT 'available'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            DB::statement("ALTER TABLE rooms MODIFY COLUMN status ENUM('available', 'occupied', 'maintenance', 'reserved') DEFAULT 'available'");
        });
    }
};
