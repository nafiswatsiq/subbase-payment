<?php

declare(strict_types=1);

namespace Nafiswatsiq\SubbasePayment\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'verified_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function getTable(): string
    {
        return config('subbase-payment.tables.subscription_payments', 'subscription_payments');
    }
}
