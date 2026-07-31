<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // owner/admin
            $table->foreignId('subscription_package_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subscription_status')->default('active'); // active, expired, trialing
            $table->timestamp('subscription_expires_at')->nullable();
            
            // Restaurant customization/details
            $table->string('logo')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('baridimob_rip')->nullable(); // RIP account number for manual Algerian post transfers
            $table->string('baridimob_qr')->nullable(); // optional uploaded QR image of their account
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
