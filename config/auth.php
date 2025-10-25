<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'sanctum',
            'provider' => 'users',
        ],

        'vendor-api' => [
            'driver' => 'sanctum',
            'provider' => 'vendors',
        ],

        'admins' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],

        'superadmins' => [
            'driver' => 'session',
            'provider' => 'superadmins',
        ],

        'pandits' => [
            'driver' => 'session',
            'provider' => 'pandits',
        ],

        'users' => [
        'driver' => 'session',
        'provider' => 'users',

    ],
     'superadmins' => [
        'driver' => 'session',
        'provider' => 'superadmins',
    ],
    'admins' => [
        'driver' => 'session',
        'provider' => 'admins',
    ],

    'rider-api' => [
        'driver' => 'sanctum', // Ensure you are using Sanctum or Passport
        'provider' => 'riders', // Matches your 'riders' provider
        'hash' => false,        // Set to true only if using hashed tokens
    ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],
        'superadmins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Superadmin::class,
        ],
        'pandits' => [
            'driver' => 'eloquent',
            'model' => App\Models\PanditLogin::class,
        ],
        'riders' => [
            'driver' => 'eloquent',
            'model' => App\Models\RiderDetails::class,
        ],
        'vendors' => [
            'driver' => 'eloquent',
            'model' => App\Models\FlowerVendor::class,
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
];
