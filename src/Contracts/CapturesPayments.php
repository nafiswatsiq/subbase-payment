<?php

namespace Nafiswatsiq\SubbasePayment\Contracts;

use Nafiswatsiq\SubbasePayment\Data\PaymentResult;

interface CapturesPayments
{
    public function capture(string $transactionId): PaymentResult;
}
