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
      Schema::create('affiliate_commissions', function (Blueprint $table) {
          $table->id();
          $table->foreignId('affiliate_id')->constrained()->onDelete('cascade');
          $table->foreignId('referred_user_id')->constrained('users');
          $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
          $table->decimal('amount', 12, 2);
          $table->string('status')->default('pending'); // pending, approved, paid
          $table->timestamp('paid_at')->nullable();
          $table->timestamps();
      });

      Schema::table('affiliate_commissions', function (Blueprint $table) {
          $table->foreignId('campaign_id')
                ->nullable()
                ->constrained('affiliate_campaigns')
                ->nullOnDelete()
                ->after('affiliate_id');
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_commissions');
    }
};
