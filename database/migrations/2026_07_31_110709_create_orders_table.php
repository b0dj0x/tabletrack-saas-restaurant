<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('table_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('waiter_id')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->string('status')->default('pending'); // pending, preparing, served, completed, cancelled
            $table->string('payment_status')->default('pending'); // pending, paid, partially_paid
            $table->string('payment_method')->nullable(); // cash, baridimob
            $table->decimal('total_price', 8, 2)->default(0.00);
            $table->string('type')->default('dine_in'); // dine_in, takeaway, delivery
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
