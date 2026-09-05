<?php

declare(strict_types=1);

namespace Nafiswatsiq\SubbasePayment\Filament\Resources\PaymentWebhookLogs\Pages;

use Nafiswatsiq\SubbasePayment\Filament\Resources\PaymentWebhookLogs\PaymentWebhookLogResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPaymentWebhookLog extends ViewRecord
{
    protected static string $resource = PaymentWebhookLogResource::class;
}
