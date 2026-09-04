<?php

use Illuminate\Support\Facades\Route;
use Nafiswatsiq\SubbasePayment\Http\Controllers\WebhookController;

Route::middleware(config('subbase-payment.webhook.middleware', []))
    ->post(config('subbase-payment.webhook.path', 'subbase-payment/webhook'), WebhookController::class)
    ->name('subbase-payment.webhook');