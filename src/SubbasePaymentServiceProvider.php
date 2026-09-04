<?php

namespace Nafiswatsiq\SubbasePayment;

use Nafiswatsiq\SubbasePayment\Console\InstallPaymentCommand;
use Nafiswatsiq\SubbasePayment\Gateways\PaypalGateway;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SubbasePaymentServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('subbase-payment')
            ->hasConfigFile('subbase-payment')
            ->hasViews()
            ->hasRoutes(['web', 'webhook'])
            ->hasCommands([InstallPaymentCommand::class])
            ->runsMigrations()
            ->hasMigrations([
                'create_subscription_payments_table',
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(PaymentManager::class, function (): PaymentManager {
            $manager = new PaymentManager();
            $manager->register('paypal', new PaypalGateway());

            return $manager;
        });
    }
}
