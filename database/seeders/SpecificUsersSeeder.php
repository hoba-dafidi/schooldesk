<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SpecificUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure roles exist
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Create user
        $user = User::updateOrCreate(
            ['email' => 'hoba.dafidi@gmail.com'],
            [
                'firstname' => 'David',
                'lastname' => 'Adeniyi',
                'password' => Hash::make('lofty2014'),
            ]
        );
        $user->syncRoles([$userRole]);

        // Create admin
        $admin = User::updateOrCreate(
            ['email' => 'djayfal@gmail.com'],
            [
                'firstname' => 'King',
                'lastname' => 'Adeniyi',
                'password' => Hash::make('lofty2014'),
            ]
        );
        $admin->syncRoles([$adminRole]);
    }
}
