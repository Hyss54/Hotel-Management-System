<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('laundry_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('order_number')->unique();
            $table->text('notes')->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'in_progress', 'done', 'delivered', 'cancelled'])->default('pending');
            $table->date('due_date')->nullable();
            $table->timestamps();
        });

        Schema::create('laundry_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laundry_order_id')->constrained()->cascadeOnDelete();
            $table->string('item_name');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_order_items');
        Schema::dropIfExists('laundry_orders');
    }
};
