<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'user_id',
        'plan_id',
        'name',
        'status',
        'is_paid_plan',
        'address',
        'email',
        'phone',
        'logo',
        'type',
        'established_year',
        'about',
        'city',
        'state',
        'country',
    ];

    protected $casts = [
        'is_paid_plan' => 'boolean',
        'established_year' => 'integer',
    ];

    /**
     * Relationship: The user (client) that owns the school.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: The plan the school is subscribed to.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Relationship: Subdomain assigned to the school.
     */
    public function subdomain()
    {
        return $this->hasOne(Subdomain::class);
    }

    /**
     * Relationship: Custom domain assigned to the school.
     */
    public function customDomain()
    {
        return $this->hasOne(CustomDomain::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
                    ->where('is_active', true)
                    ->whereDate('ends_at', '>=', now());
    }

    public function currentPlan()
    {
        return $this->activeSubscription?->plan;
    }

    public function currentSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->latest('ends_at');
    }


    /**
     * Accessor: Get full logo URL or fallback placeholder.
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo
            ? asset('storage/' . $this->logo)
            : asset('assets/img/placeholder-logo.png');
    }

    /**
     * Scope: Only active schools.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope: Only schools with paid plans.
     */
    public function scopePaid($query)
    {
        return $query->where('is_paid_plan', true);
    }

    /**
     * Check if the school is using a specific plan by slug or ID.
     *
     * @param string|int $plan
     * @return bool
     */
    public function hasPlan($plan): bool
    {
        if (!$this->plan) {
            return false;
        }

        return is_numeric($plan)
            ? $this->plan->id === (int) $plan
            : $this->plan->slug === $plan;
    }

    /**
     * Get the list of features from the school's plan.
     *
     * @return array
     */
    public function getPlanFeatures(): array
    {
        return $this->plan?->features ?? [];
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return [
            'active' => 'success',
            'pending' => 'warning',
            'suspended' => 'secondary',
            'deleted' => 'danger',
        ][$this->status] ?? 'secondary';
    }

    public function getPlanBadgeClassAttribute(): string
    {
        $slug = strtolower($this->plan?->slug ?? '');

        return [
            'free' => 'secondary',
            'basic' => 'info',
            'premium' => 'success',
            'enterprise' => 'primary',
        ][$slug] ?? 'dark';
    }


}
