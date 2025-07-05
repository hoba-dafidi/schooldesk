<?php

use Illuminate\Support\Facades\Broadcast;


Broadcast::channel('ticket-{ticketId}', function ($user, $ticketId) {
    return \App\Models\Ticket::where('id', $ticketId)
        ->where(function ($q) use ($user) {
            $q->where('user_id', $user->id)->orWhere('assigned_to', $user->id);
        })->exists();
});
