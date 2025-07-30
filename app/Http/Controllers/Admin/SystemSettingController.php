<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SystemSettingController extends Controller
{
    public function index()
    {
        // $settings = Setting::pluck('value', 'key')->toArray();
        $settings = Setting::all();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            // General Settings
            'app_name' => 'nullable|string|max:255',
            'support_email' => 'nullable|email|max:255',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'default_currency' => 'nullable|string|max:5',
            'timezone' => 'nullable|string|max:64',

            // Company
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string|max:255',

            // Invoice
            'invoice_prefix' => 'nullable|string|max:20',
            'invoice_terms' => 'nullable|string|max:255',

            // Email
            'email_from_name' => 'nullable|string|max:255',
            'email_from_address' => 'nullable|email|max:255',

            // Branding File Uploads
            'app_logo_file' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'favicon_file' => 'nullable|image|mimes:ico,png|max:512',
            'background_file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'watermark_file' => 'nullable|image|mimes:png|max:1024',

            // Affiliate/MLM
            'affiliate_commission_percentage' => 'nullable|numeric|min:0|max:100',
            'commission_levels' => 'array',
            'commission_levels.*' => 'nullable|numeric|min:0|max:100', // Each level percentage

            // URLs
            'homepage_url' => 'nullable|url',
            'terms_url' => 'nullable|url',
            'privacy_url' => 'nullable|url',
        ]);

        $data = $request->except(['_token', 'commission_levels', 'level_keys']);

        // -------------------------------------------
        // Handle branding file uploads and deletions
        // -------------------------------------------
        $fileFields = [
            'app_logo_file' => 'app_logo_url',
            'favicon_file' => 'favicon_url',
            'background_file' => 'background_url',
            'watermark_file' => 'watermark_url',
        ];

        foreach ($fileFields as $uploadField => $settingKey) {
            $removeKey = 'remove_' . $settingKey;

            if ($request->has($removeKey)) {
                $existing = Setting::where('key', $settingKey)->first();
                if ($existing && $existing->value) {
                    Storage::disk('public')->delete($existing->value);
                }
                Setting::updateOrCreate(['key' => $settingKey], ['value' => null]);
            }

            if ($request->hasFile($uploadField)) {
                $file = $request->file($uploadField);
                $path = $file->store('settings', 'public');
                Setting::updateOrCreate(['key' => $settingKey], ['value' => $path]);
            }
        }

        // -------------------------------------------
        // Save dynamic commission_levels as JSON
        // -------------------------------------------
        $levelsInput = $request->input('commission_levels', []);
        $levelsDecimal = [];

        foreach ($levelsInput as $level => $percent) {
            if ($percent === '' || $percent === null) {
                continue;
            }
            $levelsDecimal[(int)$level] = round(((float)$percent) / 100, 4); // Store decimals: 10% → 0.10
        }

        ksort($levelsDecimal);

        Setting::updateOrCreate(['key' => 'affiliate_commission_levels'], [
            'value' => json_encode($levelsDecimal),
        ]);

        // -------------------------------------------
        // Save all remaining settings
        // -------------------------------------------
        foreach ($data as $key => $value) {
            if (!Str::startsWith($key, [
                'remove_', 'app_logo_file', 'favicon_file', 'background_file', 'watermark_file'
            ])) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            }
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

}
