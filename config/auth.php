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

        // ✅ Standard user API (Sanctum)
        'api' => [
            'driver' => 'sanctum',
            'provider' => 'users',
        ],

        // ✅ Vendor API guard
        'vendor-api' => [
            'driver' => 'sanctum',
            'provider' => 'vendors',
            'hash' => false,
        ],

        // ✅ Rider API guard
        'rider-api' => [
            'driver' => 'sanctum',
            'provider' => 'riders',
            'hash' => false,
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
