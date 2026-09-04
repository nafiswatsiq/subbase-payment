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

Use the component without a `subscribe-route` prop:

```blade
<x-subbase::plan-list />
```

The registered route name is `subbase-payment.checkout`. If you need a custom
checkout flow, define a named route and pass that registered name to the
component. Do not use an example name such as `your.custom.checkout.route`
unless you have defined that route in your application.

The page reads the configured Subbase plan model, displays its features and
locale-aware price, and collects the customer's name and email before payment.
Set `subbase-payment.checkout.path` or `subbase-payment.checkout.middleware`
to customize the route.

## Payment Drivers

Payment gateway configuration is handled per driver. Each driver has its own documentation guide with installation steps, environment variables, and webhook setup:

- **[PayPal Driver Guide](docs/drivers/paypal.md)** — PayPal REST API integration.
- **[Custom Gateway Guide](docs/drivers/custom.md)** — Implement your own payment gateway driver.

### Quick Setup

```bash
# Install with PayPal driver
php artisan subbase-payment:install --driver=paypal
```

The command writes `SUBBASE_PAYMENT_DRIVER` and all required driver environment variables to `.env`:

```env
SUBBASE_PAYMENT_DRIVER=paypal
PAYPAL_CLIENT_ID=your-client-id
PAYPAL_SECRET=your-secret
PAYPAL_BASE_URL=https://api-m.sandbox.paypal.com
PAYPAL_WEBHOOK_ID=your-webhook-id
```

For non-interactive or CI environments:

```bash
php artisan subbase-payment:install --driver=paypal --no-interaction
```

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
