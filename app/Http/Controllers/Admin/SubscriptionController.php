<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Notifications\InvoiceNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Display a paginated list of all subscriptions (admin only).
     */
    public function index(Request $request)
{
    $subscriptions = \App\Models\Subscription::with(['user', 'plan', 'gateway'])
        ->when($request->user, fn($q) => $q->whereHas('user', fn($qq) => $qq->where('email', 'like', '%' . $request->user . '%')))
        ->when($request->plan, fn($q) => $q->where('plan_id', $request->plan))
        ->when($request->status, fn($q) => $q->where('status', $request->status))
        ->latest()
        ->paginate(10);

    $plans = \App\Models\Plan::select('id', 'name')->get();

    return view('admin.subscriptions.index', compact('subscriptions', 'plans'));
}

    /**
     * Display detailed info about a specific subscription.
     */
    public function show(Subscription $subscription)
    {
        $subscription->load(['user', 'plan', 'paymentGateway']);

        return view('admin.subscriptions.show', compact('subscription'));
    }

    public function cancel(Subscription $subscription)
    {
        $subscription->update([
            'status' => 'cancelled',
            'ends_at' => now(),
            'cancelled_at' => now(),
        ]);
        return back()->with('success', 'Subscription cancelled.');
    }

    public function resume(Subscription $subscription)
    {
        $subscription->update([
            'status' => 'active',
            'ends_at' => null,
            'cancelled_at' => null,
        ]);
        return back()->with('success', 'Subscription resumed.');
    }

    public function resendInvoice(Subscription $subscription)
    {
        // Your logic to resend the invoice email
        $subscription->user->notify(new InvoiceNotification($subscription));
        return redirect()->back()->with('success', 'Invoice resent successfully.');
    }

    public function export(Request $request)
    {
        $query = Subscription::with(['user', 'plan', 'paymentGateway']);

        // Apply filters same as your index
        if ($request->filled('user')) {
            $query->whereHas('user', fn($q) => $q->where('email', 'like', '%' . $request->user . '%'));
        }
        if ($request->filled('plan')) {
            $query->where('plan_id', $request->plan);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $subscriptions = $query->get();

        $format = $request->get('format', 'csv');

        if ($format === 'pdf') {
            $pdf = PDF::loadView('admin.subscriptions.export_pdf', compact('subscriptions'));
            return $pdf->download('subscriptions_' . now()->format('Ymd_His') . '.pdf');
        }

        // Default CSV Export
        $filename = 'subscriptions_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['User Name', 'User Email', 'Plan', 'Status', 'Quantity', 'Gateway', 'Starts At', 'Ends At', 'Renews At'];

        $callback = function() use ($subscriptions, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($subscriptions as $subscription) {
                fputcsv($file, [
                    $subscription->user->name,
                    $subscription->user->email,
                    $subscription->plan->name,
                    ucfirst($subscription->status),
                    $subscription->quantity,
                    $subscription->paymentGateway->name ?? 'Manual',
                    $subscription->starts_at?->format('Y-m-d H:i') ?? '-',
                    $subscription->ends_at?->format('Y-m-d H:i') ?? '-',
                    $subscription->renews_at?->format('Y-m-d H:i') ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

}
