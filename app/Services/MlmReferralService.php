<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\AffiliateCommission;
use App\Models\Referral;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MlmReferralService
{
    /** @var int */
    protected int $maxLevels;

    public function __construct()
    {
        // Derive max levels from JSON config (fallback to 3 if empty)
        $levels = Setting::getCommissionLevels(); // e.g. [1 => 0.10, 2 => 0.05, 3 => 0.02]
        $this->maxLevels = max(1, count($levels) ?: 3);
    }

    /**
     * Register referral relationships up the chain (Level 1..N).
     *
     * Creates/ensures Referral rows: (referrer_id -> referred_id, level).
     */
    public function registerReferral(User $referredUser, ?User $directReferrer): void
    {
        if (!$directReferrer) {
            return;
        }

        $currentReferrer = $directReferrer;

        for ($level = 1; $level <= $this->maxLevels && $currentReferrer; $level++) {
            Referral::firstOrCreate(
                [
                    'referrer_id' => $currentReferrer->id,
                    'referred_id' => $referredUser->id,
                ],
                [
                    'level' => $level,
                ]
            );

            // Move one level up the upline
            // (User model: $user->referrer() belongsTo(User::class, 'referred_by'))
            $currentReferrer = $currentReferrer->referrer; // returns User|null
        }
    }

    /**
     * Distribute commissions to upline for a payment/subscription.
     *
     * @param  User       $payer          The user who triggered the commission (the referred user).
     * @param  float      $amount         The commissionable base amount (e.g., subscription amount).
     * @param  int|null   $subscriptionId Optional subscription id to prevent duplicates per affiliate.
     * @param  int|null   $campaignId     Optional campaign attribution.
     */
    public function distributeCommission(User $payer, float $amount, ?int $subscriptionId = null, ?int $campaignId = null): void
    {
        // Fetch chain (levels 1..N) for this payer
        $referrals = Referral::where('referred_id', $payer->id)
            ->orderBy('level', 'asc')
            ->take($this->maxLevels)
            ->get();

        // Load commission levels as decimals (0.10, 0.05, ...)
        $levels = Setting::getCommissionLevels();
        if (empty($levels)) {
            return;
        }

        DB::transaction(function () use ($referrals, $payer, $amount, $subscriptionId, $campaignId, $levels) {
            foreach ($referrals as $referral) {
                $level = (int) $referral->level;

                // Get rate for this level (decimal fraction)
                $rate = (float) ($levels[$level] ?? 0);
                if ($rate <= 0) {
                    continue;
                }

                // Find the referrer user and their affiliate profile
                $referrer = User::find($referral->referrer_id);
                if (!$referrer) {
                    continue;
                }

                $affiliate = Affiliate::where('user_id', $referrer->id)->first();
                if (!$affiliate) {
                    continue;
                }

                $commissionAmount = round($amount * $rate, 2);
                if ($commissionAmount <= 0) {
                    continue;
                }

                // Prevent duplicates for the same subscription+affiliate
                if ($subscriptionId) {
                    $exists = AffiliateCommission::where('subscription_id', $subscriptionId)
                        ->where('affiliate_id', $affiliate->id)
                        ->exists();

                    if ($exists) {
                        continue;
                    }
                }

                AffiliateCommission::create([
                    'affiliate_id'      => $affiliate->id,
                    'referred_user_id'  => $payer->id,
                    'subscription_id'   => $subscriptionId,
                    'campaign_id'       => $campaignId,
                    'amount'            => $commissionAmount,
                    'status'            => 'pending',
                ]);

                // Optional running totals if you keep this column on affiliates
                // if (Schema::hasColumn('affiliates', 'commissions_earned')) {
                //     $affiliate->increment('commissions_earned', $commissionAmount);
                // }
            }
        });
    }
}
