<?php

use Illuminate\Support\Facades\Route;
use Nafiswatsiq\SubbasePayment\Http\Controllers\CheckoutController;

Route::middleware(config('subbase-payment.checkout.middleware', ['web']))
    ->prefix(config('subbase-payment.checkout.path', 'checkout'))
    ->group(function (): void {
        Route::get('/', function (\Illuminate\Http\Request $request) {
            $ptxn = $request->string('_ptxn')->toString();
            $status = 'pending';

            if ($ptxn) {
                $table = config('subbase-payment.tables.subscription_payments', 'subscription_payments');
                $payment = \Illuminate\Support\Facades\DB::table($table)
                    ->where('gateway_transaction_id', $ptxn)
                    ->first();

                if ($payment) {
                    $status = in_array($payment->payment_status, ['completed', 'paid']) ? 'success' : $payment->payment_status;
                }
            }

            $returnUrl = config('subbase-payment.checkout.return_url');
            if ($returnUrl) {
                return \Illuminate\Support\Str::startsWith($returnUrl, 'http')
                    ? redirect()->away($returnUrl)
                    : redirect()->route($returnUrl);
            }
            return view('subbase-payment::status', ['plan' => null, 'status' => $status]);
        })->name('subbase-payment.checkout.root');

        Route::get('/{plan}', [CheckoutController::class, 'show'])
            ->name('subbase-payment.checkout');

        Route::post('/{plan}', [CheckoutController::class, 'store'])
            ->name('subbase-payment.checkout.store');

        Route::get('/{plan}/return', [CheckoutController::class, 'returned'])
            ->name('subbase-payment.checkout.return');

        Route::get('/{plan}/cancel', [CheckoutController::class, 'canceled'])
            ->name('subbase-payment.checkout.cancel');
    });