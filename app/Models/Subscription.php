<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'payment_gateway_id',
        'status',
        'quantity',
        'starts_at',
        'trial_ends_at',
        'grace_ends_at',
        'ends_at',
        'renews_at',
        'cancelled_at',
        'external_reference',
        'meta',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'grace_ends_at' => 'datetime',
        'ends_at' => 'datetime',
        'renews_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'meta' => 'array',
    ];

// | Field                | Usage Example                                            |
// | -------------------- | -------------------------------------------------------- |
// | `status`             | Control if subscription is still valid                   |
// | `starts_at`          | When subscription becomes effective                      |
// | `renews_at`          | To send renewal reminders or process next payment        |
// | `ends_at`            | Used for soft-cancel behavior (allow till period ends)   |
// | `external_reference` | Track transaction ref or external subscription ID        |
// | `meta`               | Store raw gateway response, receipt URLs, etc. (JSON)    |
// | `grace_ends_at`      | Allow continued access after cancellation (grace period) |

    /**
     * Get the user that owns the subscription.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the plan associated with the subscription.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function isActive()
    {
        return $this->stripe_status === 'active' && (is_null($this->ends_at) || $this->ends_at->isFuture());
    }

    public function onTrial()
    {
        return $this->trial_ends_at && now()->lt($this->trial_ends_at);
    }

    public function isCancelled()
    {
        return $this->stripe_status === 'cancelled' || ($this->ends_at && $this->ends_at->isPast());
    }

    public function scopeActive($query)
    {
        return $query->where('stripe_status', 'active')->where(function ($q) {
            $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
        });
    }

    public function scopeTrialing($query)
    {
        return $query->where('stripe_status', 'trialing')->where('trial_ends_at', '>', now());
    }

}
