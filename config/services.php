<?php

return [
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID', 'your-google-client-id'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET', 'your-google-client-secret'),
        'redirect' => env('GOOGLE_REDIRECT_URL', env('APP_URL') . '/api/v1/auth/google/callback'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID', 'your-facebook-client-id'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET', 'your-facebook-client-secret'),
        'redirect' => env('FACEBOOK_REDIRECT_URL', env('APP_URL') . '/api/v1/auth/facebook/callback'),
    ],

    'apple' => [
        'client_id' => env('APPLE_CLIENT_ID', 'your-apple-client-id'),
        'client_secret' => env('APPLE_CLIENT_SECRET', 'your-apple-client-secret'),
        'redirect' => env('APPLE_REDIRECT_URL', env('APP_URL') . '/api/v1/auth/apple/callback'),
    ],
];
