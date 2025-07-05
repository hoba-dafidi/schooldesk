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
        Schema::create('payment_gateways', function (Blueprint $table) {
          $table->id();
          $table->string('name');
          $table->string('slug')->unique();
          $table->string('description')->nullable();
          $table->string('driver_class')->nullable();
          $table->string('api_key')->nullable();
          $table->string('api_secret')->nullable();
          $table->string('webhook_secret')->nullable();
          $table->string('merchant_id')->nullable();
          $table->string('currency', 5)->default('NGN');
          $table->boolean('sandbox_mode')->default(true);
          $table->string('logo_url')->nullable();
          $table->string('callback_url')->nullable();
          $table->string('webhook_url')->nullable();
          $table->json('config')->nullable();
          $table->boolean('is_active')->default(true);
          $table->timestamps();
      });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
