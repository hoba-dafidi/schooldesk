<?php

if (!function_exists('formatCurrency')) {
    function formatCurrency(float|int $amount, string $currency = '₦'): string
    {
        return $currency . number_format($amount, 2);
    }
}
