# Paddle Driver (Billing v2 API)

This guide covers integrating Paddle as a payment gateway for Subbase Payment using **Paddle Billing API v2**.

## Requirements

- A Paddle account (Sandbox or Live mode)
- Paddle **API Key** (`pdl_api_...`)
- A **Webhook Secret** (`pdl_ntf_...`)
- A publicly reachable HTTPS endpoint for webhooks

## Installation

```bash
php artisan subbase-payment:install --driver=paddle
php artisan migrate
```

The install command writes the following to your `.env`:

```env
SUBBASE_PAYMENT_DRIVER=paddle
PADDLE_API_KEY=pdl_api_your-api-key
PADDLE_WEBHOOK_SECRET=pdl_ntf_set_your-webhook-secret
PADDLE_ENVIRONMENT=sandbox
```

Replace the placeholder values with your actual Paddle credentials.

## Getting Credentials

1. Log in to your [Paddle Dashboard](https://vendors.paddle.com) (or [Sandbox Dashboard](https://sandbox-vendors.paddle.com)).
2. Go to **Developer Tools → API Keys** and generate an API key (`pdl_api_...`).
3. Set `PADDLE_API_KEY` in `.env`.
4. Go to **Developer Tools → Notifications** and create a Webhook Destination (see below).
5. Copy the **Secret key** (`pdl_ntf_...`) into `PADDLE_WEBHOOK_SECRET`.

> **IMPORTANT (Paddle Dashboard Configuration):**
> The `transaction_default_checkout_url_not_set` error occurs if your Sandbox/Production Paddle account does not have a **Default Payment Link** configured.
> Open Paddle Dashboard → **Checkout → Checkout Settings** (or Checkout Link) → Fill in **Default Payment Link / Default Checkout URL** (e.g. `https://your-domain.com/checkout`).

## Environment Variables

| Variable | Description | Default |
|---|---|---|
| `SUBBASE_PAYMENT_DRIVER` | Must be `paddle` | — |
| `PADDLE_API_KEY` | Paddle API Key (`pdl_api_...`) | — |
| `PADDLE_WEBHOOK_SECRET` | Webhook Endpoint Secret (`pdl_ntf_...`) | — |
| `PADDLE_ENVIRONMENT` | `sandbox` or `production` | `sandbox` |

### Sandbox vs Production

| Environment | Base API URL |
|---|---|
| Sandbox | `https://sandbox-api.paddle.com` |
| Production | `https://api.paddle.com` |

## Webhook Setup

1. In the Paddle Dashboard, go to **Developer Tools → Notifications → Add Destination**.
2. Set the destination URL to:

   ```text
   https://your-app.example/subbase-payment/webhook
   ```

3. Select notification events:
   - `transaction.completed`
   - `transaction.paid`
   - `transaction.canceled`
   - `transaction.payment_failed`

4. Copy the generated secret into `PADDLE_WEBHOOK_SECRET`.

### Webhook Security

- The package verifies every webhook using HMAC-SHA256 signature verification matching `Paddle-Signature` (`ts=...;h1=...`).
- Browser return URLs are **never** treated as proof of payment.

### Event Mapping

| Paddle Event | Payment Status |
|---|---|
| `transaction.completed`, `transaction.paid` | `paid` |
| `transaction.canceled` | `canceled` |
| `transaction.payment_failed` | `failed` |
| Other events | `pending` |

## Checkout Flow

1. Customer visits `/checkout/{plan-slug}`.
2. Customer submits name/email.
3. The package creates a Paddle Transaction and redirects the customer to Paddle hosted checkout.
4. Paddle sends a verified webhook notification to `/subbase-payment/webhook`.
5. On `transaction.completed`, the payment is marked `paid` and the `PaymentReceived` event fires.

## Troubleshooting & Common Issues

### 1. `transaction_default_checkout_url_not_set` (400 Bad Request)
- **Cause:** Paddle requires a Default Payment Link configured in your dashboard when no custom URL is passed.
- **Fix:** Go to **Paddle Dashboard → Checkout → Checkout Settings** and set **Default Payment Link** (e.g., `https://your-domain.com/checkout`).

### 2. `transaction_checkout_url_domain_is_not_approved` (400 Bad Request)
- **Cause:** `checkout.url` points to an unapproved domain or local/tunnel host (`127.0.0.1`, `localhost`, `ngrok`).
- **Fix:** Add your public domain in **Paddle Dashboard → Developer Tools → Approved Domains**. Local and ngrok URLs are automatically omitted by the gateway so Paddle falls back to your Default Payment Link.

### 3. Webhook Status `failed` with "Payment transaction was not found."
- **Cause:** Non-transaction webhook events (`product.created`, `price.created`) or test webhooks from Event Simulator carrying dummy IDs (`txn_01hv8wpt...`).
- **Fix:** When using Paddle Event Simulator, replace the default dummy `data.id` in the JSON payload with a real `gateway_transaction_id` from your `subscription_payments` database table.

### 4. `Invalid webhook signature.` (401 Unauthorized)
- **Cause:** `PADDLE_WEBHOOK_SECRET` does not match the secret key generated for your webhook destination.
- **Fix:** Copy the Secret Key (`pdl_ntf_...`) from **Developer Tools → Notifications → Destination Details** into your `.env` as `PADDLE_WEBHOOK_SECRET`.
