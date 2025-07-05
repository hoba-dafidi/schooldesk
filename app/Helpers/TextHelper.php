<?php

if (!function_exists('excerpt')) {
    function excerpt(string $text, int $limit = 50): string
    {
        return strlen($text) > $limit ? substr($text, 0, $limit) . '...' : $text;
    }
}
