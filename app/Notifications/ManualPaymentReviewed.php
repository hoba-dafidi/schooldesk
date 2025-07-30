<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ManualPaymentReviewed extends Notification
{
    public $payment;

    public function __construct($payment)
    {
        $this->payment = $payment;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting("Hello {$notifiable->name},")
            ->line("Your manual payment for Invoice #{$this->payment->invoice->invoice_number} has been reviewed.")
            ->line("Status: **" . ucfirst($this->payment->status) . "**")
            ->action('View Invoice', url(route('user.invoices.show', $this->payment->invoice->id)))
            ->line('Thank you for your patience.');
    }
}
