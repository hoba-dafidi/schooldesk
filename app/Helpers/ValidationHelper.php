<?php

if (!function_exists('validationErrorsToString')) {
    function validationErrorsToString($errors): string
    {
        return collect($errors)->flatten()->implode(', ');
    }
}
