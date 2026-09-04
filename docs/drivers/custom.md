# Adding a Custom Gateway

This guide explains how to add your own payment gateway driver to Subbase Payment.

## Overview

Every gateway implements `PaymentGatewayInterface`, returns `PaymentResult` DTOs, and is registered on `PaymentManager`. The checkout controller and webhook controller work with any registered gateway — no changes needed.

## Step 1: Implement the Interface

```php
<?php

namespace App\Gateways;

use Nafiswatsiq\SubbasePayment\Contracts\PaymentGatewayInterface;
use Nafiswatsiq\SubbasePayment\Data\PaymentRequest;
use Nafiswatsiq\SubbasePayment\Data\PaymentResult;

class StripeGateway implements PaymentGatewayInterface
{
    public function charge(PaymentRequest $request): PaymentResult
    {
        // Create a payment session with your provider.
        // Return a PaymentResult with status 'pending' and the redirect URL.

        return new PaymentResult(
            status: 'pending',
            transactionId: $sessionId,
            redirectUrl: $checkoutUrl,
            metadata: ['provider' => 'stripe'],
        );
    }

    public function cancel(string $transactionId): PaymentResult
    {
        // Cancel or void the transaction if the provider supports it.

        return new PaymentResult(
            status: 'canceled',
            transactionId: $transactionId,
        );
    }

    public function handleWebhook(array $payload, array $headers = []): PaymentResult
    {
        // 1. Verify the webhook signature.
        // 2. Map the provider event to a status: 'paid', 'failed', 'canceled', or 'pending'.
        // 3. Return a PaymentResult.

        return new PaymentResult(
            status: $mappedStatus,
            transactionId: $transactionId,
            metadata: ['event_id' => $eventId, 'payload' => $payload],
        );
    }
}
```

### PaymentResult Statuses

| Status | Meaning |
|---|---|
| `pending` | Payment initiated, awaiting confirmation |
| `paid` | Payment confirmed and verified |
| `failed` | Payment denied or declined |
| `canceled` | Payment reversed, refunded, or voided |

## Step 2: Add Configuration

In `config/subbase-payment.php`, add your gateway under `gateways`:

```php
'gateways' => [
    'paypal' => [...],

    'stripe' => [
        'secret_key' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],
],
```

## Step 3: Add Env Stubs to the Install Command

In `InstallPaymentCommand.php`, add your driver to `$driverEnvStubs` and `$availableDrivers`:

```php
protected array $driverEnvStubs = [
    'paypal' => [...],
    'stripe' => [
        'STRIPE_SECRET' => 'your-stripe-secret',
        'STRIPE_WEBHOOK_SECRET' => 'your-webhook-secret',
    ],
];

protected array $availableDrivers = ['paypal', 'stripe'];
```

Now `php artisan subbase-payment:install --driver=stripe` writes all required env vars.

## Step 4: Register the Gateway

In your service provider's `boot()` method:

```php
use Nafiswatsiq\SubbasePayment\PaymentManager;

public function boot(): void
{
    $this->app->make(PaymentManager::class)->register('stripe', new StripeGateway());
}
```

## Step 5: Select the Driver

```env
SUBBASE_PAYMENT_DRIVER=stripe
```

Or via the install command:

```bash
php artisan subbase-payment:install --driver=stripe
```

## Webhook Security Rules

Your gateway **must**:

- Verify the webhook signature before changing payment state.
- Make webhook processing idempotent (ignore duplicate event IDs).
- Never treat browser return URLs as proof of payment.
- Never log credentials, secrets, or sensitive provider payloads.

## PaymentRequest Properties

| Property | Type | Description |
|---|---|---|
| `plan` | Plan model | The Subbase plan being purchased |
| `amount` | string | Formatted price amount |
| `currency` | string | ISO 4217 currency code |
| `customerName` | string | Customer's name |
| `customerEmail` | string | Customer's email |
| `returnUrl` | string | URL to redirect after payment |
| `cancelUrl` | string | URL to redirect on cancellation |
