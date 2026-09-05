# Xendit Gateway Driver

Integrates [Xendit Invoices API](https://developers.xendit.co/api-reference/#invoices) for `subbase-payment`.

---

## Configuration

Add the following environment variables to `.env`:

```env
SUBBASE_PAYMENT_DRIVER=xendit
XENDIT_SECRET_KEY=xnd_development_...
XENDIT_WEBHOOK_VERIFICATION_TOKEN=your-xendit-webhook-token
```

Or configure in `config/subbase-payment.php`:

```php
'gateways' => [
    'xendit' => [
        'secret_key' => env('XENDIT_SECRET_KEY'),
        'webhook_verification_token' => env('XENDIT_WEBHOOK_VERIFICATION_TOKEN'),
    ],
],
```

---

## Installation via CLI

Run the installer command:

```bash
php artisan subbase-payment:install --driver=xendit
```

This updates `SUBBASE_PAYMENT_DRIVER=xendit` and sets placeholders for `XENDIT_SECRET_KEY` and `XENDIT_WEBHOOK_VERIFICATION_TOKEN`.

---

## Obtaining Credentials

### 1. Xendit Secret Key (`XENDIT_SECRET_KEY`)
1. Log into your [Xendit Dashboard](https://dashboard.xendit.co/).
2. Go to **Settings** > **Developers** > **API Keys** (or **API Keys & Webhooks**).
3. Under **Secret Keys**, click **Generate secret key**.
4. Give the key a name (e.g. `Subbase Payment`) and set permissions (minimum: **Invoices: Write**).
5. Copy the generated key (starts with `xnd_development_...` or `xnd_production_...`) and add it to `.env` as `XENDIT_SECRET_KEY`.

---

## Webhook Setup & Token (`XENDIT_WEBHOOK_VERIFICATION_TOKEN`)

1. Log into your [Xendit Dashboard](https://dashboard.xendit.co/).
2. Go to **Settings** > **Developers** > **Webhooks** (or **Callbacks**).
3. Locate **Verification Token** section (or Webhook Verification Token).
4. Copy the verification token string and save it in `.env` as `XENDIT_WEBHOOK_VERIFICATION_TOKEN`.
5. Under **Invoice Paid** and **Invoice Expired** callback settings:
   - Set URL to: `https://your-domain.com/subbase-payment/webhook`
   - Test callback using the **Test and Save** button.
