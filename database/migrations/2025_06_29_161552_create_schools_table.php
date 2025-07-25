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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Changed to user_id as per model
            $table->string('name')->unique();
            $table->enum('status', ['pending', 'active', 'suspended', 'deleted'])->default('pending');
            $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();
            $table->boolean('is_paid_plan')->default(false); // Indicates if they are on a paid plan (e.g., custom domain)
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('logo')->nullable(); // Store path to logo image
            $table->string('type')->nullable(); // e.g., 'Primary', 'Secondary', 'University'
            $table->year('established_year')->nullable();
            $table->text('about')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
