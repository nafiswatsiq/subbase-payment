<?php

use Illuminate\Support\Facades\Route;
use Nafiswatsiq\SubbasePayment\Http\Controllers\CheckoutController;

Route::middleware(config('subbase-payment.checkout.middleware', ['web']))
    ->prefix(config('subbase-payment.checkout.path', 'checkout'))
    ->group(function (): void {
        Route::get('/{plan}', [CheckoutController::class, 'show'])
            ->name('subbase-payment.checkout');

        Route::post('/{plan}', [CheckoutController::class, 'store'])
            ->name('subbase-payment.checkout.store');

        Route::get('/{plan}/return', [CheckoutController::class, 'returned'])
            ->name('subbase-payment.checkout.return');

        Route::get('/{plan}/cancel', [CheckoutController::class, 'canceled'])
            ->name('subbase-payment.checkout.cancel');
    });