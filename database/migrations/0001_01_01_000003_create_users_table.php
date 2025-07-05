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
        Schema::create('users', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Basic Info
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email')->unique();
            $table->string('password');

            // Email Verification
            $table->timestamp('email_verified_at')->nullable();

            // Remember Me Token
            $table->rememberToken();

            // Profile Info
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->string('timezone')->default('UTC');

            // Security & Access
            $table->boolean('is_active')->default(true);
            $table->boolean('is_admin')->default(false);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->integer('login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();

            // Multi-Tenancy (Assuming a 'tenants' table exists or will be created)
            // You'll need to create a `create_tenants_table` migration if you don't have one.
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');

            // Roles (if not using Spatie package)
            $table->string('role')->default('user'); // e.g., 'user', 'admin', 'super_admin'

            // Billing (Laravel Cashier Stripe)
            $table->string('stripe_id')->nullable();
            $table->string('pm_type')->nullable();
            $table->string('pm_last_four')->nullable();
            $table->timestamp('trial_ends_at')->nullable();

            // OAuth (optional)
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();

            // Usage Tracking
            $table->timestamp('last_seen_at')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes(); // optional, for soft deletes
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
