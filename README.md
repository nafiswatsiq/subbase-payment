# Subbase Payment

Payment gateway integrations for [`nafiswatsiq/subbase`](https://github.com/nafiswatsiq/subbase).

## Status

This package currently contains the package foundation only. Gateway drivers, payment contracts, webhooks, and lifecycle handling will be added in separate releases.

## Installation

```bash
composer require nafiswatsiq/subbase-payment
php artisan vendor:publish --tag=subbase-payment-config
php artisan migrate
```

The service provider is registered automatically by Laravel. To disable payment integrations, remove the package or leave `SUBBASE_PAYMENT_DRIVER` unset. Existing subscription data is not removed.

## Configuration

The published `config/subbase-payment.php` file provides:

- `driver`: the selected payment driver, unset by default;
- `webhook.path`: the future webhook endpoint path;
- `gateways`: gateway-specific configuration.

Payment SDKs will be optional dependencies and will be documented with the driver that requires them.

## Development

```bash
composer install
composer validate --strict
composer test
```

This package intentionally remains separate from `subbase`; the core package does not depend on payment SDKs or this package.
