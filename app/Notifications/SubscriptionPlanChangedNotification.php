<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionPlanChangedNotification extends Notification
{
    public $school, $oldPlan, $newPlan, $action;

    public function __construct($school, $oldPlan, $newPlan, $action)
    {
        $this->school = $school;
        $this->oldPlan = $oldPlan;
        $this->newPlan = $newPlan;
        $this->action = $action;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Subscription {$this->action} for {$this->school->name}")
            ->line("The subscription for {$this->school->name} has been {$this->action}.")
            ->line("Old Plan: " . ($this->oldPlan->name ?? 'None'))
            ->line("New Plan: " . $this->newPlan->name)
            ->action('View Dashboard', url('/admin/subscription-audits'))
            ->line('Thank you for using our service!');
    }
}
