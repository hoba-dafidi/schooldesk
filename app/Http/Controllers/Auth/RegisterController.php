<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\AffiliateCampaign;
use App\Models\AffiliateCommission;
use App\Models\User;
use App\Services\MlmReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register'); // create view as needed
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8|confirmed',
        ]);

        // Resolve attribution from session (set by TrackAffiliateClicks middleware)
        $campaignId   = session('referrer_campaign_id'); // when ?camp=
        $referrerId   = session('referrer_user_id');     // when ?ref= OR derived from campaign
        $referrerUser = null;

        // If a campaign was used, derive the referrer from the campaign’s affiliate
        if ($campaignId) {
            $campaign = AffiliateCampaign::with('affiliate.user')->find($campaignId);
            if ($campaign && $campaign->affiliate && $campaign->affiliate->user) {
                $referrerUser = $campaign->affiliate->user;
                $referrerId   = $referrerUser->id;
            }
        }

        // Otherwise fall back to a direct referrer user
        if (!$referrerUser && $referrerId) {
            $referrerUser = User::find($referrerId);
        }

        DB::transaction(function () use ($data, $referrerUser, $campaignId) {
            // Create the user, attribute referrer & campaign if present
            $user = User::create([
                'firstname'               => $data['firstname'],
                'lastname'                => $data['lastname'],
                'email'                   => $data['email'],
                'password'                => Hash::make($data['password']),
                'is_active'               => true,
                'tenant_id'               => null,
                'referred_by'             => $referrerUser?->id,
                'attribution_campaign_id' => $campaignId,
            ]);

            // Ensure the new user has an Affiliate row (for their own future referrals)
            Affiliate::firstOrCreate(['user_id' => $user->id]);

            // Assign role, login, verify
            $user->assignRole('user');
            Auth::login($user);
            $user->sendEmailVerificationNotification();

            // Track MLM chain + metrics if referrer exists
            if ($referrerUser) {
                // Register multi-level referral path
                app(MlmReferralService::class)->registerReferral($user, $referrerUser);

                // Increment direct affiliate’s signups
                Affiliate::firstOrCreate(['user_id' => $referrerUser->id])
                    ->increment('signups');

                // Increment campaign unique signups too (if campaign present)
                if ($campaignId) {
                    AffiliateCampaign::where('id', $campaignId)->increment('unique_signups');
                }
            }
        });

        return redirect()->route('verification.notice');
    }


}
