<?php

declare(strict_types=1);

namespace Nafiswatsiq\SubbasePayment;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Nafiswatsiq\SubbasePayment\Filament\Resources\PaymentWebhookLogs\PaymentWebhookLogResource;
use Nafiswatsiq\SubbasePayment\Filament\Resources\SubscriptionPayments\SubscriptionPaymentResource;

class SubbasePaymentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'subbase-payment';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            SubscriptionPaymentResource::class,
            PaymentWebhookLogResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }
}
