<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_number')->unique();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('room_charge', 10, 2)->default(0);
            $table->decimal('kitchen_charge', 10, 2)->default(0);
            $table->decimal('laundry_charge', 10, 2)->default(0);
            $table->decimal('other_charges', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);
            $table->enum('status', ['draft', 'issued', 'partial', 'paid', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained()->cascadeOnDelete();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reference')->unique();
            $table->decimal('amount', 10, 2);
            $table->enum('method', ['cash', 'mobile_money', 'card', 'bank_transfer'])->default('cash');
            $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('bills');
    }
};
