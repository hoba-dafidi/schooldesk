<?php

namespace App\Events;

use App\Models\TicketMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;

class TicketMessageCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticketMessage;
    public $html;

    public function __construct(TicketMessage $ticketMessage, $html)
    {
        $this->ticketMessage = $ticketMessage;
        $this->html = $html;
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('ticket-' . $this->ticketMessage->ticket_id);
    }

    public function broadcastAs(): string
    {
        return 'message.created';
    }

    public function broadcastWith(): array
    {
        return ['view' => $this->html];
    }
}
