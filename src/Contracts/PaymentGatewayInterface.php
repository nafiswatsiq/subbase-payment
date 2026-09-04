<?php

namespace Nafiswatsiq\SubbasePayment\Contracts;

use Nafiswatsiq\SubbasePayment\Data\PaymentRequest;
use Nafiswatsiq\SubbasePayment\Data\PaymentResult;

interface PaymentGatewayInterface
{
    public function charge(PaymentRequest $request): PaymentResult;

    public function cancel(string $transactionId): PaymentResult;

    public function handleWebhook(array $payload, array $headers = []): PaymentResult;

    public function name(): string;

    public function logo(): ?string;
}