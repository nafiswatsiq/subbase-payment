<?php

namespace Nafiswatsiq\SubbasePayment\Data;

use Illuminate\Database\Eloquent\Model;

final readonly class PaymentRequest
{
    public function __construct(
        public Model $plan,
        public string $amount,
        public string $currency,
        public string $customerName,
        public string $customerEmail,
        public string $returnUrl,
        public string $cancelUrl,
    ) {}
}