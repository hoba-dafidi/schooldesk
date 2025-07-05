<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'is_active',
        'description',
        'driver_class',        // Optional fully qualified service class name
        'api_key',
        'api_secret',
        'webhook_secret',
        'merchant_id',
        'currency',
        'sandbox_mode',        // true/false
        'logo_url',            // gateway branding logo
        'callback_url',        // where to receive responses
        'webhook_url',
        'config',              // JSON config for special settings
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sandbox_mode' => 'boolean',
        'config' => 'array',
    ];

    // Relationships
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    // Accessors / Helpers (optional)
    public function getDisplayNameAttribute()
    {
        return ucfirst($this->name);
    }
}
