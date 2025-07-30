<?php

namespace App\Observers;

use App\Models\Affiliate;
use App\Models\User;
use App\Services\MlmReferralService;
use Illuminate\Support\Str;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        if ($user->referred_by) {
            $referrer = User::find($user->referred_by);

            if ($referrer) {
                app(MlmReferralService::class)->registerReferral($user, $referrer);
            }
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
