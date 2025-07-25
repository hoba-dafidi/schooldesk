<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $activeTickets = $user->tickets()->whereIn('status', ['open', 'pending'])->count();

        $previousWeekTickets = $user->tickets()
          ->whereIn('status', ['open', 'pending'])
          ->whereBetween('created_at', [now()->subWeeks(2), now()->subWeek()])
          ->count();

        // Active subscriptions
        $activeSubscriptions = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('plan') // if there's a `plan()` relationship
            ->get();

        // Recent activity logs
        $recentActivities = Activity::causedBy(Auth::user())
            ->latest()
            ->take(5)
            ->get();

        $transactions = Invoice::with('paymentGateway')
            ->where('user_id', $user->id)
            ->latest('created_at')
            ->take(5)
            ->get();

        $totalSchools = $user->schools()->count();

        $totalInvoices = $user->invoices()->count();
        // Optional: Compare this week vs last week for trend
        $now = now();
        $startOfThisWeek = $now->startOfWeek();
        $startOfLastWeek = $startOfThisWeek->copy()->subWeek();
        $endOfLastWeek = $startOfThisWeek->copy()->subSecond();

        $invoicesLastWeek = $user->invoices()->whereBetween('created_at', [$startOfLastWeek, $endOfLastWeek])->count();
        $invoicesThisWeek = $user->invoices()->where('created_at', '>=', $startOfThisWeek)->count();

        $invoiceGrowth = $invoicesLastWeek > 0
            ? round((($invoicesThisWeek - $invoicesLastWeek) / $invoicesLastWeek) * 100, 1)
            : ($invoicesThisWeek > 0 ? 100 : 0);

        $trendColor = $invoiceGrowth > 0 ? 'success' : ($invoiceGrowth < 0 ? 'danger' : 'secondary');
        $trendIcon = $invoiceGrowth > 0 ? 'bx-up-arrow-alt' : ($invoiceGrowth < 0 ? 'bx-down-arrow-alt' : 'bx-minus');

        $customDomainsCount = $user->customDomains()->count();

        $domainsLastWeek = $user->customDomains()->whereBetween('custom_domains.created_at', [$startOfLastWeek, $endOfLastWeek])->count();
        $domainsThisWeek = $user->customDomains()->where('custom_domains.created_at', '>=', $startOfThisWeek)->count();

        $domainGrowth = $domainsLastWeek > 0
            ? round((($domainsThisWeek - $domainsLastWeek) / $domainsLastWeek) * 100, 1)
            : ($domainsThisWeek > 0 ? 100 : 0);

        $domainTrendColor = $domainGrowth > 0 ? 'success' : ($domainGrowth < 0 ? 'danger' : 'secondary');
        $domainTrendIcon = $domainGrowth > 0 ? 'bx-up-arrow-alt' : ($domainGrowth < 0 ? 'bx-down-arrow-alt' : 'bx-minus');

        $notifications = Notification::where('notifiable_type', get_class($user))
          ->where('notifiable_id', $user->id)
          ->latest()
          ->take(5)
          ->get();


        activity()->log('User opened the dashboard');

        return view('user.dashboard', compact('user', 'activeTickets', 'previousWeekTickets',
        'activeSubscriptions', 'recentActivities', 'transactions', 'totalSchools',
        'totalInvoices', 'trendIcon', 'trendColor', 'invoiceGrowth',
        'customDomainsCount', 'domainGrowth', 'domainTrendColor', 'domainTrendIcon',
        'notifications'));
    }
}
