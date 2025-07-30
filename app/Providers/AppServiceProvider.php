<?php

namespace App\Providers;

use App\Models\Subscription;
use App\Models\Ticket;
use App\Models\User;
use App\Observers\SubscriptionObserver;
use App\Observers\TicketObserver;
use App\Observers\UserObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Ticket::observe(TicketObserver::class);
        User::observe(UserObserver::class);
        Subscription::observe(SubscriptionObserver::class);
    }
}
