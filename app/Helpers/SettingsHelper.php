<?php

use App\Models\Setting;


if (!function_exists('setting')) {
    function settings($key, $default = null) {
        static $settings = null;
        if ($settings === null) {
            $settings = Setting::all()->pluck('value', 'key');
        }
        return $settings[$key] ?? $default;
    }
}
