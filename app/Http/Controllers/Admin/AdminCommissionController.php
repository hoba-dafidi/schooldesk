<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CommissionExport;
use App\Http\Controllers\Controller;
use App\Models\AffiliateCommission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class AdminCommissionController extends Controller
{
    /**
     * Show the commissions management view.
     */
    public function index()
    {
        return view('admin.commissions.index');
    }

    /**
     * DataTable AJAX response.
     */
    public function data(Request $request)
    {
        $query = AffiliateCommission::with('user')->select('affiliate_commissions.*');

        return DataTables::eloquent($query)
            ->addColumn('user_name', fn($row) => $row->user->name)
            ->addColumn('status_label', function ($row) {
                return view('admin.commissions.partials.status', compact('row'))->render();
            })
            ->addColumn('actions', function ($row) {
                return view('admin.commissions.partials.actions', compact('row'))->render();
            })
            ->rawColumns(['status_label', 'actions'])
            ->make(true);
    }

    /**
     * Approve a pending commission.
     */
    public function approve(AffiliateCommission $commission)
    {
        if ($commission->status === 'pending') {
            $commission->update(['status' => 'approved']);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Mark commission as paid.
     */
    public function markAsPaid(AffiliateCommission $commission)
    {
        if (!$commission->is_paid) {
            $commission->update([
                'is_paid' => true,
                'paid_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Export commissions to Excel.
     */
    public function export()
    {
        return Excel::download(new CommissionExport, 'commissions_report.xlsx');
    }

    /**
     * Show affiliate performance chart.
     */
    public function performance()
    {
        $topAffiliates = User::whereHas('affiliateCommissions')
            ->withSum('affiliateCommissions as total_commission', 'amount')
            ->orderByDesc('total_commission')
            ->take(10)
            ->get();

        $chartData = [
            'labels' => $topAffiliates->pluck('name'),
            'series' => $topAffiliates->pluck('total_commission')->map(fn($val) => round($val)),
        ];

        return view('admin.commissions.performance', compact('chartData'));
    }
}
