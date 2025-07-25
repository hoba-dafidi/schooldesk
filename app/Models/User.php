<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, LogsActivity; // Added Billable trait

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'is_admin',
        'phone',
        'avatar',
        'bio',
        'timezone',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'login_attempts',
        'locked_until',
        'tenant_id',
        'role',
        'stripe_id',
        'pm_type',
        'pm_last_four',
        'trial_ends_at',
        'provider',
        'provider_id',
        'last_seen_at',
        'facebook',
        'twitter',
        'linkedin',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
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

    /**
     * Get the schools for the user (client).
     */
    public function schools()
    {
        return $this->hasMany(School::class, 'user_id'); // Ensure this matches your School model's foreign key
    }

    /**
     * Get the subscriptions for the user (client).
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the invoices for the user (client).
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the support tickets for the user (client).
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function customDomains()
    {
        return $this->hasManyThrough(
            \App\Models\CustomDomain::class,
            \App\Models\School::class,
            'user_id',      // Foreign key on schools table
            'school_id',    // Foreign key on custom_domains table
            'id',           // Local key on users table
            'id'            // Local key on schools table
        );
    }


    /**
     * Get the tenant that owns the user.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class); // Assuming you'll create a Tenant model
    }

    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    public function getNameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
    }

     public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email']) // or leave empty to log all changes
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }


    /**
     * Check if the user is an administrator.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return (bool) $this->is_admin;
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : asset('assets/img/avatars/default.png');
    }

}

// $user->assignRole('admin');
// $user->givePermissionTo('edit posts');
