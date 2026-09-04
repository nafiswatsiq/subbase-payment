<?php

namespace Nafiswatsiq\SubbasePayment\Data;

final readonly class PaymentResult
{
    public function __construct(
        public string $status,
        public ?string $transactionId = null,
        public ?string $approvalUrl = null,
        public array $data = [],
    ) {
    }
}