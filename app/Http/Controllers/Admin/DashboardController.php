<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\CustomDomain;
use App\Models\Invoice;
use App\Models\School;
use App\Models\Subscription;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index()
{
    $totalSchools = School::count();

    // Custom domains (assuming School has custom_domain column)
    $customDomainsCount = CustomDomain::count();

    // Custom domain growth (this month vs last month)
    $currentMonthDomains = CustomDomain::whereMonth('created_at', now()->month)->count();
    $lastMonthDomains = CustomDomain::whereMonth('created_at', now()->subMonth()->month)->count();

    $domainGrowth = $lastMonthDomains > 0
        ? round((($currentMonthDomains - $lastMonthDomains) / $lastMonthDomains) * 100, 2)
        : 100;

    $domainTrendColor = $domainGrowth >= 0 ? 'success' : 'danger';
    $domainTrendIcon = $domainGrowth >= 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt';

    // Invoices via subscriptions' related plan price
    $totalInvoices = Subscription::count();

    $thisMonthRevenue = Subscription::whereMonth('created_at', now()->month)
        ->with('plan')
        ->get()
        ->sum(fn($s) => $s->plan?->price ?? 0);

    $lastMonthRevenue = Subscription::whereMonth('created_at', now()->subMonth()->month)
        ->with('plan')
        ->get()
        ->sum(fn($s) => $s->plan?->price ?? 0);

    $invoiceGrowth = $lastMonthRevenue > 0
        ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 2)
        : 100;

    $trendColor = $invoiceGrowth >= 0 ? 'success' : 'danger';
    $trendIcon = $invoiceGrowth >= 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt';

    // Active tickets (not closed)
    $activeTickets = Ticket::where('status', '!=', 'closed')->count();

    $notifications = DatabaseNotification::where('notifiable_id', auth()->id())
        ->where('notifiable_type', User::class)
        ->latest()
        ->take(10)
        ->get();

    $newUsersThisWeek = User::whereBetween('created_at', [
        Carbon::now()->startOfWeek(),
        Carbon::now()->endOfWeek(),
    ])->count();

    $activeUsers = User::where('is_admin', false)
        ->whereNotNull('last_seen_at')
        ->where('last_seen_at', '>=', Carbon::now()->subDays(7))
        ->count();

    // Total users (excluding admins, if needed)
    $totalUsers = User::where('is_admin', false)->count();

    // Calculate percentage
    $activeUsersPercent = $totalUsers > 0
        ? round(($activeUsers / $totalUsers) * 100, 1)
        : 0;

    // Active/open tickets (not resolved or closed)
    $openTickets = Ticket::whereNotIn('status', [TicketStatus::Resolved, TicketStatus::Closed])->count();

    // In Progress Tickets
    $inProgressTickets = Ticket::where('status', TicketStatus::InProgress)->count();

    // Resolved Tickets
    $resolvedTickets = Ticket::where('status', TicketStatus::Resolved)->count();

    // Overdue Tickets (e.g., unresolved and created more than 5 days ago)
    $overdueTickets = Ticket::whereNotIn('status', [TicketStatus::Resolved, TicketStatus::Closed])
        ->where('created_at', '<', now()->subDays(5)) // or any logic you define as overdue
        ->count();

    $activities = Activity::whereHas('causer.roles', function ($query) {
        $query->where('name', 'admin');
    })->latest()->limit(10)->get();

    $invoices = Invoice::latest()->limit(10)->get();

    // Revenue this month
    $currentMonthRevenue = Invoice::whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('total_amount');

    // Revenue last month
    $lastMonth = now()->subMonth();
    $lastMonthRevenue = Invoice::whereMonth('created_at', $lastMonth->month)
        ->whereYear('created_at', $lastMonth->year)
        ->sum('total_amount');

    // Calculate growth
    $revenueGrowth = 0;
    if ($lastMonthRevenue > 0) {
        $revenueGrowth = round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 2);
    }
    $totalRevenue = $currentMonthRevenue;

    $today = Carbon::now();

    // Active subscriptions
    $activeSubscriptions = Subscription::where('status', 'active')->count();

    // New subscriptions this week
    $newSubscriptions = Subscription::whereBetween('created_at', [
        $today->startOfWeek(), $today->endOfWeek()
    ])->count();

    // Expired subscriptions (status = expired or end_date < today)
    $expiredSubscriptions = Subscription::where('status', 'expired')
        ->orWhereDate('ends_at', '<', $today)
        ->count();

    // Last week's new subscriptions
    $lastWeekNew = Subscription::whereBetween('created_at', [
        $today->subWeek()->startOfWeek(), $today->subWeek()->endOfWeek()
    ])->count();

    // Reset date after subWeek()
    $today = Carbon::now();

    // Growth calculation
    $subscriptionGrowth = 0;
    if ($lastWeekNew > 0) {
        $subscriptionGrowth = round((($newSubscriptions - $lastWeekNew) / $lastWeekNew) * 100, 2);
    }


    return view('admin.dashboard', compact(
        'totalSchools',
        'customDomainsCount',
        'domainGrowth',
        'domainTrendColor',
        'domainTrendIcon',
        'totalInvoices',
        'invoiceGrowth',
        'trendColor',
        'trendIcon',
        'activeTickets',
        'notifications',
        'newUsersThisWeek',
        'activeUsers',
        'totalUsers',
        'activeUsersPercent',
        'openTickets',
        'inProgressTickets',
        'resolvedTickets',
        'overdueTickets',
        'activities',
        'invoices',
        'totalRevenue',
        'revenueGrowth',
        'activeSubscriptions',
        'newSubscriptions',
        'expiredSubscriptions',
        'subscriptionGrowth',
    ));
}

}
