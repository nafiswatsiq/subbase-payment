<?php

namespace Nafiswatsiq\SubbasePayment;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SubbasePaymentServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('subbase-payment')
            ->hasConfigFile('subbase-payment')
            ->hasMigrations([
                'add_payment_fields_to_subscriptions_table',
            ]);
    }
}
