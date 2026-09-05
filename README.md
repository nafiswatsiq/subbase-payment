# Subbase Payment

Payment gateway integrations for [`nafiswatsiq/subbase`](https://github.com/nafiswatsiq/subbase).

> **Quick links:** [Installation](#installation) · [Checkout](#public-checkout) · [Events](#events--subscription-lifecycle) · [PayPal Gateway](docs/drivers/paypal.md) · [Midtrans Gateway](docs/drivers/midtrans.md) · [Custom Gateway](docs/drivers/custom.md) · [Configuration](#configuration)

---

## Installation

```bash
composer require nafiswatsiq/subbase-payment
php artisan vendor:publish --tag=subbase-payment-config
php artisan subbase-payment:install --driver=paypal
php artisan migrate
```

**CI / non-interactive:**
```bash
php artisan subbase-payment:install --driver=paypal --no-interaction
```

The install command writes `SUBBASE_PAYMENT_DRIVER=paypal` + required env vars to `.env`.  
Service provider auto-registers. Disable by removing package or leaving `SUBBASE_PAYMENT_DRIVER` unset.

### Reset / Switch Driver

Reset driver configuration and remove driver env keys:
```bash
php artisan subbase-payment:reset
```

Reset and immediately configure/switch to another driver:
```bash
php artisan subbase-payment:reset --driver=paypal --force
```

---

## Public Checkout

Auto-links from Subbase `<x-subbase::plan-list />` component:

```
/checkout/{plan-slug}
```

Route name: `subbase-payment.checkout`  
Customize path/middleware/redirects in `config/subbase-payment.php`:
```php
'checkout' => [
    'path' => 'checkout',
    'middleware' => ['web'],
    'return_url' => null,  // named route or full URL after successful payment
    'cancel_url' => null,  // named route or full URL after canceled payment
],
```

Page shows plan features, locale-aware price, collects name/email before payment.

### Custom Redirect After Payment

Set `return_url` / `cancel_url` to override the default status page:

| Value | Behavior |
|-------|----------|
| `null` (default) | Show built-in status page (`status.blade.php`) |
| Named route (e.g. `dashboard`) | `redirect()->route('dashboard', $plan->slug)` |
| Full URL (e.g. `https://app.example/success`) | `redirect()->away('https://app.example/success')` |

**Example — redirect to dashboard after payment:**
```php
'checkout' => [
    'return_url' => 'dashboard',
    'cancel_url' => 'plans.index',
],
```

**Example — external URLs:**
```php
'checkout' => [
    'return_url' => 'https://app.example.com/payment/success',
    'cancel_url' => 'https://app.example.com/payment/cancel',
],
```

---

## Events & Subscription Lifecycle

Webhook verifies payment → dispatches `PaymentReceived` event.

**Handle in your app (e.g. `AppServiceProvider`):**
```php
use Nafiswatsiq\SubbasePayment\Events\PaymentReceived;
use Nafiswatsiq\Subbase\Models\Plan;
use Illuminate\Support\Facades\Event;

Event::listen(PaymentReceived::class, function (PaymentReceived $event) {
    $payment = $event->paymentRecord;
    $planId  = $event->metadata['plan_id'] ?? null;

    $user = \App\Models\User::where('email', $payment->customer_email)->first();
    $plan = Plan::find($planId);

    if ($user && $plan) {
        $user->newSubscription('default', $plan);
    }
});
```

---

## Payment Drivers

| Driver | Guide |
|--------|-------|
| <img src="https://www.paypalobjects.com/webstatic/icon/pp258.png" width="20" height="20" alt="PayPal" align="center"> **PayPal** | [docs/drivers/paypal.md](docs/drivers/paypal.md) — credentials, webhook setup, sandbox/production, troubleshooting |
| <img src="https://midtrans.com/assets/img/logo.svg" height="18" alt="Midtrans" align="center"> **Midtrans** | [docs/drivers/midtrans.md](docs/drivers/midtrans.md) — credentials, Snap setup, notification URL, sandbox/production |
| ⚙️ **Custom** | [docs/drivers/custom.md](docs/drivers/custom.md) — implement `PaymentGatewayInterface`, register on `PaymentManager` |

### Quick PayPal Setup

```bash
php artisan subbase-payment:install --driver=paypal
```

Writes to `.env`:
```env
SUBBASE_PAYMENT_DRIVER=paypal
PAYPAL_CLIENT_ID=your-client-id
PAYPAL_SECRET=your-secret
PAYPAL_BASE_URL=https://api-m.sandbox.paypal.com
PAYPAL_WEBHOOK_ID=your-webhook-id
```

### Quick Midtrans Setup

```bash
php artisan subbase-payment:install --driver=midtrans
```

Writes to `.env`:
```env
SUBBASE_PAYMENT_DRIVER=midtrans
MIDTRANS_MERCHANT_ID=your-merchant-id
MIDTRANS_CLIENT_KEY=your-client-key
MIDTRANS_SERVER_KEY=your-server-key
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

---

## Configuration

Published `config/subbase-payment.php`:

| Key | Default | Description |
|-----|---------|-------------|
| `driver` | `null` | Selected gateway (`paypal`, `stripe`, etc.) |
| `checkout.path` | `checkout` | Public checkout URL prefix |
| `checkout.middleware` | `['web']` | Middleware on checkout routes |
| `checkout.return_url` | `null` | Named route or full URL after successful payment |
| `checkout.cancel_url` | `null` | Named route or full URL after canceled payment |
| `webhook.path` | `subbase-payment/webhook` | Webhook endpoint path |
| `webhook.middleware` | `[]` | Middleware on webhook (keep empty for PayPal) |
| `gateways` | `[]` | Per-gateway config (see driver guides) |

---

## Publishing Assets & Views

Publish configuration file:
```bash
php artisan vendor:publish --tag=subbase-payment-config
```

Publish Blade views (`checkout.blade.php`, `status.blade.php` to `resources/views/vendor/subbase-payment`):
```bash
php artisan vendor:publish --tag=subbase-payment-views
```

---

## Development

```bash
composer install
composer validate --strict
composer test
```

---
