<?php

return [
    'default' => env('AUTH_GUARD', 'web'),

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'api' => [
            'driver' => 'sanctum',
            'provider' => 'users',
            'hash' => false,
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

    'roles' => [
        'guest' => 1,
        'client' => 2,
        'photographer' => 3,
        'studio_owner' => 4,
        'studio_manager' => 5,
        'studio_photographer' => 6,
        'moderator' => 7,
        'admin' => 8,
        'super_admin' => 9,
    ],

    'permissions' => [
        'view_photographers' => true,
        'view_profile' => true,
        'create_inquiry' => true,
        'create_booking' => true,
        'create_review' => true,
        'manage_portfolio' => true,
        'manage_packages' => true,
        'view_analytics' => true,
        'manage_moderators' => true,
        'manage_system' => true,
    ],
];
