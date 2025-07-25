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
        Schema::create('subscriptions', function (Blueprint $table) {
        $table->id();

        // Foreign keys
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('plan_id')->constrained()->onDelete('cascade');
        $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
        $table->foreignId('payment_gateway_id')->nullable()->constrained()->nullOnDelete();

        // Subscription details
        $table->enum('status', ['active', 'trialing','pending', 'cancelled', 'expired'])->default('pending'); // active, cancelled, expired, pending, trialing, etc.
        $table->unsignedInteger('quantity')->default(1);

        $table->timestamp('starts_at')->nullable();
        $table->timestamp('trial_ends_at')->nullable();
        $table->timestamp('grace_ends_at')->nullable(); // optional: for grace period after cancel
        $table->timestamp('ends_at')->nullable(); // when it fully expires (after cancel)

        $table->timestamp('renews_at')->nullable(); // next renewal date (for recurring)
        $table->timestamp('cancelled_at')->nullable(); // when user/admin cancelled

        // Payment/Reference
        $table->string('external_reference')->nullable(); // gateway ID / transaction ref
        $table->text('meta')->nullable(); // JSON blob for logs, receipt, raw gateway response

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
