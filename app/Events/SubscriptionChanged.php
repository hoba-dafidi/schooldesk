<?php

namespace App\Events;

use App\Models\School;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class SubscriptionChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $school;
    public $user;
    public $oldPlan;
    public $newPlan;
    public $action;

    /**
     * Create a new event instance.
     */
    public function __construct(School $school, User $user, ?Plan $oldPlan, Plan $newPlan, string $action)
    {
        $this->school = $school;
        $this->user = $user;
        $this->oldPlan = $oldPlan;
        $this->newPlan = $newPlan;
        $this->action = $action;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('subscription.' . $this->school->id),
        ];
    }

    public function broadcastAs(): string
      {
          return 'subscription.changed';
      }

}
