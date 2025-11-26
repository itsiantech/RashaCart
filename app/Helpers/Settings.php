<?php

use App\Models\Settings;

if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        return Settings::getAllSettings()[$key] ?? $default;
    }
}