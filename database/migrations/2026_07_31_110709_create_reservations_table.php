<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use IlluminateStyle\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint as BlueprintSchema;
use Illuminate\Support\Facades\Schema as SchemaFacade;

return new class extends Migration
{
    public function up(): void
    {
        SchemaFacade::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('table_id')->constrained()->cascadeOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->dateTime('reservation_time');
            $table->integer('party_size');
            $table->string('status')->default('pending'); // pending, confirmed, cancelled
            $table->timestamps();
        });
    }

    public function down(): void
    {
        SchemaFacade::dropIfExists('reservations');
    }
};
