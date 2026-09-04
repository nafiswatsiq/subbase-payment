<?php

namespace Nafiswatsiq\SubbasePayment;

use Nafiswatsiq\SubbasePayment\Contracts\PaymentGatewayInterface;
use Nafiswatsiq\SubbasePayment\Exceptions\PaymentConfigurationException;

class PaymentManager
{
    /** @var array<string, PaymentGatewayInterface> */
    private array $gateways = [];

    public function register(string $name, PaymentGatewayInterface $gateway): void
    {
        $this->gateways[$name] = $gateway;
    }

    public function driver(?string $name = null): PaymentGatewayInterface
    {
        $name ??= config('subbase-payment.driver');

        if (! $name) {
            throw new PaymentConfigurationException('No payment driver is configured.');
        }

        if (! isset($this->gateways[$name])) {
            throw new PaymentConfigurationException("Payment driver [{$name}] is not registered.");
        }

        return $this->gateways[$name];
    }
}