<?php

use Carbon\Carbon;

if (!function_exists('formatDate')) {
    function formatDate($date, string $format = 'd M Y, h:i A'): string
    {
        return Carbon::parse($date)->format($format);
    }
}

if (!function_exists('humanDate')) {
    function humanDate($date): string
    {
        return Carbon::parse($date)->diffForHumans();
    }
}

if (!function_exists('isToday')) {
    function isToday($date): bool
    {
        return Carbon::parse($date)->isToday();
    }
}

if (!function_exists('isPast')) {
    function isPast($date): bool
    {
        return Carbon::parse($date)->isPast();
    }
}
