<?php

namespace App\Http\Controllers\Admin;

use App\Events\SubscriptionActivated;
use App\Http\Controllers\Controller;
use App\Models\ManualPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;


class ManualPaymentController extends Controller
{

    public function index()
    {
        $manualPayments = ManualPayment::with(['user', 'invoice'])->latest()->get();
        return view('admin.manual-payments.index', compact('manualPayments'));
    }


    public function update(Request $request, $id)
    {
        $payment = ManualPayment::with('invoice', 'user')->findOrFail($id);

        if (!in_array($request->status, ['approved', 'rejected'])) {
            return response()->json(['success' => false, 'message' => 'Invalid status']);
        }

        $payment->status = $request->status;
        $payment->reviewed_by = auth()->id();
        $payment->reviewed_at = now();
        $payment->save();

        // Mark invoice as paid if approved
        if ($request->status === 'approved') {
            $payment->invoice->update(['status' => 'paid']); // Adjust field name as needed
        }

        // Notify user
        Notification::route('mail', $payment->user->email)
            ->notify(new \App\Notifications\ManualPaymentReviewed($payment));

        return response()->json([
            'success' => true,
            'message' => "Payment {$request->status} successfully."
        ]);
    }

    public function review(Request $request, ManualPayment $manualPayment)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        if ($manualPayment->status !== 'pending') {
            return response()->json([
                'message' => 'This payment has already been reviewed.',
            ], 422);
        }

        $manualPayment->update([
            'status' => $request->status,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Only proceed with invoice/subscription updates if approved
        if ($request->status === 'approved') {
            $invoice = $manualPayment->invoice;

            // Mark invoice as paid
            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(), // optional
            ]);

            // Activate subscription (if not already active)
            if ($invoice->subscription && $invoice->subscription->status !== 'active') {
                $invoice->subscription->update([
                    'status' => 'active',
                    'starts_at' => now(),
                    'ends_at' => now()->addDays($invoice->subscription->duration_in_days), // or from plan
                ]);

                 event(new SubscriptionActivated($invoice->subscription));
            }

            // Optionally, send email to user
            try {
                Mail::to($manualPayment->user->email)->queue(
                    new \App\Mail\ManualPaymentApproved($invoice)
                );
            } catch (\Exception $e) {
                Log::error('Failed to send manual payment approval email: ' . $e->getMessage());
            }

            // Optionally, log activity
            activity()
                ->performedOn($invoice)
                ->causedBy(Auth::user())
                ->withProperties(['invoice' => $invoice->invoice_number])
                ->log('Manual payment approved and subscription activated.');
        }

        return response()->json([
            'message' => 'Payment has been ' . $manualPayment->status . '.',
            'status' => $manualPayment->status,
            'reviewer' => $manualPayment->reviewer->name ?? 'N/A',
        ]);
    }


}
