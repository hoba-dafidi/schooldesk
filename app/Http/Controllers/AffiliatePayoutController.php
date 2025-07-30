<?php

namespace App\Http\Controllers;

use App\Models\AffiliatePayoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AffiliatePayoutController extends Controller
{
    /** Affiliate: Submit payout request */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        if ($request->amount > $user->totalCommission) {
            return back()->with('error', 'Insufficient balance for this payout amount.');
        }

        AffiliatePayoutRequest::create([
            'user_id'       => $user->id,
            'amount'        => $request->amount,
            'bank_name'     => $user->bank_name,
            'account_name'  => $user->account_name,
            'account_number'=> $user->account_number,
        ]);

        return back()->with('success', 'Your payout request has been submitted.');
    }

    /** Admin: List all requests */
    public function adminIndex()
    {
        $requests = AffiliatePayoutRequest::with('user')->latest()->paginate(15);
        return view('admin.payouts.index', compact('requests'));
    }

    /** Admin: Approve or reject */
    public function updateStatus(AffiliatePayoutRequest $payout, Request $request)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,paid'
        ]);

        $payout->update([
            'status' => $request->status,
            'paid_at' => $request->status === 'paid' ? now() : null
        ]);

        return back()->with('success', 'Payout status updated successfully.');
    }
}
