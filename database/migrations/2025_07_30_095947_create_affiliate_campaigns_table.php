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
        Schema::create('affiliate_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Campaign name
            $table->string('slug')->unique(); // e.g., summer-sale-2025
            $table->string('referral_code')->unique(); // Unique campaign code
            $table->unsignedBigInteger('clicks')->default(0);
            $table->unsignedBigInteger('unique_signups')->default(0);
            $table->decimal('custom_commission_rate', 5, 2)->nullable(); // override commission rate
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_campaigns');
    }
};
