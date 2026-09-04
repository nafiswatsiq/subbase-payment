# Subbase Payment

Payment gateway integrations for [`nafiswatsiq/subbase`](https://github.com/nafiswatsiq/subbase).

## Status

The package includes a public checkout flow, a PayPal REST driver, and a
gateway-agnostic webhook contract. Other gateways can be added without
changing the checkout controller or webhook controller.

## Installation

```bash
composer require nafiswatsiq/subbase-payment
php artisan vendor:publish --tag=subbase-payment-config
php artisan subbase-payment:install --driver=paypal
php artisan migrate
```

The install command writes `SUBBASE_PAYMENT_DRIVER=paypal` to `.env`. For CI
or other non-interactive environments, use:

```bash
php artisan subbase-payment:install --driver=paypal --no-interaction
```

The package creates an independent `subscription_payments` table. It stores the
Subbase subscription identifier in `subscription_id` as a string without a
database foreign key or changes to the Subbase subscriptions table. The table
name can be changed with `subbase-payment.tables.subscription_payments`.

The service provider is registered automatically by Laravel. To disable payment integrations, remove the package or leave `SUBBASE_PAYMENT_DRIVER` unset. Existing subscription data is not removed.

## Public checkout

When this package is installed, the Subbase `plan-list` component automatically
links each plan to the public checkout page:

```text
/checkout/{plan-slug}
```

The page reads the configured Subbase plan model, displays its features and
locale-aware price, and collects the customer's name and email before payment.
Set `subbase-payment.checkout.path` or `subbase-payment.checkout.middleware`
to customize the route.

## PayPal

Choose the driver and configure PayPal credentials. Sandbox is the default;
use `https://api-m.paypal.com` for production.

```env
SUBBASE_PAYMENT_DRIVER=paypal
PAYPAL_CLIENT_ID=your-client-id
PAYPAL_SECRET=your-secret
PAYPAL_BASE_URL=https://api-m.sandbox.paypal.com
PAYPAL_WEBHOOK_ID=your-webhook-id
```

Register the webhook URL in PayPal as:

```text
https://your-app.example/subbase-payment/webhook
```
For PayPal, configure `PAYPAL_WEBHOOK_ID` and register
`https://your-app.example/subbase-payment/webhook` in the PayPal dashboard.
The webhook verifies the signature through PayPal before changing a payment to
`paid`; browser redirects never activate a subscription.

PayPal signatures are verified through PayPal's verification API. The webhook
maps provider events to `pending`, `paid`, `failed`, or `canceled`, updates the
configured payment table by transaction ID, and ignores duplicate event IDs.
The customer return URL is not treated as proof of payment. The current release
does not yet create or activate a Subbase subscription from a successful
payment; application code must perform that lifecycle step after verification.

The webhook must be publicly reachable over HTTPS. Do not put it behind the
`web` middleware group if that group enables CSRF protection.

## Adding gateways

Implement `PaymentGatewayInterface`, return the package `PaymentResult` DTO,
and register the gateway in a service provider:

```php
$this->app->make(PaymentManager::class)->register('custom', new CustomGateway());
```
Additional gateways implement `PaymentGatewayInterface` and map their provider
events to the same `PaymentResult` statuses. Register them on `PaymentManager`
from the gateway package's service provider, then select the driver with
`SUBBASE_PAYMENT_DRIVER`.

The gateway is responsible for authenticating and verifying its own webhook
signature. Set `SUBBASE_PAYMENT_DRIVER=custom` after registration.

## Configuration

The published `config/subbase-payment.php` file provides:

- `driver`: the selected payment driver, unset by default;
- `checkout.path`: public checkout prefix, `checkout` by default;
- `checkout.middleware`: middleware applied to checkout, `web` by default;
- `webhook.path`: webhook endpoint path, `subbase-payment/webhook` by default;
- `gateways`: gateway-specific configuration.

The package does not require a PayPal SDK; it uses Laravel's HTTP client.

## Development

```bash
composer install
composer validate --strict
composer test
```

This package intentionally remains separate from `subbase`; the core package does not depend on payment SDKs or this package.
