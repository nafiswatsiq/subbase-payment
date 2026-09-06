<?php

namespace Nafiswatsiq\SubbasePayment\Gateways;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Nafiswatsiq\SubbasePayment\Contracts\PaymentGatewayInterface;
use Nafiswatsiq\SubbasePayment\Data\PaymentRequest;
use Nafiswatsiq\SubbasePayment\Data\PaymentResult;
use Nafiswatsiq\SubbasePayment\Exceptions\InvalidWebhookSignatureException;
use Nafiswatsiq\SubbasePayment\Exceptions\PaymentConfigurationException;
use RuntimeException;

class PaddleGateway implements PaymentGatewayInterface
{
    public function charge(PaymentRequest $request): PaymentResult
    {
        $priceId = config('subbase-payment.gateways.paddle.price_id');

        if ($priceId) {
            $items = [
                [
                    'price_id' => $priceId,
                    'quantity' => 1,
                ],
            ];
        } else {
            $items = [
                [
                    'quantity' => 1,
                    'price' => array_filter([
                        'name' => $request->plan->name,
                        'description' => $request->plan->description ?? null,
                        'unit_price' => [
                            'amount' => (string) $this->toMinorUnits($request->amount, $request->currency),
                            'currency_code' => strtoupper($request->currency),
                        ],
                        'product' => [
                            'name' => $request->plan->name,
                            'tax_category' => 'standard',
                        ],
                    ]),
                ],
            ];
        }

        $checkoutUrl = $request->returnUrl;
        if (! empty($checkoutUrl)) {
            $parsedHost = parse_url($checkoutUrl, PHP_URL_HOST);
            if ($parsedHost === '127.0.0.1' || $parsedHost === 'localhost' || str_contains((string) $parsedHost, 'ngrok')) {
                $checkoutUrl = null;
            }
        }

        $payloadData = array_filter([
            'items' => $items,
            'customer_details' => array_filter([
                'email' => $request->customerEmail,
                'name' => $request->customerName,
            ]),
            'custom_data' => [
                'plan_id' => (string) $request->plan->getKey(),
                'plan_slug' => $request->plan->slug,
                'customer_name' => $request->customerName,
                'customer_email' => $request->customerEmail,
            ],
            'checkout' => array_filter([
                'url' => $checkoutUrl,
            ]),
        ]);

        $response = $this->api()->post('/transactions', $payloadData);

        if ($response->failed()) {
            throw new RuntimeException('Paddle API Error ['.$response->status().']: '.$response->body());
        }

        $payload = $response->json('data') ?? [];

        if (empty($payload['id'])) {
            throw new RuntimeException('Paddle did not return a transaction ID.');
        }

        $checkoutUrl = $payload['url'] ?? ($payload['checkout']['url'] ?? null);

        return new PaymentResult('pending', $payload['id'], $checkoutUrl, [
            'provider' => 'paddle',
            'transaction' => $payload,
        ]);
    }

    public function cancel(string $transactionId): PaymentResult
    {
        $response = $this->api()->post('/transactions/'.$transactionId.'/cancel')->throw();

        return new PaymentResult('canceled', $transactionId, null, [
            'cancel' => $response->json('data') ?? [],
        ]);
    }

    public function handleWebhook(array $payload, array $headers = []): PaymentResult
    {
        $this->verifyWebhook($payload, $headers);

        $eventType = (string) ($payload['event_type'] ?? '');
        $status = match ($eventType) {
            'transaction.completed', 'transaction.paid' => 'paid',
            'transaction.canceled' => 'canceled',
            'transaction.payment_failed' => 'failed',
            default => 'pending',
        };

        $data = $payload['data'] ?? [];
        $transactionId = str_starts_with((string) ($data['id'] ?? ''), 'txn_')
            ? $data['id']
            : ($data['transaction_id'] ?? null);

        return new PaymentResult(
            $status,
            $transactionId,
            null,
            [
                'event_id' => $payload['event_id'] ?? null,
                'event_type' => $eventType,
                'payload' => $payload,
            ],
        );
    }

    public function name(): string
    {
        return 'Paddle';
    }

    public function logo(): ?string
    {
        if (file_exists(public_path('vendor/subbase-payment/images/paddle.png'))) {
            return asset('vendor/subbase-payment/images/paddle.png');
        }

        return 'https://assets.paddle.com/paddle-logo.png';
    }

    protected function api(): PendingRequest
    {
        $apiKey = config('subbase-payment.gateways.paddle.api_key');
        $environment = config('subbase-payment.gateways.paddle.environment', 'sandbox');

        if (! $apiKey) {
            throw new PaymentConfigurationException('Paddle API key is not configured.');
        }

        $baseUrl = strtolower((string) $environment) === 'production'
            ? 'https://api.paddle.com'
            : 'https://sandbox-api.paddle.com';

        return Http::baseUrl($baseUrl)
            ->withToken($apiKey)
            ->acceptJson()
            ->asJson();
    }

    private function verifyWebhook(array $payload, array $headers = []): void
    {
        $secretKey = config('subbase-payment.gateways.paddle.webhook_secret');

        if (! $secretKey) {
            throw new PaymentConfigurationException('Paddle webhook secret is not configured.');
        }

        $signatureHeader = null;
        foreach ($headers as $key => $value) {
            if (strtolower($key) === 'paddle-signature') {
                $signatureHeader = is_array($value) ? ($value[0] ?? null) : $value;
                break;
            }
        }

        if (! $signatureHeader) {
            throw new InvalidWebhookSignatureException('Missing Paddle-Signature header.');
        }

        $ts = null;
        $h1 = null;
        foreach (explode(';', $signatureHeader) as $part) {
            $part = trim($part);
            if (str_starts_with($part, 'ts=')) {
                $ts = substr($part, 3);
            } elseif (str_starts_with($part, 'h1=')) {
                $h1 = substr($part, 3);
            }
        }

        if (! $ts || ! $h1) {
            throw new InvalidWebhookSignatureException('Malformed Paddle-Signature header.');
        }

        $rawBody = $payload['_raw_body'] ?? json_encode($payload);
        $signedPayload = $ts.':'.$rawBody;
        $expectedH1 = hash_hmac('sha256', $signedPayload, $secretKey);

        if (! hash_equals($expectedH1, $h1)) {
            throw new InvalidWebhookSignatureException('Paddle webhook signature mismatch.');
        }
    }

    private function toMinorUnits(float|int|string $amount, string $currency): int
    {
        $zeroDecimalCurrencies = ['BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF'];
        if (in_array(strtoupper($currency), $zeroDecimalCurrencies, true)) {
            return (int) round((float) $amount);
        }

        return (int) round((float) $amount * 100);
    }
}
