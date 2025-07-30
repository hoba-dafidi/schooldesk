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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null'); // Optional link
            $table->foreignId('school_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('plan_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['unpaid', 'paid', 'void', 'refunded', 'pending'])->default('unpaid');
            $table->timestamp('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_gateway_id')->nullable(); // Transaction ID from payment gateway
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->string('reference')->nullable();
            $table->string('payment_method')->nullable();         // e.g., 'manual', 'paystack'
            $table->string('payment_proof_path')->nullable();     // file path
            $table->text('payment_note')->nullable();             // user-provided note

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
