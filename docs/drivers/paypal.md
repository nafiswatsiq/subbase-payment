# PayPal Driver

This guide covers integrating PayPal as a payment gateway for Subbase Payment.

## Requirements

- A PayPal Business account
- PayPal REST API credentials (Client ID and Secret)
- A publicly reachable HTTPS endpoint for webhooks

## Installation

```bash
php artisan subbase-payment:install --driver=paypal
php artisan migrate
```

The install command writes the following to your `.env`:

```env
SUBBASE_PAYMENT_DRIVER=paypal
PAYPAL_CLIENT_ID=your-client-id
PAYPAL_SECRET=your-secret
PAYPAL_BASE_URL=https://api-m.sandbox.paypal.com
PAYPAL_WEBHOOK_ID=your-webhook-id
```

Replace the placeholder values with your actual PayPal credentials.

## Getting Credentials

1. Go to the [PayPal Developer Dashboard](https://developer.paypal.com/dashboard/applications/sandbox).
2. Create or select an app under **Apps & Credentials**.
3. Copy the **Client ID** and **Secret** into your `.env`.

## Environment Variables

| Variable | Description | Default |
|---|---|---|
| `SUBBASE_PAYMENT_DRIVER` | Must be `paypal` | — |
| `PAYPAL_CLIENT_ID` | REST API client ID | — |
| `PAYPAL_SECRET` | REST API secret | — |
| `PAYPAL_BASE_URL` | API base URL | `https://api-m.sandbox.paypal.com` |
| `PAYPAL_WEBHOOK_ID` | Webhook ID from PayPal dashboard | — |

### Sandbox vs Production

| Environment | Base URL |
|---|---|
| Sandbox | `https://api-m.sandbox.paypal.com` |
| Production | `https://api-m.paypal.com` |

Set `PAYPAL_BASE_URL` accordingly. Sandbox is the default.

## Webhook Setup

1. In the PayPal Developer Dashboard, go to **Webhooks**.
2. Add a webhook URL:

   ```text
   https://your-app.example/subbase-payment/webhook
   ```

3. Subscribe to these events:
   - `PAYMENT.CAPTURE.COMPLETED`
   - `CHECKOUT.ORDER.COMPLETED`
    - `CHECKOUT.PAYMENT-RESOURCE.PAYMENT-COMPLETED`
   - `PAYMENT.CAPTURE.DENIED`
   - `PAYMENT.CAPTURE.DECLINED`
   - `PAYMENT.CAPTURE.REVERSED`
   - `PAYMENT.CAPTURE.REFUNDED`

4. Copy the generated **Webhook ID** into `PAYPAL_WEBHOOK_ID`.

### Webhook Security

- The package verifies every webhook signature through PayPal's verification API before changing payment status.
- Browser return URLs are **never** treated as proof of payment.
- The webhook endpoint must be publicly reachable over HTTPS.
- Do **not** put the webhook route behind CSRF protection (the `web` middleware group). The default webhook middleware is empty.

### Event Mapping

| PayPal Event | Payment Status |
|---|---|
| `PAYMENT.CAPTURE.COMPLETED` | `paid` |
| `CHECKOUT.ORDER.COMPLETED` | `paid` |
| `CHECKOUT.PAYMENT-RESOURCE.PAYMENT-COMPLETED` | `paid` |
| `CHECKOUT.ORDER.APPROVED` | `pending` |
| `PAYMENT.CAPTURE.DENIED` | `failed` |
| `PAYMENT.CAPTURE.DECLINED` | `failed` |
| `PAYMENT.CAPTURE.REVERSED` | `canceled` |
| `PAYMENT.CAPTURE.REFUNDED` | `canceled` |
| Other events | `pending` |

Duplicate event IDs are ignored (idempotent processing).

## Checkout Flow

1. Customer visits `/checkout/{plan-slug}`.
2. Customer fills in name and email, then clicks pay.
3. Package creates a PayPal order via the REST API and redirects to PayPal's approval page.
4. After approval and capture, PayPal sends a webhook to confirm payment.
5. The return URL captures the approved order server-side.
6. The webhook handler verifies the signature and updates the payment record to `paid` only after PayPal confirms completion.

`CHECKOUT.ORDER.APPROVED` is intentionally stored as `pending`. It is not a
payment confirmation, so the webhook endpoint does not call PayPal's capture
API and will acknowledge this event without returning a 500 error.

The payment is bound to the authenticated local user through a `user_id` in
the server-generated payment metadata. The PayPal payer email is not used to
find or create a local account.

> **Note:** The current release does not automatically create or activate a Subbase subscription after payment. Your application code must perform that lifecycle step after verified payment.

## Event & Subscription Activation

When a payment is verified, the package dispatches a `Nafiswatsiq\SubbasePayment\Events\PaymentReceived` event containing `$paymentRecord` and `$metadata`.

Listen for this event in your application (e.g. `AppServiceProvider`) to create or activate the user's Subbase subscription:

```php
use Nafiswatsiq\SubbasePayment\Events\PaymentReceived;
use Nafiswatsiq\Subbase\Models\Plan;
use Illuminate\Support\Facades\Event;

Event::listen(PaymentReceived::class, function (PaymentReceived $event) {
    $payment = $event->paymentRecord;
    $planId = $event->metadata['plan_id'] ?? null;

    $user = \App\Models\User::where('email', $payment->customer_email)->first();
    $plan = Plan::find($planId);

    if ($user && $plan) {
        $user->newSubscription('default', $plan);
    }
});
```

## Customizing the Webhook Path

In `config/subbase-payment.php`:

```php
'webhook' => [
    'path' => 'subbase-payment/webhook', // change this
    'middleware' => [],
],
```

Update the webhook URL in PayPal's dashboard to match.

## Troubleshooting

| Problem | Solution |
|---|---|
| `PayPal client credentials are not configured` | Set `PAYPAL_CLIENT_ID` and `PAYPAL_SECRET` in `.env` |
| `PayPal webhook ID is not configured` | Set `PAYPAL_WEBHOOK_ID` in `.env` |
| `PayPal did not return an access token` | Verify credentials and `PAYPAL_BASE_URL` |
| `PayPal webhook signature verification failed` | Confirm `PAYPAL_WEBHOOK_ID` matches the dashboard, endpoint is HTTPS |
| Webhook not received | Ensure endpoint is publicly reachable, not behind CSRF middleware |
| `Some of your information isn't correct` on Sandbox login | Use a **Sandbox Personal (Buyer)** account from Developer Dashboard (Testing Tools > Sandbox Accounts), not real PayPal or developer account credentials |

## No SDK Required

This package uses Laravel's HTTP client (`Http::`) directly — no PayPal SDK dependency needed.
