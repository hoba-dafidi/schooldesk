<?php

namespace App\Listeners;

use App\Events\SubscriptionChanged;
use App\Notifications\SubscriptionPlanChangedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendSubscriptionChangeNotification implements ShouldQueue
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
    public function handle(SubscriptionChanged $event)
    {
        // Notify the school's user (owner/admin)
        $recipient = $event->school->user ?? $event->user;

        if ($recipient) {
            $recipient->notify(new SubscriptionPlanChangedNotification(
                $event->school,
                $event->oldPlan,
                $event->newPlan,
                $event->action
            ));
        }
    }
}
