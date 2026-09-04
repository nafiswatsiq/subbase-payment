<?php

return [
    'driver' => env('SUBBASE_PAYMENT_DRIVER'),

    'checkout' => [
        'path' => 'checkout',
        'middleware' => ['web'],
        'return_url' => null,
        'cancel_url' => null,
    ],

    'tables' => [
        'subscription_payments' => 'subscription_payments',
    ],

    'webhook' => [
        'path' => 'subbase-payment/webhook',
        'middleware' => [],
    ],

    'gateways' => [
        'paypal' => [
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'secret' => env('PAYPAL_SECRET'),
            'base_url' => env('PAYPAL_BASE_URL', 'https://api-m.sandbox.paypal.com'),
            'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
        ],
    ],
];
