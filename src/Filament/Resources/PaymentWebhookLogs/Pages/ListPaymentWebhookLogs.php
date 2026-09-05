<?php

declare(strict_types=1);

namespace Nafiswatsiq\SubbasePayment\Filament\Resources\PaymentWebhookLogs\Pages;

use Nafiswatsiq\SubbasePayment\Filament\Resources\PaymentWebhookLogs\PaymentWebhookLogResource;
use Filament\Resources\Pages\ListRecords;

class ListPaymentWebhookLogs extends ListRecords
{
    protected static string $resource = PaymentWebhookLogResource::class;
}
