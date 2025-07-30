<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    // -----------------------------
    // Static Get/Set helpers
    // -----------------------------
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value)
    {
        return static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    // -----------------------------
    // Accessors / Helpers
    // -----------------------------

    public function isImage()
    {
        return in_array($this->key, [
            'app_logo_url',
            'favicon_url',
            'background_url',
            'watermark_url',
        ]);
    }

    /**
     * Get affiliate commission levels as array.
     * Example: {1: 0.1, 2: 0.05, 3: 0.02}
     */
    public static function getCommissionLevels(): array
    {
        $levelsJson = self::get('affiliate_commission_levels');
        return $levelsJson ? json_decode($levelsJson, true) : [];
    }

    /**
     * Get default affiliate commission percentage (fallback if levels missing)
     */
    public static function getDefaultCommissionPercentage(): float
    {
        return (float) self::get('affiliate_commission_percentage', 30);
    }
}
