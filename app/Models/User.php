<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, LogsActivity;

    protected $fillable = [
        'firstname', 'middlename', 'lastname', 'email', 'password', 'is_admin', 'phone', 'avatar', 'bio', 'timezone',
        'is_active', 'last_login_at', 'last_login_ip', 'login_attempts', 'locked_until', 'tenant_id', 'role',
        'stripe_id', 'pm_type', 'pm_last_four', 'trial_ends_at', 'provider', 'provider_id', 'last_seen_at',
        'facebook', 'twitter', 'linkedin', 'address_line1', 'address_line2', 'city', 'state', 'postal_code', 'country',
        'referral_code', 'referred_by', 'bank_code', 'bank_name', 'account_number', 'account_name',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
        'trial_ends_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    // RELATIONSHIPS
    public function schools()
    {
        return $this->hasMany(School::class, 'user_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function manualPayments()
    {
        return $this->hasMany(ManualPayment::class);
    }

    public function customDomains()
    {
        return $this->hasManyThrough(
            CustomDomain::class,
            School::class,
            'user_id', 'school_id', 'id', 'id'
        );
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    public function affiliate()
    {
        return $this->hasOne(Affiliate::class);
    }

    // public function referredUsers()
    // {
    //     return $this->hasManyThrough(
    //         User::class,
    //         AffiliateCommission::class,
    //         'affiliate_id',
    //         'id',
    //         'id',
    //         'referred_user_id'
    //     );
    // }

    // // Get the user who referred this user
    // public function referrer()
    // {
    //     return $this->belongsTo(User::class, 'referred_by');
    // }

    // Get the users referred by this user
    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function referralTree($maxDepth = 3, $currentDepth = 1)
    {
        if ($currentDepth > $maxDepth) {
            return collect();
        }

        return $this->referrals->map(function ($referral) use ($maxDepth, $currentDepth) {
            return [
                'user' => $referral,
                'level' => $currentDepth,
                'children' => $referral->referralTree($maxDepth, $currentDepth + 1),
            ];
        });
    }

    public function referredUsers()
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function referrer()
    {
        return $this->hasOne(Referral::class, 'referred_id');
    }

    // To get all users referred directly
    public function directReferrals()
    {
        return $this->belongsToMany(User::class, 'referrals', 'referrer_id', 'referred_id')
                    ->wherePivot('level', 1);
    }

    // To get all levels (optional)
    public function referralLevels()
    {
        return $this->hasMany(Referral::class, 'referrer_id')->orderBy('level');
    }


    public function affiliateCommissions()
    {
        return $this->hasManyThrough(
            \App\Models\AffiliateCommission::class,
            \App\Models\Affiliate::class,
            'user_id', // Foreign key on Affiliate table
            'affiliate_id', // Foreign key on AffiliateCommission table
            'id', // Local key on User table
            'id'  // Local key on Affiliate table
        );
    }


    // ACCESSORS
    public function getNameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : asset('assets/img/avatars/default.png');
    }

    // ACTIVITY LOGGING
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['firstname', 'lastname', 'email'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // UTILITY
    public function isAdmin()
    {
        return (bool) $this->is_admin;
    }

    // Accessor for referral link
    public function getReferralLinkAttribute(): string
    {
        return route('register', ['ref' => $this->referral_code]);
    }

    public function getTotalCommissionAttribute()
    {
        return $this->affiliateCommissions()->approved()->sum('amount');
    }

    public function getTotalSignupsAttribute()
    {
        return $this->affiliateCommissions()->count();
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->firstname} {$this->middlename} {$this->lastname}");
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->referral_code = self::generateUniqueReferralCode();
        });
    }

    public static function generateUniqueReferralCode()
    {
        do {
            $code = Str::upper(Str::random(10));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }
}
