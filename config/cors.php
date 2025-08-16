<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | You may configure your settings for cross-origin resource sharing or
    | "CORS" here. This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // In production, set CORS_ALLOWED_ORIGINS to a comma separated list of origins
    // e.g. https://your-domain,https://app.your-domain
    'allowed_origins' => (function () {
        $env = env('CORS_ALLOWED_ORIGINS', '*');
        // Simple support for comma-separated values. If '*', return as-is.
        if ($env === '*') {
            return ['*'];
        }
        $parts = array_filter(array_map('trim', explode(',', $env)));
        return !empty($parts) ? $parts : ['*'];
    })(),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];
