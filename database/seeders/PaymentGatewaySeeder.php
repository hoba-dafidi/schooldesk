<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentGateway;

class PaymentGatewaySeeder extends Seeder
{
    public function run()
    {
        // Delete existing entries (optional)
        PaymentGateway::query()->delete(); // ✅ Deletes all records respecting foreign keys

        // Example: Paystack
        PaymentGateway::create([
            'name' => 'Paystack',
            'slug' => 'paystack',
            'is_active' => true,
            'description' => 'Paystack Payment Gateway for Nigeria',
            'driver_class' => null, // Optional
            'api_key' => 'pk_test_0099d1b8950f8f494243d9be09752d6096b0317e',
            'api_secret' => 'sk_test_06b966f83698d4753cc58379ae3b82ef24467d72',
            'webhook_secret' => 'webhook_secret_here',
            'merchant_id' => null,
            'currency' => 'NGN',
            'sandbox_mode' => true,
            'logo_url' => 'https://paystack.com/assets/logo.png',
            // 'callback_url' => route('payment.callback', 'paystack'),
            // 'webhook_url' => route('payment.webhook', 'paystack'),
            'config' => [
                'api_version' => 'v1',
            ],
        ]);

        // Example: Flutterwave
        PaymentGateway::create([
            'name' => 'Flutterwave',
            'slug' => 'flutterwave',
            'is_active' => false,
            'description' => 'Flutterwave Payment Gateway',
            'driver_class' => null,
            'api_key' => 'FLW_PUBLIC_KEY',
            'api_secret' => 'FLW_SECRET_KEY',
            'webhook_secret' => 'webhook_secret_here',
            'merchant_id' => 'FLW_MERCHANT_ID',
            'currency' => 'NGN',
            'sandbox_mode' => true,
            'logo_url' => 'https://flutterwave.com/images/logo.png',
            // 'callback_url' => route('payment.callback', 'flutterwave'),
            // 'webhook_url' => route('payment.webhook', 'flutterwave'),
            'config' => [
                'api_version' => 'v3',
            ],
        ]);
    }
}
