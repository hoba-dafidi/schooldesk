<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing users with role "user" (clients)
        $users = User::whereHas('roles', fn ($q) => $q->where('name', 'user'))->pluck('id')->toArray();
        $plans = Plan::pluck('id')->toArray();

        // Create fake schools
        \App\Models\School::factory(20)->create([
            'user_id'      => fn () => fake()->randomElement($users),
            'plan_id'      => fn () => fake()->randomElement($plans),
            'status'       => fn () => fake()->randomElement(['pending', 'active', 'suspended', 'deleted']),
            'is_paid_plan' => fn () => fake()->boolean(70),
        ]);
    }
}
