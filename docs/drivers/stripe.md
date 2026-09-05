# Stripe Driver (Checkout Sessions)

This guide covers integrating Stripe as a payment gateway for Subbase Payment using **Stripe Checkout Sessions** (hosted payment page).

## Requirements

- A Stripe account (Test or Live mode)
- Stripe **Secret API key** (`sk_test_...` / `sk_live_...`)
- A **Webhook signing secret** (`whsec_...`)
- A publicly reachable HTTPS endpoint for webhooks

## Installation

```bash
php artisan subbase-payment:install --driver=stripe
php artisan migrate
```

The install command writes the following to your `.env`:

```env
SUBBASE_PAYMENT_DRIVER=stripe
STRIPE_SECRET_KEY=sk_test_your-secret-key
STRIPE_WEBHOOK_SECRET=whsec_your-webhook-secret
```

Replace the placeholder values with your actual Stripe credentials.

## Getting Credentials

1. Log in to the [Stripe Dashboard](https://dashboard.stripe.com).
2. Go to **Developers → API keys**.
3. Copy the **Secret key** into `STRIPE_SECRET_KEY`. Use `sk_test_...` for sandbox, `sk_live_...` for production.
4. Go to **Developers → Webhooks** and create a webhook endpoint (see below).
5. Copy the **Signing secret** (`whsec_...`) into `STRIPE_WEBHOOK_SECRET`.

> **Security:** Never expose your secret key or webhook signing secret in client-side code. Only the publishable key (`pk_...`) is safe to expose. This package only uses the server-side secret key.

## Environment Variables

| Variable | Description | Default |
|---|---|---|
| `SUBBASE_PAYMENT_DRIVER` | Must be `stripe` | — |
| `STRIPE_SECRET_KEY` | Stripe secret API key (`sk_test_` / `sk_live_`) | — |
| `STRIPE_WEBHOOK_SECRET` | Webhook signing secret (`whsec_...`) | — |

### Sandbox vs Production

| Environment | Secret key prefix |
|---|---|
| Sandbox (Test mode) | `sk_test_...` |
| Production (Live mode) | `sk_live_...` |

Test mode is the default. Switch to live keys only when you are ready to accept real payments.

## Webhook Setup

1. In the Stripe Dashboard, go to **Developers → Webhooks → Add endpoint**.
2. Set the endpoint URL to:

   ```text
   https://your-app.example/subbase-payment/webhook
   ```

3. Subscribe to these events:
   - `checkout.session.completed`
   - `checkout.session.expired`
   - `charge.refunded`

4. Copy the generated **Signing secret** (`whsec_...`) into `STRIPE_WEBHOOK_SECRET`.

### Webhook Security

- The package verifies every webhook by recomputing the HMAC-SHA256 signature from the raw payload, timestamp, and signing secret, then comparing with the `Stripe-Signature` header.
- Signatures older than 5 minutes are rejected to prevent replay attacks.
- Browser return URLs are **never** treated as proof of payment.
- The webhook endpoint must be publicly reachable over HTTPS.
- Do **not** put the webhook route behind CSRF protection (the `web` middleware group). The default webhook middleware is empty.

### Event Mapping

| Stripe Event | Payment Status |
|---|---|
| `checkout.session.completed` | `paid` |
| `checkout.session.expired` | `failed` |
| `charge.refunded` | `canceled` |
| Other events | `pending` |

Duplicate event IDs are ignored (idempotent processing).

## Checkout Flow

1. Customer visits `/checkout/{plan-slug}`.
2. Customer submits name/email.
3. The package creates a Stripe Checkout Session and redirects the customer to the hosted payment page (`url`).
4. Stripe sends a verified webhook notification to `/subbase-payment/webhook`.
5. On `checkout.session.completed`, the payment is marked `paid` and the `PaymentReceived` event fires, activating the subscription.

## Notes

- Stripe Checkout Sessions is used in **redirect mode** (hosted payment page). The session ID is stored as the gateway transaction ID.
- Amounts are converted to minor units (cents) automatically. Zero-decimal currencies (e.g. JPY, VND) are handled correctly.
- No Stripe SDK is required — the driver uses Laravel's HTTP client.

## Troubleshooting

| Problem | Solution |
|---|---|
| `Stripe secret key is not configured` | Set `STRIPE_SECRET_KEY` in `.env` |
| `Stripe webhook signing secret is not configured` | Set `STRIPE_WEBHOOK_SECRET` in `.env` |
| `Stripe did not return a Checkout Session URL` | Verify secret key and that the plan has a valid price/currency |
| `Stripe webhook signature verification failed` | Confirm `STRIPE_WEBHOOK_SECRET` matches the dashboard, endpoint is HTTPS |
| Webhook not received | Ensure endpoint is publicly reachable, not behind CSRF middleware |
