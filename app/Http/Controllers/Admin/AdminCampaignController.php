<?php

namespace App\Http\Controllers\Admin;

use \PDF;
use App\Http\Controllers\Controller;
use App\Models\AffiliateCampaign;
use App\Models\AffiliateCommission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class AdminCampaignController extends Controller
{
    public function index(Request $request)
    {
        $users = User::has('affiliate')->get();
        $period = $request->get('period', 'weekly');

        // Summary stats
        $totalCommission = AffiliateCommission::sum('amount');
        $paidCommission = AffiliateCommission::where('status', 'paid')->sum('amount');
        $pendingCommission = AffiliateCommission::where('status', 'pending')->sum('amount');
        $activeCampaigns = AffiliateCampaign::where('is_active', true)->count();

        // Chart data (Weekly/Monthly)
        $chartLabels = [];
        $chartData = [];

        if ($period === 'monthly') {
            $commissions = AffiliateCommission::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as period, SUM(amount) as total')
                ->groupBy('period')
                ->orderBy('period')
                ->take(6)
                ->get();

            foreach ($commissions as $c) {
                $chartLabels[] = Carbon::createFromFormat('Y-m', $c->period)->format('M Y');
                $chartData[] = $c->total;
            }
        } else {
            // Weekly
            $commissions = AffiliateCommission::selectRaw('YEARWEEK(created_at, 1) as week, SUM(amount) as total')
                ->groupBy('week')
                ->orderBy('week', 'desc')
                ->take(6)
                ->get()
                ->reverse();

            foreach ($commissions as $c) {
                $year = substr($c->week, 0, 4);
                $weekNum = substr($c->week, 4);
                $chartLabels[] = Carbon::now()->setISODate($year, $weekNum)->startOfWeek()->format('d M');
                $chartData[] = $c->total;
            }
        }

        return view('admin.campaigns.index', compact(
            'users',
            'totalCommission',
            'paidCommission',
            'pendingCommission',
            'activeCampaigns',
            'chartLabels',
            'chartData',
            'period'
        ));
    }

    // DataTable AJAX response
    public function data(Request $request)
    {
        $query = AffiliateCampaign::with('affiliate.user')
            ->when($request->affiliate_id, fn($q) => $q->where('affiliate_id', $request->affiliate_id))
            ->when($request->start_date, fn($q) => $q->whereDate('created_at', '>=', $request->start_date))
            ->when($request->end_date, fn($q) => $q->whereDate('created_at', '<=', $request->end_date));

        return DataTables::eloquent($query)
            ->addColumn('affiliate', fn($row) => $row->affiliate->user->name ?? '—')
            ->addColumn('link', function ($row) {
                $link = url('/register?ref=' . $row->code);
                return "<a href='$link' target='_blank'>$link</a>";
            })
            ->addColumn('active', fn($row) => $row->isActive() ?
                '<span class="badge bg-success">Active</span>' :
                '<span class="badge bg-secondary">Inactive</span>')
            ->addColumn('actions', function ($row) {
                return view('admin.campaigns.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['link', 'active', 'actions'])
            ->make(true);
    }

    public function destroy(AffiliateCampaign $campaign)
    {
        $campaign->delete();
        return response()->json(['success' => true]);
    }

    // Export campaigns
    public function export($type)
    {
        $campaigns = AffiliateCampaign::with('affiliate.user')->get();

        if ($type === 'excel') {
            return Excel::download(new \App\Exports\CampaignsExport($campaigns), 'campaigns.xlsx');
        }

        if ($type === 'pdf') {
            $pdf = PDF::loadView('admin.campaigns.export-pdf', compact('campaigns'));
            return $pdf->download('campaigns.pdf');
        }

        return back();
    }
}
