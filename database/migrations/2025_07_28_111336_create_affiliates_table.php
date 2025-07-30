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
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('referral_code')->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('clicks')->default(0);
            $table->unsignedBigInteger('signups')->default(0);
            $table->decimal('custom_commission_rate', 5, 2)->nullable(); // e.g. 15% override
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliates');
    }
};
