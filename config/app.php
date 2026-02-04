<?php

return [
    'default' => env('APP_NAME', 'Photographer SB'),

    'name' => env('APP_NAME', 'Photographer SB'),

    'env' => env('APP_ENV', 'production'),

    'debug' => (bool) env('APP_DEBUG', false),

    'url' => env('APP_URL', 'https://photographersb.com'),

    'asset_url' => env('ASSET_URL'),

    'timezone' => 'Asia/Dhaka',

    'locale' => 'en',

    'fallback_locale' => 'en',

    'faker_locale' => 'en_US',

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    'build_version' => env('APP_BUILD_VERSION', now()->format('Y-m-d_H:i:s')),

    'api_prefix' => 'api/v1',

    'api_rate_limit' => [
        'per_minute' => 60,
        'per_hour' => 1000,
    ],

    'features' => [
        'photographers' => true,
        'competitions' => true,
        'events' => true,
        'subscriptions' => true,
        'payments' => true,
        'notifications' => true,
    ],
];
