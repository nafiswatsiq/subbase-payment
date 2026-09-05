<?php

namespace Nafiswatsiq\SubbasePayment\Gateways;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Nafiswatsiq\SubbasePayment\Contracts\CapturesPayments;
use Nafiswatsiq\SubbasePayment\Contracts\PaymentGatewayInterface;
use Nafiswatsiq\SubbasePayment\Data\PaymentRequest;
use Nafiswatsiq\SubbasePayment\Data\PaymentResult;
use Nafiswatsiq\SubbasePayment\Exceptions\InvalidWebhookSignatureException;
use Nafiswatsiq\SubbasePayment\Exceptions\PaymentConfigurationException;
use RuntimeException;

class StripeGateway implements PaymentGatewayInterface, CapturesPayments
{
    public function charge(PaymentRequest $request): PaymentResult
    {
        $response = $this->api()->post('/v1/checkout/sessions', [
            'mode' => 'payment',
            'success_url' => $request->returnUrl,
            'cancel_url' => $request->cancelUrl,
            'customer_email' => $request->customerEmail,
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($request->currency),
                    'unit_amount' => $this->toMinorUnits($request->amount, $request->currency),
                    'product_data' => [
                        'name' => $request->plan->name,
                        'description' => $request->plan->description ?? null,
                    ],
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'plan_id' => (string) $request->plan->getKey(),
                'plan_slug' => $request->plan->slug,
                'customer_name' => $request->customerName,
            ],
        ])->throw();

        $payload = $response->json();

        if (empty($payload['id']) || empty($payload['url'])) {
            throw new RuntimeException('Stripe did not return a Checkout Session URL.');
        }

        return new PaymentResult('pending', $payload['id'], $payload['url'], [
            'provider' => 'stripe',
            'session' => $payload,
        ]);
    }

    public function cancel(string $transactionId): PaymentResult
    {
        $response = $this->api()->post('/v1/checkout/sessions/'.$transactionId.'/expire')->throw();

        return new PaymentResult('canceled', $transactionId, null, [
            'expire' => $response->json(),
        ]);
    }

    public function capture(string $transactionId): PaymentResult
    {
        $response = $this->api()
            ->get('/v1/checkout/sessions/'.$transactionId)
            ->throw();

        $payload = $response->json();

        return new PaymentResult(
            ($payload['payment_status'] ?? null) === 'paid' ? 'paid' : 'pending',
            $transactionId,
            null,
            ['session' => $payload],
        );
    }

    public function handleWebhook(array $payload, array $headers = []): PaymentResult
    {
        $this->verifyWebhook($payload, $headers);

        $eventType = (string) ($payload['type'] ?? '');
        $status = match ($eventType) {
            'checkout.session.completed' => 'paid',
            'checkout.session.expired' => 'failed',
            'charge.refunded' => 'canceled',
            default => 'pending',
        };

        $object = $payload['data']['object'] ?? [];
        $isCheckoutSession = ($object['object'] ?? '') === 'checkout.session';
        $transactionId = $isCheckoutSession ? ($object['id'] ?? null) : null;

        return new PaymentResult(
            $status,
            $transactionId,
            null,
            [
                'event_id' => $payload['id'] ?? null,
                'event_type' => $eventType,
                'payload' => $payload,
            ],
        );
    }

    public function name(): string
    {
        return 'Stripe';
    }

    public function logo(): ?string
    {
        return 'https://stripe.com/img/v3/home/twitter.png';
    }

    protected function api(): PendingRequest
    {
        $secretKey = config('subbase-payment.gateways.stripe.secret_key');

        if (! $secretKey) {
            throw new PaymentConfigurationException('Stripe secret key is not configured.');
        }

        return Http::baseUrl('https://api.stripe.com')
            ->withToken($secretKey)
            ->asForm()
            ->acceptJson();
    }

    private function verifyWebhook(array $payload, array $headers): void
    {
        $signingSecret = config('subbase-payment.gateways.stripe.webhook_secret');

        if (! $signingSecret) {
            throw new PaymentConfigurationException('Stripe webhook signing secret is not configured.');
        }

        $signatureHeader = $headers['stripe-signature'] ?? '';
        $rawPayload = $payload['_raw_body'] ?? json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if (! $signatureHeader || ! $rawPayload) {
            throw new InvalidWebhookSignatureException('Stripe webhook signature verification failed.');
        }

        $parts = [];
        foreach (explode(',', $signatureHeader) as $part) {
            [$key, $value] = array_pad(explode('=', $part, 2), 2, '');
            $parts[$key] = $value;
        }

        $timestamp = $parts['t'] ?? null;
        $signature = $parts['v1'] ?? null;

        if (! $timestamp || ! $signature) {
            throw new InvalidWebhookSignatureException('Stripe webhook signature verification failed.');
        }

        // Reject signatures older than 5 minutes to prevent replay attacks.
        if (abs(time() - (int) $timestamp) > 300) {
            throw new InvalidWebhookSignatureException('Stripe webhook timestamp is too old.');
        }

        $expected = hash_hmac('sha256', $timestamp.'.'.$rawPayload, $signingSecret);

        if (! hash_equals($expected, $signature)) {
            throw new InvalidWebhookSignatureException('Stripe webhook signature verification failed.');
        }
    }

    private function toMinorUnits(string $amount, string $currency): int
    {
        // Zero-decimal currencies are expressed as whole units in Stripe.
        $zeroDecimal = ['bif', 'clp', 'djf', 'gnf', 'jpy', 'kmf', 'krw', 'mga', 'pyg', 'rwf', 'ugx', 'vnd', 'vuv', 'xaf', 'xof', 'xpf'];

        if (in_array(strtolower($currency), $zeroDecimal, true)) {
            return (int) round((float) $amount);
        }

        return (int) round((float) $amount * 100);
    }
}
