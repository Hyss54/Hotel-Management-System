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
        Schema::table('laundry_items', function (Blueprint $table) {
            $table->integer('dirty_quantity')->default(0)->after('stock_quantity');
            $table->integer('in_housekeeping_quantity')->default(0)->after('dirty_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laundry_items', function (Blueprint $table) {
            $table->dropColumn(['dirty_quantity', 'in_housekeeping_quantity']);
        });
    }
};
