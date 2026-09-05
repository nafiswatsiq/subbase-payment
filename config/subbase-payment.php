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
        'payment_webhook_logs' => 'payment_webhook_logs',
    ],

    'permissions' => [
        'subscription_payment' => null,
        'payment_webhook_log' => null,
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
        'midtrans' => [
            'server_key' => env('MIDTRANS_SERVER_KEY'),
            'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
        ],
        'stripe' => [
            'secret_key' => env('STRIPE_SECRET_KEY'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        ],
    ],
];
