<?php

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;

if (!function_exists('getStatusColor')) {
    function getStatusColor(TicketStatus|string $status): string
    {
        $value = $status instanceof TicketStatus ? $status->value : $status;

        return match ($value) {
            'open' => 'warning',
            'in_progress' => 'primary',
            'awaiting_reply' => 'info',
            'closed' => 'success',
            default => 'secondary',
        };
    }
}

if (!function_exists('getPriorityColor')) {
    function getPriorityColor(TicketPriority|string $priority): string
    {
        $value = $priority instanceof TicketPriority ? $priority->value : $priority;

        return match ($value) {
            'low' => 'secondary',
            'medium' => 'info',
            'high' => 'warning',
            'urgent' => 'danger',
            default => 'secondary',
        };
    }
}
