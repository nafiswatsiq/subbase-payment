<?php

namespace Nafiswatsiq\SubbasePayment;

use Nafiswatsiq\SubbasePayment\Console\InstallPaymentCommand;
use Nafiswatsiq\SubbasePayment\Console\ResetPaymentCommand;
use Nafiswatsiq\SubbasePayment\Gateways\MidtransGateway;
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
            ->hasTranslations()
            ->hasViews('subbase-payment')
            ->hasRoutes(['web', 'webhook'])
            ->hasCommands([
                InstallPaymentCommand::class,
                ResetPaymentCommand::class,
            ])
            ->runsMigrations()
            ->hasMigrations([
                'create_subscription_payments_table',
                'create_payment_webhook_logs_table',
            ]);
    }

    public function packageBooted(): void
    {
        $this->publishes([
            __DIR__.'/../resources/images' => public_path('vendor/subbase-payment/images'),
        ], 'subbase-payment-assets');
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(PaymentManager::class, function (): PaymentManager {
            $manager = new PaymentManager();
            $manager->register('paypal', new PaypalGateway());
            $manager->register('midtrans', new MidtransGateway());

            return $manager;
        });
    }
}
