<?php

declare(strict_types=1);

namespace Nafiswatsiq\SubbasePayment\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentWebhookLog extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'headers' => 'array',
        ];
    }

    public function getTable(): string
    {
        return config('subbase-payment.tables.payment_webhook_logs', 'payment_webhook_logs');
    }
}
