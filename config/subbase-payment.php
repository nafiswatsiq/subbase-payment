<?php

return [
    'driver' => env('SUBBASE_PAYMENT_DRIVER'),

    'webhook' => [
        'path' => 'subbase-payment/webhook',
        'middleware' => [],
    ],

    'gateways' => [],
];
