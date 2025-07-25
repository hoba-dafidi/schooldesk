<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price',
        'currency',
        'interval',
        'description',
        'features',
        'is_active',
        'duration_days',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'duration_days' => 'integer',
    ];

    protected $attributes = [
        'features' => '[]',
        'is_active' => true,
    ];

    /**
     * Relationships
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Scope for active plans
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Accessor for formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return strtoupper($this->currency) . ' ' . number_format($this->price, 2);
    }

    // Model accessor (optional)
    public function getIntervalLabelAttribute(): string
    {
        return ucfirst($this->interval); // Capitalize for display
    }
}
