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
    public function client()
    {
        return $this->belongsTo(User::class, 'user_id');
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

    public function subdomain()
{
    return $this->hasOne(Subdomain::class);
}

public function customDomain()
{
    return $this->hasOne(CustomDomain::class);
}

}

// $activeSchools = School::active()->get();
// $paidSchools = School::paid()->get();

// // Access full logo URL:
// $logoUrl = $school->logo_url;
