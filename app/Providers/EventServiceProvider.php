<?php

namespace App\Providers;

use App\Events\SubscriptionActivated;
use App\Events\SubscriptionChanged;
use App\Listeners\SendSubscriptionActivatedNotification;
use App\Listeners\SendSubscriptionChangeNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     */
    protected $listen = [
        SubscriptionChanged::class => [
            SendSubscriptionChangeNotification::class,
        ],
        SubscriptionActivated::class => [
            SendSubscriptionActivatedNotification::class,
        ],
    ];
}
