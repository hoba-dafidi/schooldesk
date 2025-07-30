<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('affiliate_payout_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Affiliate user requesting payout
            $table->decimal('amount', 12, 2);
            $table->string('bank_name');
            $table->string('account_name');
            $table->string('account_number');
            $table->string('status')->default('pending'); // pending, approved, rejected, paid
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_payout_requests');
    }
};
