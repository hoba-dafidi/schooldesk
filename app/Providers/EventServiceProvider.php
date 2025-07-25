<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\SubscriptionChanged;
use App\Listeners\SendSubscriptionChangeNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     */
    protected $listen = [
        SubscriptionChanged::class => [
            SendSubscriptionChangeNotification::class,
        ],
    ];
}
