<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'price' => 0,
                'currency' => 'NGN',
                'description' => 'Basic free plan with limited access',
                'features' => ['1 User', 'Basic Support'],
                'duration_days' => 365, // ✅ Corrected
                'is_active' => true,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'price' => 2999.99,
                'currency' => 'NGN',
                'description' => 'Professional plan with more features',
                'features' => ['5 Users', 'Priority Support', 'Custom Reports'],
                'duration_days' => 90, // ✅ Corrected
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'price' => 9999.99,
                'currency' => 'NGN',
                'description' => 'Enterprise-level plan for organizations',
                'features' => ['Unlimited Users', '24/7 Support', 'Custom Integrations'],
                'duration_days' => 365,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
