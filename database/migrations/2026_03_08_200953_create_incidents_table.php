<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('severity')->default('medium'); // low, medium, high, emergency
            $table->string('status')->default('open'); // open, resolved, escalated
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
