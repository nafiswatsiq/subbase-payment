# Midtrans Driver (Snap)

This guide covers integrating Midtrans as a payment gateway for Subbase Payment using the **Snap** payment page.

## Requirements

- A Midtrans account (Sandbox or Production)
- Midtrans **Server Key**
- A publicly reachable HTTPS endpoint for webhooks

## Installation

```bash
php artisan subbase-payment:install --driver=midtrans
php artisan migrate
```

The install command writes the following to your `.env`:

```env
SUBBASE_PAYMENT_DRIVER=midtrans
MIDTRANS_SERVER_KEY=your-server-key
MIDTRANS_IS_PRODUCTION=false
```

Replace the placeholder values with your actual Midtrans credentials.

## Getting Credentials

1. Log in to the [Midtrans Dashboard](https://dashboard.sandbox.midtrans.com) (Sandbox) or [Production Dashboard](https://dashboard.midtrans.com).
2. Go to **Settings → Access Keys**.
3. Copy the **Server Key** into `MIDTRANS_SERVER_KEY`.

## Environment Variables

| Variable | Description | Default |
|---|---|---|
| `SUBBASE_PAYMENT_DRIVER` | Must be `midtrans` | — |
| `MIDTRANS_SERVER_KEY` | Midtrans server key | — |
| `MIDTRANS_IS_PRODUCTION` | `true` for production, `false` for sandbox | `false` |

### Sandbox vs Production

| Environment | `MIDTRANS_IS_PRODUCTION` |
|---|---|
| Sandbox | `false` |
| Production | `true` |

Sandbox is the default. Set `MIDTRANS_IS_PRODUCTION=true` only when you are ready to accept live payments.

## Webhook Setup

1. In the Midtrans Dashboard, go to **Settings → Configuration**.
2. Set the **Payment Notification URL** to:

   ```text
   https://your-app.example/subbase-payment/webhook
   ```

3. Save the configuration.

### Webhook Security

- The package verifies every webhook by recomputing the SHA-512 signature from `order_id`, `status_code`, `gross_amount`, and the server key, then comparing with `signature_key`.
- Browser return URLs are **never** treated as proof of payment.
- The webhook endpoint must be publicly reachable over HTTPS.
- Do **not** put the webhook route behind CSRF protection (the `web` middleware group). The default webhook middleware is empty.

### Event Mapping

| Midtrans `transaction_status` | Payment Status |
|---|---|
| `settlement` | `paid` |
| `capture` + `fraud_status=accept` | `paid` |
| `pending` | `pending` |
| `deny` | `failed` |
| `failure` | `failed` |
| `expire` | `failed` |
| `cancel` | `canceled` |
| `refund` | `canceled` |
| `partial_refund` | `canceled` |
| Other | `pending` |

Duplicate event IDs are ignored (idempotent processing).

## Checkout Flow

1. Customer visits `/checkout/{plan-slug}`.
2. Customer submits name/email.
3. The package creates a Midtrans Snap transaction and redirects the customer to the Snap payment page (`redirect_url`).
4. Midtrans sends a verified webhook notification to `/subbase-payment/webhook`.
5. On `settlement`, the payment is marked `paid` and the `PaymentReceived` event fires, activating the subscription.

## Notes

- Snap is used in **redirect mode** (hosted payment page). The Snap token is stored in the payment metadata for reference.
- No Midtrans SDK is required — the driver uses Laravel's HTTP client.
