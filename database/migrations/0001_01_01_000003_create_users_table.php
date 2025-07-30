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

            // Referral system
            $table->string('referral_code')->nullable()->unique();
            $table->unsignedBigInteger('referred_by')->nullable();

            // Basic Info
            $table->string('firstname');
            $table->string('middlename')->nullable();
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

            // Multi-Tenancy
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade');

            // Roles (if not using Spatie roles)
            $table->string('role')->default('user');

            // Billing (Laravel Cashier Stripe)
            $table->string('stripe_id')->nullable();
            $table->string('pm_type')->nullable();
            $table->string('pm_last_four')->nullable();
            $table->timestamp('trial_ends_at')->nullable();

            // OAuth
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();

            $table->string('bank_name')->nullable();
            $table->string('bank_code')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();

            // Usage Tracking
            $table->timestamp('last_seen_at')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes(); // Optional
        });

        // Add foreign key for referred_by separately
        Schema::table('users', function (Blueprint $table) {
            // Optional: Add foreign key constraint (self-referencing)
            $table->foreign('referred_by')->references('id')->on('users')->onDelete('set null');
        });

        // Schema::table('users', function (Blueprint $table) {
        //     $table->foreignId('attribution_campaign_id')
        //         ->nullable()
        //         ->after('referred_by')
        //         ->constrained('affiliate_campaigns')
        //         ->nullOnDelete();
        // });

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
      Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropColumn(['referral_code', 'referred_by']);
        });
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('attribution_campaign_id');
        });
    }
};
