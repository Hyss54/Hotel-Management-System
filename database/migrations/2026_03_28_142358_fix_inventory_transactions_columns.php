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
        Schema::table('inventory_transactions', function (Blueprint $table) {
            // Drop old columns if they exist
            if (Schema::hasColumn('inventory_transactions', 'from_state')) {
                $table->dropColumn('from_state');
            }
            if (Schema::hasColumn('inventory_transactions', 'to_state')) {
                $table->dropColumn('to_state');
            }
            if (Schema::hasColumn('inventory_transactions', 'user_id')) {
                $table->dropColumn('user_id');
            }

            // Add new columns if they don't exist
            if (!Schema::hasColumn('inventory_transactions', 'from_department_id')) {
                $table->foreignId('from_department_id')->nullable()->after('laundry_item_id')->constrained('departments');
            }
            if (!Schema::hasColumn('inventory_transactions', 'to_department_id')) {
                $table->foreignId('to_department_id')->nullable()->after('from_department_id')->constrained('departments');
            }
            if (!Schema::hasColumn('inventory_transactions', 'transaction_type')) {
                $table->string('transaction_type')->after('quantity');
            }
            if (!Schema::hasColumn('inventory_transactions', 'status')) {
                $table->string('status')->default('completed')->after('transaction_type');
            }
            if (!Schema::hasColumn('inventory_transactions', 'created_by')) {
                $table->foreignId('created_by')->after('notes')->constrained('users');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropColumn(['from_department_id', 'to_department_id', 'transaction_type', 'status', 'created_by']);
        });
    }
};
