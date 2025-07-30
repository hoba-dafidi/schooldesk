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
        Schema::create('manual_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');

            $table->string('bank_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('reference')->nullable(); // optional user input
            $table->string('proof_path'); // path to uploaded file
            $table->text('note')->nullable();

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manual_payments');
    }
};
