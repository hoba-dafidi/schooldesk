<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('settings')->insert([
          ['key' => 'tax_percentage', 'value' => null, 'created_at' => $now, 'updated_at' => $now],
          ['key' => 'affiliate_commission_percentage', 'value' => '30', 'created_at' => $now, 'updated_at' => $now],
          ['key' => 'app_name', 'value' => null, 'created_at' => $now, 'updated_at' => $now],
          ['key' => 'support_email', 'value' => null, 'created_at' => $now, 'updated_at' => $now],
          ['key' => 'default_currency', 'value' => 'NGN', 'created_at' => $now, 'updated_at' => $now],
          ['key' => 'company_name', 'value' => null, 'created_at' => $now, 'updated_at' => $now],
          ['key' => 'company_address', 'value' => null, 'created_at' => $now, 'updated_at' => $now],
          ['key' => 'invoice_prefix', 'value' => null, 'created_at' => $now, 'updated_at' => $now],
          ['key' => 'invoice_terms', 'value' => null, 'created_at' => $now, 'updated_at' => $now],
          ['key' => 'email_from_name', 'value' => null, 'created_at' => $now, 'updated_at' => $now],
          ['key' => 'email_from_address', 'value' => null, 'created_at' => $now, 'updated_at' => $now],
          ['key' => 'homepage_url', 'value' => null, 'created_at' => $now, 'updated_at' => $now],
          ['key' => 'app_logo_url', 'value' => '/assets/logo.png', 'created_at' => $now, 'updated_at' => $now],
          ['key' => 'terms_url', 'value' => null, 'created_at' => $now, 'updated_at' => $now],
          ['key' => 'privacy_url', 'value' => null, 'created_at' => $now, 'updated_at' => $now],
          ['key' => 'timezone', 'value' => 'Africa/Lagos', 'created_at' => $now, 'updated_at' => $now],
          ['key' => 'favicon_url', 'value' => '/assets/logo.png', 'created_at' => $now, 'updated_at' => $now],
          ['key' => 'background_url', 'value' => '/assets/logo.png', 'created_at' => $now, 'updated_at' => $now],
          ['key' => 'watermark_url', 'value' => '/assets/logo.png', 'created_at' => $now, 'updated_at' => $now],
          ['key' => 'commission_level_1', 'value' => '10', 'created_at' => $now, 'updated_at' => $now],
          ['key' => 'commission_level_2', 'value' => '5', 'created_at' => $now, 'updated_at' => $now],
          ['key' => 'commission_level_3', 'value' => '2', 'created_at' => $now, 'updated_at' => $now],

          // ✅ Multi-level commission percentages in JSON
          ['key' => 'affiliate_commission_levels', 'value' => json_encode([
              1 => 0.10, // Level 1 = 10%
              2 => 0.05, // Level 2 = 5%
              3 => 0.02, // Level 3 = 2%
          ]), 'created_at' => $now, 'updated_at' => $now],
      ]);

    }
}
