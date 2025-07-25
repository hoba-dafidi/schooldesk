<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionAudit;
use Illuminate\Http\Request;

class SubscriptionAuditController extends Controller
{
    /**
     * Display a listing of the subscription audit logs.
     */
    public function index(Request $request)
    {
        $query = SubscriptionAudit::with(['school', 'user', 'oldPlan', 'newPlan'])
            ->latest();

        // Optional filters
        if ($request->has('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        $audits = $query->paginate(20);

        return view('admin.subscription_audits.index', compact('audits'));
    }
}
