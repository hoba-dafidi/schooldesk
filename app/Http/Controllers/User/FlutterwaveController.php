<?php

namespace App\Http\Controllers\User;

use App\Events\SubscriptionActivated;
use App\Events\SubscriptionUpdated;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\PaymentGateway;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlutterwaveController extends Controller
{
    public function handleCallback(Request $request)
    {
        $transactionId = $request->query('transaction_id');
        $status = $request->query('status');

        if (!$transactionId) {
            return redirect()->route('dashboard')->with('error', 'Transaction ID is missing.');
        }

        // ✅ Step 1: Verify transaction from Flutterwave
        $secretKey = config('services.flutterwave.secret_key'); // store in config/services.php
        $response = Http::withToken($secretKey)
            ->get("https://api.flutterwave.com/v3/transactions/{$transactionId}/verify");

        if (!$response->ok()) {
            Log::error('Flutterwave verification failed.', $response->json());
            return redirect()->route('dashboard')->with('error', 'Transaction verification failed.');
        }

        $data = $response->json('data');

        // ✅ Step 2: Find invoice using reference
        $invoice = Invoice::where('invoice_number', $data['tx_ref'])->first();

        if (!$invoice) {
            Log::warning("No invoice found for tx_ref: {$data['tx_ref']}");
            return redirect()->route('dashboard')->with('error', 'Invalid transaction reference.');
        }

        if ($invoice->status === 'paid') {
            return redirect()->route('dashboard')->with('info', 'Invoice already marked as paid.');
        }

        // ✅ Step 3: Update invoice
        $invoice->status = 'paid';
        $invoice->reference = $transactionId;
        $invoice->paid_at = now();
        $invoice->save();

        // ✅ Step 4: Activate subscription
        $subscription = $invoice->subscription;
        if ($subscription) {
            $now = now();
            $subscription->status = 'active';
            $subscription->starts_at = $now;
            $subscription->ends_at = $now->copy()->addDays($subscription->plan->duration_days);
            $subscription->save();

            // ✅ Step 5: Trigger event/notification
            event(new SubscriptionActivated($subscription));
        }

        return redirect()->route('user.invoices.show', $invoice)->with('success', 'Payment successful. Subscription activated.');
    }

    // public function callback(Request $request)
    // {
    //     $transactionId = $request->query('transaction_id');

    //     if (!$transactionId) {
    //         return redirect()->route('home')->with('error', 'Transaction failed or cancelled.');
    //     }

    //     $gateway = PaymentGateway::where('slug', 'flutterwave')->firstOrFail();
    //     $secretKey = config('services.flutterwave.secret_key'); // store in config/services.php

    //     dd($secretKey);

    //     // Verify with Flutterwave API
    //     $response = Http::withToken($secretKey)
    //         ->get("https://api.flutterwave.com/v3/transactions/{$transactionId}/verify");

    //     if (!$response->successful()) {
    //         return redirect()->route('home')->with('error', 'Unable to verify transaction.');
    //     }

    //     $data = $response->json('data');

    //     // Optional: confirm status
    //     if ($data['status'] !== 'successful' || $data['currency'] !== 'NGN') {
    //         return redirect()->route('home')->with('error', 'Transaction verification failed.');
    //     }

    //     // Find invoice using tx_ref or metadata
    //     $invoiceNumber = $data['tx_ref']; // e.g., INV-123456-1721981267

    //     $invoice = Invoice::where('invoice_number', $invoiceNumber)->firstOrFail();

    //     if ($invoice->is_paid) {
    //         return redirect()->route('home')->with('info', 'Invoice already paid.');
    //     }

    //     // Mark invoice paid
    //     $invoice->update([
    //         'is_paid' => true,
    //         'paid_at' => now(),
    //         'payment_gateway_id' => $gateway->id,
    //     ]);

    //     // Activate subscription
    //     $subscription = $invoice->subscription;
    //     $plan = $subscription->plan;

    //     $subscription->update([
    //         'status' => 'active',
    //         'starts_at' => now(),
    //         'ends_at' => now()->addDays($plan->duration_days),
    //     ]);

    //     event(new \App\Events\SubscriptionActivated($subscription));

    //     return redirect()->route('home')->with('success', 'Payment successful. Subscription activated.');
    // }

}
