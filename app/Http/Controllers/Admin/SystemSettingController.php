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
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('admin.settings.index', compact('settings'));
    }



    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'nullable|string|max:255',
            'support_email' => 'nullable|email|max:255',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',

            'app_logo_file' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',        // 1MB
            'favicon_file' => 'nullable|image|mimes:ico,png|max:512',              // 512KB
            'background_file' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',     // 2MB
            'watermark_file' => 'nullable|image|mimes:png|max:1024',               // 1MB
        ]);

        $data = $request->except(['_token']);

        $fileFields = [
            'app_logo_file' => 'app_logo_url',
            'favicon_file' => 'favicon_url',
            'background_file' => 'background_url',
            'watermark_file' => 'watermark_url',
        ];

        foreach ($fileFields as $uploadField => $settingKey) {
            $removeKey = 'remove_' . $settingKey;

            // Remove file if checkbox is ticked
            if ($request->has($removeKey)) {
                $existing = Setting::where('key', $settingKey)->first();
                if ($existing && $existing->value) {
                    Storage::disk('public')->delete($existing->value);
                }
                Setting::updateOrCreate(['key' => $settingKey], ['value' => null]);
            }

            // Upload new file
            if ($request->hasFile($uploadField)) {
                $file = $request->file($uploadField);
                $path = $file->store('settings', 'public');
                Setting::updateOrCreate(['key' => $settingKey], ['value' => $path]);
            }
        }

        // Save remaining settings
        foreach ($data as $key => $value) {
            if (!Str::startsWith($key, ['remove_', 'app_logo_file', 'favicon_file', 'background_file', 'watermark_file'])) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            }
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }


}
