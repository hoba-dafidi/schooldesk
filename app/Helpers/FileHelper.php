<?php

if (!function_exists('humanFileSize')) {
    function humanFileSize($bytes, $decimals = 2): string
    {
        $size = ['B','KB','MB','GB','TB','PB'];
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }
}

if (!function_exists('isImage')) {
    function isImage(string $mime): bool
    {
        return str_starts_with($mime, 'image/');
    }
}
