<?php

namespace App\Http\Controllers;

use \App\Models\PayoutRequest;
use App\Models\Affiliate;
use App\Models\AffiliateCampaign;
use App\Models\AffiliateCommission;
use App\Models\AffiliatePayoutRequest;
use App\Models\Referral;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Yabacon\Paystack;

class AffiliateController extends Controller
{
    /**
     * Show the affiliate dashboard.
     */
    public function index(Request $request)
    {
        $user   = Auth::user();

        // Ensure this user has an Affiliate profile for metrics
        $affiliate = Affiliate::firstOrCreate(['user_id' => $user->id]);

        // Referral code/display link (user-based code; campaigns have separate codes)
        $referralCode = $user->referral_code;
        $referralLink = url('/register?ref=' . $referralCode);

        // Filters
        $status = $request->get('status'); // null | 'pending' | 'paid'
        $range  = $request->get('range', 'weekly'); // 'weekly' | 'monthly'

        // --- Commissions for this affiliate ---
        $commissionsQuery = AffiliateCommission::with('referredUser')
            ->where('affiliate_id', $affiliate->id)
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest();

        $commissions = $commissionsQuery->paginate(10);

        // Totals
        $totalCommission   = (float) AffiliateCommission::where('affiliate_id', $affiliate->id)->sum('amount');
        $paidCommission    = (float) AffiliateCommission::where('affiliate_id', $affiliate->id)->where('status', 'paid')->sum('amount');
        $pendingCommission = (float) AffiliateCommission::where('affiliate_id', $affiliate->id)->where('status', 'pending')->sum('amount');

        // --- Direct referrals (Level 1) via Referral table ---
        $directReferralIds = Referral::where('referrer_id', $user->id)
            ->where('level', 1)
            ->pluck('referred_id');

        $signupCount   = $directReferralIds->count();
        $referredUsers = User::whereIn('id', $directReferralIds)
            ->latest()
            ->select('id', 'firstname', 'lastname', 'email', 'created_at')
            ->get();

        // --- Chart data for affiliate commissions ---
        $groupFormat = $range === 'monthly' ? 'M Y' : 'd M';
        $startDate   = $range === 'monthly' ? now()->subMonths(5)->startOfMonth() : now()->subWeeks(5)->startOfWeek();

        $chartRaw = AffiliateCommission::where('affiliate_id', $affiliate->id)
            ->where('created_at', '>=', $startDate)
            ->orderBy('created_at')
            ->get();

        $chartSeries = $chartRaw->groupBy(function ($c) use ($range) {
                return $range === 'monthly'
                    ? $c->created_at->format('Y-m') // stable key
                    : $c->created_at->startOfWeek()->format('Y-m-d');
            })
            ->map(function ($group) {
                return $group->sum('amount');
            })
            ->sortKeys();

        // Human-friendly labels
        $chartLabels = $chartSeries->keys()->map(function ($key) use ($range) {
            return $range === 'monthly'
                ? Carbon::createFromFormat('Y-m', $key)->format('M Y')
                : Carbon::createFromFormat('Y-m-d', $key)->format('d M');
        })->values();

        $chartData = $chartSeries->values();

        // --- Campaign performance ---
        $campaigns = AffiliateCampaign::where('affiliate_id', $affiliate->id)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'name', 'referral_code', 'clicks', 'unique_signups', 'starts_at', 'ends_at', 'created_at']);

        // Sum of commissions by campaign for this affiliate
        $commissionByCampaign = AffiliateCommission::select('campaign_id', DB::raw('SUM(amount) as total'))
            ->where('affiliate_id', $affiliate->id)
            ->groupBy('campaign_id')
            ->pluck('total', 'campaign_id');

        // Paginated payout requests
        $payoutRequests = AffiliatePayoutRequest::where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        // JSON API support
        if ($request->wantsJson()) {
            return response()->json([
                'referral_code' => $referralCode,
                'referral_link' => $referralLink,
                'stats' => [
                    'total'   => $totalCommission,
                    'paid'    => $paidCommission,
                    'pending' => $pendingCommission,
                    'signups' => $signupCount,
                ],
                'chart' => [
                    'labels' => $chartLabels,
                    'values' => $chartData,
                    'range'  => $range,
                ],
                'commissions'     => $commissions,
                'referred_users'  => $referredUsers,
                'campaigns'       => $campaigns,
                'campaign_totals' => $commissionByCampaign,
                'payoutRequests' => $payoutRequests,
            ]);
        }

        return view('affiliate.dashboard', compact(
            'user',
            'affiliate',
            'referralCode',
            'referralLink',
            'status',
            'range',
            'commissions',
            'totalCommission',
            'paidCommission',
            'pendingCommission',
            'signupCount',
            'referredUsers',
            'chartLabels',
            'chartData',
            'campaigns',
            'commissionByCampaign',
            'payoutRequests'
        ));
    }



    /**
     * (Optional) Export commission history as CSV
     */
    public function exportCsv()
    {
        $user = Auth::user();

        $commissions = AffiliateCommission::where('affiliate_id', $user->id)->get();

        $filename = 'commissions_' . now()->format('Ymd_His') . '.csv';
        $headers = ['Content-Type' => 'text/csv'];
        $columns = ['ID', 'Amount', 'Status', 'Referred User', 'Created At'];

        $callback = function () use ($commissions, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($commissions as $commission) {
                fputcsv($file, [
                    $commission->id,
                    $commission->amount,
                    $commission->status,
                    optional($commission->referredUser)->email,
                    $commission->created_at->toDateTimeString()
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, array_merge([
            "Content-Disposition" => "attachment; filename=\"$filename\"",
        ], $headers));
    }

    public function requestPayout(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:20',
            'account_name' => 'required|string|max:255',
        ]);

        // Save bank details
        $user->update([
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
        ]);

        // Optional: Save payout request
        AffiliatePayoutRequest::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Your payout request has been submitted.');
    }

    public function updateBank(Request $request)
    {

        $validator = Validator::make($request->all(), [
          'bank_name' => 'required|string|max:100',
            'bank_code' => 'required|string',
            'account_number' => 'required|string|size:10',
            'account_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        $user->update([
            'bank_name' => $request->bank_name,
            'bank_code' => $request->bank_code,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name, // enforce same name
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Bank details updated successfully.',
        ]);
    }

    public function resolveAccount(Request $request)
    {
        $request->validate([
            'bank_code' => 'required|string',
            'account_number' => 'required|string|min:10|max:10',
        ]);

        $user = Auth::user();

        // Call Paystack API
        $secret = config('paystack.secret_key');
        $res = Http::withToken($secret)->get('https://api.paystack.co/bank/resolve', [
            'account_number' => $request->account_number,
            'bank_code' => $request->bank_code,
        ]);

        if (!$res->successful() || !isset($res['data']['account_name'])) {
            return response()->json([
                'status' => false,
                'message' => 'Unable to resolve account. Please check your input.'
            ]);
        }

        $data = $res->json();

        $resolvedName = strtoupper(trim($res['data']['account_name']));
        $expectedName = strtoupper(trim($user->firstname . ' ' . $user->middlename . ' ' . $user->lastname));
        $expectedName = preg_replace('/\s+/', ' ', $expectedName);

        // Fuzzy match
        similar_text($resolvedName, $expectedName, $similarity);

        // If names don't closely match and user hasn't forced it
        if ($similarity < 75 && !$request->boolean('force')) {
            return response()->json([
                'status' => false,
                'message' => "Account name doesn't closely match your registered name.",
                'expected_name' => $expectedName,
                'resolved_name' => $resolvedName,
                'match_score' => round($similarity, 2),
                'can_force' => true,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Account resolved successfully.',
            'resolved_name' => $data['data']['account_name'],
        ]);
    }

    public function bankSettings()
    {
        $banks = cache()->remember('paystack_banks', 86400, function () {
            $res = Http::withToken(config('services.paystack.secret'))
                ->get('https://api.paystack.co/bank');
            return $res->json()['data'] ?? [];
        });

        $user = Auth::user();

        return view('affiliate.bank', compact('banks', 'user'));
    }

}
