<?php

namespace App\Observers;

use App\Models\AffiliateCommission;
use App\Models\Subscription;

class SubscriptionObserver
{
    /**
     * Handle the Subscription "created" event.
     */
    public function created(Subscription $subscription): void
    {
        //
    }

    /**
     * Handle the Subscription "updated" event.
     */
    public function updated(Subscription $subscription): void
    {
        // Only proceed if status changed to 'active'
        if (
            $subscription->getOriginal('status') !== 'active' &&
            $subscription->status === 'active'
        ) {
            $user = $subscription->user;

            // Record commission if user was referred
            AffiliateCommission::createForSubscription($user, $subscription);

        }
    }


    /**
     * Handle the Subscription "deleted" event.
     */
    public function deleted(Subscription $subscription): void
    {
        //
    }

    /**
     * Handle the Subscription "restored" event.
     */
    public function restored(Subscription $subscription): void
    {
        //
    }

    /**
     * Handle the Subscription "force deleted" event.
     */
    public function forceDeleted(Subscription $subscription): void
    {
        //
    }
}
