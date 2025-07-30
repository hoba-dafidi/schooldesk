<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Affiliate;
use App\Models\AffiliateCampaign;

class TrackAffiliateClicks
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // -------------------------------
        // Track user-based referrals (?ref=CODE)
        // -------------------------------
        if ($request->filled('ref')) {
            $refCode = $request->get('ref');

            $referrer = User::where('referral_code', $refCode)->first();
            if ($referrer) {
                Affiliate::firstOrCreate(['user_id' => $referrer->id])
                    ->increment('clicks');

                // Store referrer for later (registration / subscription)
                session([
                    'referrer_user_id' => $referrer->id,
                    'referrer_type'    => 'user'
                ]);
            }
        }

        // -------------------------------
        // Track campaign clicks (?camp=CODE)
        // -------------------------------
        if ($request->filled('camp')) {
            $campCode = $request->get('camp');

            $campaign = AffiliateCampaign::where('code', $campCode)->first();
            if ($campaign) {
                $campaign->increment('clicks');

                // Store campaign in session
                session([
                    'referrer_campaign_id' => $campaign->id,
                    'referrer_type'        => 'campaign'
                ]);
            }
        }

        return $next($request);
    }
}
