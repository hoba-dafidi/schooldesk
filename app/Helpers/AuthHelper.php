<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('isAdmin')) {
    function isAdmin(): bool
    {
        return Auth::check() && Auth::user()->hasRole('admin');
    }
}

if (!function_exists('hasRole')) {
    function hasRole(string $role): bool
    {
        return Auth::check() && Auth::user()->hasRole($role);
    }
}

if (!function_exists('userName')) {
    function userName(): ?string
    {
        return Auth::check() ? Auth::user()->name : null;
    }
}
