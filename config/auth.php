<?php

return[
    'defaults' => [
        'guard' => 'api',
        'passwords' => 'oc_customer',
    ],

    'guards' => [
        'api' => [
            'driver' => 'jwt',
            'provider' => 'oc_customer',
        ],
    ],

    'providers' => [
        'oc_customer' => [
            'driver' => 'eloquent',
            'model' => \App\Models\Oc_customer::class
        ]
    ],

    'passwords' => [
        'oc_customer' => [
            'provider' => 'oc_customer',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
      ],
    'password_timeout' => 10800,
];