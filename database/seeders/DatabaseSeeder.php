<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    // User::factory(10)->create();

    Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'support', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'sales', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'developer', 'guard_name' => 'web']);

     $this->call(SpecificUsersSeeder::class);
    $this->call(AdminRoleSeeder::class);
    $this->call(SchoolSeeder::class);
    $this->call(SettingSeeder::class);
    $this->call([
        PlanSeeder::class,
    ]);

    $this->call([
        PaymentGatewaySeeder::class,
    ]);

    // User::factory()->create([
    //   'name' => 'Test User',
    //   'email' => 'test@example.com',
    // ]);
  }
}
