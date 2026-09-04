<?php

namespace Nafiswatsiq\SubbasePayment\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentReceived
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public object $paymentRecord,
        public array $metadata = []
    ) {}
}
