<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Broadcasting\PrivateChannel;

class AdminBroadcastNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $message;

    public function __construct(array $message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => $this->message['message'],
            'level' => $this->message['level'],
            'icon' => $this->message['icon'],
            'urgency' => $this->message['urgency'] ?? 'info',
            'created_at' => now()->toDateTimeString(),
        ]);
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->message['message'],
            'level' => $this->message['level'],
            'icon' => $this->message['icon'],
            'urgency' => $this->message['urgency'] ?? 'info',
        ];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('App.Models.User.' . $notifiable->id);
    }
}
