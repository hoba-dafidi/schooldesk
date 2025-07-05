<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminRoleSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
public function run()
{
    $adminRole = Role::firstOrCreate(['name' => 'admin']);

    $user = User::updateOrCreate(
        ['email' => 'djayfal@gmail.com']
    );

    $user->assignRole($adminRole);
}

}
