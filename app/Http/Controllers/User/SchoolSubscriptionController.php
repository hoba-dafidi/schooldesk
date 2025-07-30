<?php

namespace App\Http\Controllers\User;

use App\Events\SubscriptionChanged;
use App\Http\Controllers\Controller;
use App\Models\AffiliateCommission;
use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PaymentGateway;
use App\Models\Plan;
use App\Models\School;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\SubscriptionAudit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class SchoolSubscriptionController extends Controller
{

  public function index()
    {
        // Fetch all subscriptions related to the user's school(s)
        $user = Auth::user();

        // Assuming subscriptions are linked through the school relationship
        // Adjust the relationship as needed
        $subscriptions = Subscription::where('user_id', $user->id)
            ->with(['plan', 'school', 'gateway', 'invoice']) // or 'invoices'
            ->latest()
            ->get();

        return view('user.subscriptions.index', compact('subscriptions'));
    }


    /**
     * Handle subscribing a school to a plan.
     */

    public function subscribe(Request $request, School $school)
    {
        $request->validate([
            'plan_id'          => 'required|exists:plans,id',
            'title'            => 'nullable|string|max:255',
            'description'      => 'nullable|string|max:1000',
            'notes'            => 'nullable|string|max:1000',
            'currency'         => 'nullable|string|max:10',
            'discount_amount'  => 'nullable|numeric|min:0',
            'due_date'         => 'nullable|date|after_or_equal:today',
            'payment_gateway_slug' => 'required|exists:payment_gateways,slug',
        ]);

        $plan = Plan::findOrFail($request->plan_id);

        $subscription = $school->subscriptions()->create([
            'user_id'    => Auth::id(),
            'plan_id'    => $plan->id,
            'start_date' => now(),
            'end_date'   => now()->addDays($plan->duration_days),
            'status'     => 'pending',
        ]);

        $this->handleReferral(Auth::user(), $subscription);

        if ($plan->price > 0) {
            $gateway = PaymentGateway::where('slug', $request->payment_gateway_slug)->firstOrFail();
            $taxPercentage = Setting::get('tax_percentage', 0);

            $discountAmount = $request->discount_amount ?? 0;
            $taxableBase = max(0, $plan->price - $discountAmount);
            $taxAmount = round(($taxableBase * $taxPercentage) / 100, 2);
            $finalAmount = max(0, $taxableBase + $taxAmount);

            $invoice = Invoice::create([
                'user_id'             => Auth::id(),
                'school_id'           => $school->id,
                'plan_id'             => $plan->id,
                'subscription_id'     => $subscription->id,
                'payment_gateway_id'  => $gateway->id ?? null,
                'title'               => $request->title ?? "{$plan->name} Plan Subscription",
                'description'         => $request->description ?? "Subscription for {$plan->duration_days} days on {$plan->name} plan.",
                'currency'            => $request->currency ?? ($gateway->currency ?? 'NGN'),
                'total_amount'        => $finalAmount * 100, // kobo
                'tax_amount'          => $taxAmount * 100,
                'discount_amount'     => $discountAmount * 100,
                'status'              => 'unpaid',
                'due_date'            => $request->due_date ?? now()->addDay(),
                'notes'               => $request->notes ?? "Auto-generated invoice for subscription.",
                'meta'                => [
                    'coupon' => $request->coupon ?? null
                ],
            ]);

            // Main Plan Item
            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'name'        => "{$plan->name} Plan",
                'description' => "Subscription for {$plan->duration_days} days",
                'quantity'    => 1,
                'unit_price'  => $plan->price * 100,
                'total_price' => $plan->price * 100,
            ]);

            // Optional Discount item (as negative line item)
            if ($discountAmount > 0) {
                InvoiceItem::create([
                    'invoice_id'  => $invoice->id,
                    'name'        => 'Discount',
                    'description' => $request->coupon ? "Coupon: {$request->coupon}" : 'Discount',
                    'quantity'    => 1,
                    'unit_price'  => -$discountAmount * 100,
                    'total_price' => -$discountAmount * 100,
                ]);
            }

            activity()
              ->causedBy(Auth::user())
              ->performedOn($subscription)
              ->withProperties(['plan' => $subscription->plan->name])
              ->log('Initiates Subscription to plan');


            SubscriptionAudit::create([
                'school_id'   => $school->id,
                'user_id'     => Auth::id(),
                'old_plan_id' => null,
                'new_plan_id' => $plan->id,
                'action'      => 'subscribe',
                'notes'       => "New subscription: {$plan->name}" . ($request->coupon ? " (Coupon: {$request->coupon})" : ''),
            ]);

            $redirectUrl = route('user.invoices.show', $invoice->id);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => $redirectUrl,
                    'invoice_id' => $invoice->id,
                ]);
            }

            return redirect($redirectUrl)->with('success', 'Subscription created. Please complete payment.');
        }

        // Free Plan Flow
        SubscriptionAudit::create([
            'school_id'   => $school->id,
            'user_id'     => Auth::id(),
            'old_plan_id' => null,
            'new_plan_id' => $plan->id,
            'action'      => 'subscribe',
            'notes'       => "Free plan: {$plan->name}",
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Free subscription activated.',
            ]);
        }

        return redirect()->back()->with('success', 'Free subscription activated.');
    }



    public function renew(Request $request, School $school)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'coupon_code' => 'nullable|string|max:50',
            'payment_gateway_slug' => 'required|exists:payment_gateways,slug',
        ]);

        $subscription = $school->activeSubscription;

        if ($school->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if (!$subscription) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active subscription to renew.'
                ], 404);
            }
            return redirect()->back()->with('error', 'No active subscription to renew.');
        }

        $plan = Plan::findOrFail($request->plan_id);

        // New subscription record
        $newSubscription = $school->subscriptions()->create([
            'user_id'    => Auth::id(),
            'plan_id'    => $plan->id,
            'start_date' => now(),
            'end_date'   => now()->addDays($plan->duration_days),
            'status'     => 'pending',
        ]);

        $this->handleReferral(Auth::user(), $subscription);

        $gateway = PaymentGateway::where('slug', $request->payment_gateway_slug)->firstOrFail();
        $currency = $gateway->currency ?? 'NGN';

        // Tax calculation
        $taxPercentage = Setting::get('tax_percentage', 0);
        $taxAmount = round(($plan->price * $taxPercentage) / 100, 2);

        // Handle discount via coupon (example only, you can expand it)
        $discountAmount = 0;
        $coupon = null;

        if ($request->filled('coupon_code')) {
            $coupon = Coupon::where('code', $request->coupon_code)
                            ->where('is_active', true)
                            ->where(function ($q) use ($plan) {
                                $q->whereNull('plan_id')->orWhere('plan_id', $plan->id);
                            })->first();

            if ($coupon) {
                if ($coupon->type === 'percentage') {
                    $discountAmount = round(($plan->price * $coupon->value) / 100, 2);
                } else {
                    $discountAmount = min($coupon->value, $plan->price); // cap it at plan price
                }
            }
        }

        $subtotal = $plan->price;
        $totalAmount = ($subtotal - $discountAmount + $taxAmount) * 100;

        // Create invoice
        $invoice = Invoice::create([
            'user_id'            => Auth::id(),
            'school_id'          => $school->id,
            'plan_id'            => $plan->id,
            'subscription_id'    => $newSubscription->id,
            'payment_gateway_id' => $gateway->id ?? null,
            'title'              => "{$plan->name} Plan Renewal",
            'description'        => "Renewal for {$plan->duration_days} days on {$plan->name} plan.",
            'currency'           => $currency,
            'total_amount'       => $totalAmount * 100,
            'tax_amount'         => $taxAmount * 100,
            'discount_amount'    => $discountAmount * 100,
            'status'             => 'unpaid',
            'due_date'           => now()->addDay(),
            'notes'              => "Auto-generated invoice for renewal.",
            'meta'               => ['coupon' => $coupon?->code],
        ]);

        // Plan item
        InvoiceItem::create([
            'invoice_id'  => $invoice->id,
            'name'        => "{$plan->name} Plan",
            'description' => "Renewal for {$plan->duration_days} days",
            'quantity'    => 1,
            'unit_price'  => $plan->price * 100,
            'total_price' => $plan->price * 100,
        ]);

        // Discount item (if any)
        if ($discountAmount > 0) {
            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'name'        => "Discount" . ($coupon ? " ({$coupon->code})" : ""),
                'description' => "Applied discount",
                'quantity'    => 1,
                'unit_price'  => -$discountAmount * 100,
                'total_price' => -$discountAmount * 100,
            ]);
        }

        // Tax item (optional if you want line item breakdown)
        if ($taxAmount > 0) {
            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'name'        => "Tax",
                'description' => "{$taxPercentage}% Tax",
                'quantity'    => 1,
                'unit_price'  => $taxAmount * 100,
                'total_price' => $taxAmount * 100,
            ]);
        }

        activity()
              ->causedBy(Auth::user())
              ->performedOn($subscription)
              ->withProperties(['plan' => $subscription->plan->name])
              ->log('Subscription renewal initiated');

        // Log audit
        SubscriptionAudit::create([
            'school_id'   => $school->id,
            'user_id'     => Auth::id(),
            'old_plan_id' => $subscription->plan_id,
            'new_plan_id' => $plan->id,
            'action'      => 'renew',
            'notes'       => "Renewed plan: {$plan->name} (previous: {$subscription->plan->name})",
        ]);

        // Free plan
        if ($plan->price <= 0) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Subscription renewed successfully (free plan).',
                ]);
            }
            return redirect()->back()->with('success', 'Subscription renewed successfully (free plan).');
        }

        // Paid plan: redirect to invoice
        $redirectUrl = route('user.invoices.show', $invoice->id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect_url' => $redirectUrl,
                'invoice_id' => $invoice->id
            ]);
        }

        return redirect($redirectUrl)->with('success', 'Renewal started. Please complete payment.');
    }



    public function upgrade(Request $request, School $school)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'payment_gateway_slug' => 'required|exists:payment_gateways,slug',
            'coupon_code' => 'nullable|string',
        ]);

        $newPlan = Plan::findOrFail($request->plan_id);
        $gateway = PaymentGateway::where('slug', $request->payment_gateway_slug)->firstOrFail();

        $subscription = $school->subscription;

        if ($school->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Cancel old subscription if active
        $existing = $school->currentSubscription()->first();
        if ($existing && $existing->status === 'active') {
            $existing->update(['status' => 'cancelled']);
        }

        // Create new subscription
        $subscription = Subscription::create([
            'school_id'           => $school->id,
            'plan_id'             => $newPlan->id,
            'user_id'             => Auth::id(),
            'payment_gateway_id'  => $newPlan ? null : $request->gateway_id,
            'external_reference'  => $newPlan ? null : $request->reference,
            'status'              => 'pending',
            'starts_at'           => now(),
            'ends_at'             => now()->addDays($newPlan->duration_days),
        ]);

        // $this->handleReferral(Auth::user(), $subscription);

        // Coupon logic
        $coupon = null;
        $discountAmount = 0;

        if ($request->filled('coupon_code')) {
            $coupon = Coupon::where('code', $request->coupon_code)
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->first();

            if ($coupon) {
                if ($coupon->type === 'percent') {
                    $discountAmount = round(($newPlan->price * $coupon->value) / 100, 2);
                } elseif ($coupon->type === 'fixed') {
                    $discountAmount = min($coupon->value, $newPlan->price);
                }
            }
        }

        // Tax calculation
        $taxPercentage = Setting::get('tax_percentage', 0);
        $taxableAmount = $newPlan->price - $discountAmount;
        $taxAmount = round(($taxableAmount * $taxPercentage) / 100, 2);

        // Total amount for invoice (multiply by 100 if gateway like Paystack)
        $totalAmount = $taxableAmount + $taxAmount;


        // dd($subscription);
        $invoice = Invoice::create([
            'user_id'             => Auth::id(),
            'school_id'           => $school->id,
            'plan_id'             => $newPlan->id,
            'subscription_id'     => $subscription->id,
            'payment_gateway_id'  => $gateway?->id,
            'title'               => "Upgrade to {$newPlan->name} Plan",
            'description'         => "Upgrade for {$newPlan->duration_days} days.",
            'currency'            => $gateway?->currency ?? 'NGN',
            'total_amount'        => $totalAmount * 100, // convert to kobo if needed
            'tax_amount'          => $taxAmount * 100,
            'discount_amount'     => $discountAmount * 100,
            'status'              => 'unpaid',
            'due_date'            => now()->addDay(),
            'notes'               => "Auto-generated invoice for upgrade.",
            'meta'                => ['coupon' => $coupon?->code],
        ]);

        // Base plan item
        InvoiceItem::create([
            'invoice_id'  => $invoice->id,
            'name'        => "{$newPlan->name} Plan",
            'description' => "Upgrade for {$newPlan->duration_days} days",
            'quantity'    => 1,
            'unit_price'  => $newPlan->price * 100,
            'total_price' => $newPlan->price * 100,
        ]);

        // Discount item
        if ($discountAmount > 0) {
            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'name'        => "Discount ({$coupon->code})",
                'description' => $coupon->type === 'percent'
                    ? "{$coupon->value}% off"
                    : "₦" . number_format($coupon->value, 2),
                'quantity'    => 1,
                'unit_price'  => -$discountAmount * 100,
                'total_price' => -$discountAmount * 100,
            ]);
        }

        // Tax item
        if ($taxAmount > 0) {
            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'name'        => "VAT ({$taxPercentage}%)",
                'description' => "Tax on subscription",
                'quantity'    => 1,
                'unit_price'  => $taxAmount * 100,
                'total_price' => $taxAmount * 100,
            ]);
        }

        activity()
              ->causedBy(Auth::user())
              ->performedOn($subscription)
              ->withProperties(['plan' => $subscription->plan->name])
              ->log('Subscription upgrade initiated');

              // Log audit
        SubscriptionAudit::create([
            'school_id'   => $school->id,
            'user_id'     => Auth::id(),
            'old_plan_id' => $subscription->plan_id,
            'new_plan_id' => $newPlan->id,
            'action'      => 'renew',
            'notes'       => "Renewed plan: {$newPlan->name} (previous: {$subscription->plan->name})",
        ]);

        // Paid plan: redirect to invoice
        $redirectUrl = route('user.invoices.show', $invoice->id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect_url' => $redirectUrl,
                'invoice_id' => $invoice->id
            ]);
        }

        return redirect()->route('user.invoices.show', $invoice->id)
            ->with('success', 'Upgrade invoice created. Proceed to payment.');
    }



    // public function subscribe(Request $request, School $school)
    // {
    //     $request->validate([
    //         'plan_id' => 'required|exists:plans,id',
    //     ]);

    //     $plan = Plan::findOrFail($request->plan_id);
    //     $now = now();

    //     // Check for existing active subscription
    //     $existing = Subscription::where('school_id', $school->id)
    //         ->where('status', 'active')
    //         ->whereDate('ends_at', '>=', $now)
    //         ->first();

    //     $action = 'new';

    //     if ($existing) {
    //         // Determine action
    //         $action = $plan->price > $existing->plan->price ? 'upgrade' : 'downgrade';

    //         // Cancel the current subscription
    //         $existing->update([
    //             'status' => 'cancelled',
    //             'cancelled_at' => $now,
    //             'grace_ends_at' => $existing->ends_at,
    //         ]);

    //         // Audit the change
    //         SubscriptionAudit::create([
    //             'school_id' => $school->id,
    //             'user_id' => Auth::id(),
    //             'old_plan_id' => $existing->plan_id,
    //             'new_plan_id' => $plan->id,
    //             'action' => $action,
    //             'notes' => "Switched from {$existing->plan->name} to {$plan->name}.",
    //         ]);
    //     }

    //     // Create new subscription
    //     $subscription = Subscription::create([
    //         'user_id' => Auth::id(),
    //         'school_id' => $school->id,
    //         'plan_id' => $plan->id,
    //         'status' => 'active',
    //         'starts_at' => $now,
    //         'ends_at' => $now->copy()->addDays($plan->duration_days),
    //     ]);

    //     // Trigger event
    //     event(new SubscriptionChanged($school, Auth::user(), $existing?->plan, $plan, $action));

    //     return response()->json([
    //         'success' => true,
    //         'message' => "Subscribed to {$plan->name} plan for {$plan->duration_days} days.",
    //         'subscription' => $subscription,
    //     ]);
    // }



    public function history(School $school)
    {
      // dd([
      //     'school_id' => $school->id,
      //     'subscriptions_query' => $school->subscriptions()->toSql(),
      //     'bindings' => $school->subscriptions()->getBindings(),
      //     'count' => $school->subscriptions()->count()
      // ]);

        // Load subscriptions for this school, with related plan and user
        $subscriptions = $school->subscriptions()->with('plan', 'user')->latest()->paginate(20);

        // dd($subscriptions);
        return view('user.schools.subscriptions.history', compact('school', 'subscriptions'));
    }

    public function cancel(Request $request, $schoolId)
    {
        $school = School::findOrFail($schoolId);

        if ($school->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $subscription = $school->currentSubscription()->first();

        if (!$subscription || $subscription->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'No active subscription to cancel.'], 422);
        }

        // Prevent cancelling the free plan
        if ($subscription->plan && $subscription->plan->slug === 'free') {
            return response()->json(['success' => false, 'message' => 'Free plan cannot be cancelled.'], 422);
        }

        $now = now();

        // Cancel the subscription
        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => $now,
            'grace_ends_at' => $subscription->ends_at,
        ]);

        $freePlan = Plan::where('slug', 'free')->first();

        // Audit the cancellation
        \App\Models\SubscriptionAudit::create([
            'school_id'    => $school->id,
            'user_id'      => Auth::id(),
            'old_plan_id'  => $subscription->plan_id,
            'new_plan_id'  => $freePlan?->id, // default to free,
            'action'       => 'cancel',
            'notes'        => "Cancelled subscription for plan {$subscription->plan->name}.",
        ]);

        // Optionally update school billing status
        $school->is_paid_plan = false;
        $school->save();

        return response()->json([
            'success' => true,
            'message' => 'Subscription cancelled successfully.',
        ]);
    }


    // Get current active subscription info (AJAX GET)
    public function currentSubscription(School $school)
    {
        $user = Auth::user();

        $subscription = Subscription::where('user_id', $user->id)
            ->where('school_id', $school->id)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
            })
            ->with('plan')
            ->latest('ends_at')
            ->first();

        if (!$subscription) {
            return response()->json(['subscription' => null]);
        }

        return response()->json(['subscription' => $subscription]);
    }

    public function getPrice(Plan $plan)
    {
        $gateway = PaymentGateway::where('is_active', true)->first(); // or based on request input

        return response()->json([
            'amount' => $plan->price,
            'gateway' => [
                'slug' => $gateway->slug,
                'public_key' => $gateway->public_key,
                'currency' => $gateway->currency,
            ]
        ]);
    }

    public function handleReferral(User $user, Subscription $subscription)
    {
        // Example: check if referral code was stored during registration
        $referrer = User::where('referral_code', $user->referred_by_code)->first();

        // 1. Prevent self-referrals
        if (!$referrer || $referrer->id === $user->id) {
            return; // invalid or self-referral, ignore
        }

        // 2. Avoid duplicate commissions (one referral per referred user)
        $existing = AffiliateCommission::where('referred_user_id', $user->id)->first();
        if ($existing) {
            return; // already rewarded, no duplicate
        }

        // Get system-wide affiliate commission percentage from settings
            $commissionRate = (float) \App\Models\Setting::get('affiliate_commission_percentage', 10); // default to 10%
            $commissionRateDecimal = $commissionRate / 100;
            $amount = $subscription->plan->price * $commissionRateDecimal;

        // 3. Reward the referrer (one-time commission)
        AffiliateCommission::create([
            'user_id' => $referrer->id, // referrer
            'referred_user_id' => $user->id,
            'amount' => $amount, // or based on your logic
            'status' => 'pending',
        ]);
    }


}
