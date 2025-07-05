<?php

namespace App\Observers;

use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class TicketObserver
{
    public function creating(Ticket $ticket): void
    {
        if (Auth::check()) {
            $ticket->created_by = Auth::id();
        }

        // Optionally set default status
        $ticket->status = $ticket->status ?? \App\Enums\TicketStatus::Open;
    }

    public function updating(Ticket $ticket): void
    {
        if ($ticket->isDirty('status') && Auth::check()) {
            activity()
                ->causedBy(Auth::user())
                ->performedOn($ticket)
                ->withProperties(['old' => $ticket->getOriginal('status'), 'new' => $ticket->status])
                ->log('Ticket status changed');
        }

        if (Auth::check()) {
            $ticket->updated_by = Auth::id();
        }
    }
}
