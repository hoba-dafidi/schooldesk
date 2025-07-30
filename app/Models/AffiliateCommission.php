<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AffiliateCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_id',
        'referred_user_id',
        'subscription_id',
        'campaign_id',
        'amount',
        'status', // 'pending', 'approved', 'paid'
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'float',
    ];

    // ----------------------
    // Relationships
    // ----------------------

    /**
     * The affiliate who earned the commission.
     */
    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function campaign()
    {
        return $this->belongsTo(AffiliateCampaign::class);
    }

    /**
     * The user that was referred.
     */
    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    /**
     * The user (via affiliate) who earned the commission.
     */
    public function user()
    {
        return $this->affiliate ? $this->affiliate->user : null;
    }

    /**
     * The subscription that triggered the commission.
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    // ----------------------
    // Scopes
    // ----------------------

    /**
     * Scope for approved commissions.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for paid commissions.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // ----------------------
    // Accessors
    // ----------------------

    /**
     * Check if commission is paid.
     */
    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid';
    }

    // ----------------------
    // Static Logic
    // ----------------------

    /**
     * Create a commission record for a successful subscription.
     */
    // public static function createForSubscription(User $user, Subscription $subscription): void
    // {
    //     if (!$user->referred_by || !$subscription->is_active) {
    //         return;
    //     }

    //     $affiliate = Affiliate::where('user_id', $user->referred_by)->first();

    //     if (!$affiliate) {
    //         return;
    //     }

    //     $commissionPercentage = (float) \App\Models\Setting::get('affiliate_commission_percentage', 10);
    //     $commissionAmount = ($subscription->amount * $commissionPercentage) / 100;

    //     self::create([
    //         'affiliate_id'      => $affiliate->id,
    //         'referred_user_id'  => $user->id,
    //         'subscription_id'   => $subscription->id,
    //         'amount'            => $commissionAmount,
    //         'status'            => 'pending',
    //     ]);

    //     $affiliate->increment('commissions_earned', $commissionAmount);
    // }

    /**
     * Create commissions for a new subscription, supporting multi-level affiliates.
     */
    // app/Models/AffiliateCommission.php

    public static function createForSubscription(User $user, Subscription $subscription, ?int $campaignId = null): void
    {
        // Only award if subscription is active (adjust if you use a different flag)
        if (($subscription->status ?? null) !== 'active') {
            return;
        }

        // Get commission levels: [1 => 0.10, 2 => 0.05, ...] as decimals
        $commissionLevels = \App\Models\Setting::getCommissionLevels();
        if (empty($commissionLevels)) {
            return;
        }

        $level       = 1;
        $currentUser = $user;

        while ($level <= count($commissionLevels) && $currentUser->referred_by) {
            $referrer = User::find($currentUser->referred_by);
            if (!$referrer) {
                break;
            }

            $affiliate = \App\Models\Affiliate::where('user_id', $referrer->id)->first();
            if (!$affiliate) {
                $currentUser = $referrer;
                $level++;
                continue;
            }

            $pct = (float) ($commissionLevels[$level] ?? 0);
            if ($pct <= 0) {
                $currentUser = $referrer;
                $level++;
                continue;
            }

            $amount = ($subscription->amount * $pct);

            // Avoid duplicates for same subscription + affiliate
            $exists = self::where('subscription_id', $subscription->id)
                ->where('affiliate_id', $affiliate->id)
                ->exists();

            if (!$exists) {
                self::create([
                    'affiliate_id'      => $affiliate->id,
                    'referred_user_id'  => $user->id,
                    'subscription_id'   => $subscription->id,
                    'campaign_id'       => $campaignId,   // <— attach campaign
                    'amount'            => $amount,
                    'status'            => 'pending',
                ]);

                // Optional: track totals on affiliates table (if you keep this column)
                if (\Illuminate\Support\Facades\Schema::hasColumn('affiliates', 'commissions_earned')) {
                    $affiliate->increment('commissions_earned', $amount);
                }
            }

            $currentUser = $referrer;
            $level++;
        }
    }



}
