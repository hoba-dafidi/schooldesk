<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ticket;

class TicketPolicy
{
    /**
     * Determine if the user can view the ticket.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->user_id || $user->id === $ticket->assigned_to || $user->hasRole('admin');
    }

    /**
     * Determine if the user can update the ticket.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->user_id || $user->id === $ticket->assigned_to || $user->hasRole('admin');
    }

    /**
     * Determine if the user can delete the ticket.
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine if the user can assign a ticket.
     */
    public function assign(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasPermissionTo('assign tickets');
    }

    /**
     * Determine if the user can reply to a ticket.
     */
    public function reply(User $user, Ticket $ticket): bool
    {
        return $this->view($user, $ticket);
    }
}
