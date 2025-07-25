<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $defaultSettings = [
            'tax_percentage' => '7.5',
            'app_name' => 'SchoolDesk',
            'support_email' => 'support@schooldesk.com',
            'default_currency' => 'NGN',
            'company_name' => 'Lofty TreeApps Studio',
            'company_address' => '123 Adeyemi St, Lagos, Nigeria',
            'invoice_prefix' => 'INV-',
            'invoice_terms' => 'Payment due within 7 days.',
            'email_from_name' => 'SchoolDesk Support',
            'email_from_address' => 'no-reply@schooldesk.com',
            'homepage_url' => 'https://schooldesk.com',
            'app_logo_url' => '/assets/logo.png',
            'terms_url' => 'https://schooldesk.com/terms',
            'privacy_url' => 'https://schooldesk.com/privacy',
            'timezone' => 'Africa/Lagos',
            'app_logo_url' => '/assets/logo.png',
            'favicon_url' => '/assets/logo.png',
            'background_url' => '/assets/logo.png',
            'watermark_url' => '/assets/logo.png',
        ];

        foreach ($defaultSettings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        // Setting::set('tax_percentage', 7.5); // You can adjust the default
    }
}
