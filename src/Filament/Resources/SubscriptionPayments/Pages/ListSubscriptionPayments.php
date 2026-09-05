<?php

declare(strict_types=1);

namespace Nafiswatsiq\SubbasePayment\Filament\Resources\SubscriptionPayments\Pages;

use Nafiswatsiq\SubbasePayment\Filament\Resources\SubscriptionPayments\SubscriptionPaymentResource;
use Filament\Resources\Pages\ListRecords;

class ListSubscriptionPayments extends ListRecords
{
    protected static string $resource = SubscriptionPaymentResource::class;
}
