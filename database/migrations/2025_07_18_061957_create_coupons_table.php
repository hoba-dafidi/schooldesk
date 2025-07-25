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
        Schema::create('coupons', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique();
        $table->enum('type', ['fixed', 'percentage']); // Fixed amount or percentage
        $table->decimal('value', 10, 2);
        $table->integer('max_uses')->nullable(); // Total times coupon can be used
        $table->integer('uses')->default(0); // Track current usage
        $table->timestamp('expires_at')->nullable(); // Expiration date
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
