<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AffiliatesExport;
use App\Http\Controllers\Controller;
use App\Models\AffiliateCommission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminAffiliateController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'weekly');
        $range = $request->get('range', 'weekly'); // for legacy compatibility
        $start = $request->start_date ?? now()->subDays(30);
        $end = $request->end_date ?? now();
        $userId = $request->user_id;

        // Base Query
        $commissionsQuery = AffiliateCommission::with('affiliate.user')
            ->when($userId, fn($q) => $q->where('user_id', $userId))
            ->whereBetween('created_at', [$start, $end]);

        $commissions = $commissionsQuery->get();

        // Totals
        $totalAffiliates = User::whereHas('affiliate.commissions')->count();
        $totalCommission = $commissions->sum('amount');
        $paidCommission = $commissions->where('status', 'paid')->sum('amount');
        $pendingCommission = $commissions->where('status', 'pending')->sum('amount');

        // Referrals by school
        $referralsBySchool = $commissions->groupBy('school_id')->map->count()->sortDesc()->take(5);

        // Top affiliates
        $topAffiliates = User::withCount('affiliateCommissions')
            ->withSum('affiliateCommissions', 'amount')
            ->orderByDesc('affiliate_commissions_sum_amount')
            ->take(5)
            ->get();

        // Recent commissions
        $recentCommissions = $commissions->sortByDesc('created_at')->take(5);

        // Chart
        $chartLabels = [];
        $chartData = [];

        if ($period === 'monthly') {
            $monthly = $commissions->groupBy(function ($c) {
                return $c->created_at->format('Y-m');
            })->sortKeys();

            foreach ($monthly as $key => $group) {
                $chartLabels[] = Carbon::createFromFormat('Y-m', $key)->format('M Y');
                $chartData[] = $group->sum('amount');
            }
        } else {
            // weekly
            $weekly = $commissions->groupBy(function ($c) {
                return $c->created_at->startOfWeek()->format('Y-m-d');
            })->sortKeys();

            foreach ($weekly as $week => $group) {
                $chartLabels[] = Carbon::createFromFormat('Y-m-d', $week)->format('d M');
                $chartData[] = $group->sum('amount');
            }
        }

        return view('admin.affiliates.index', [
            'totalAffiliates' => $totalAffiliates,
            'totalCommission' => $totalCommission,
            'paidCommission' => $paidCommission,
            'pendingCommission' => $pendingCommission,
            'referralsBySchool' => $referralsBySchool,
            'topAffiliates' => $topAffiliates,
            'recentCommissions' => $recentCommissions,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'commissions' => $commissions,
            'users' => User::has('affiliate.commissions')->get(),
            'period' => $period,
        ]);
    }


    public function export()
    {
        $fileName = 'affiliates_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new AffiliatesExport, $fileName);
    }
}
