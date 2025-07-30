<?php

namespace App\Listeners;

use App\Events\SubscriptionActivated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendSubscriptionActivatedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SubscriptionActivated $event)
    {
        $subscription = $event->subscription;
        $user = $subscription->user;

        // Example: send email or notification
        // $user->notify(new SubscriptionActivatedNotification($subscription));
    }
}
