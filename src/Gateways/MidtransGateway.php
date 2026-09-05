<?php

namespace Nafiswatsiq\SubbasePayment\Gateways;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Nafiswatsiq\SubbasePayment\Contracts\PaymentGatewayInterface;
use Nafiswatsiq\SubbasePayment\Data\PaymentRequest;
use Nafiswatsiq\SubbasePayment\Data\PaymentResult;
use Nafiswatsiq\SubbasePayment\Exceptions\InvalidWebhookSignatureException;
use Nafiswatsiq\SubbasePayment\Exceptions\PaymentConfigurationException;
use RuntimeException;

class MidtransGateway implements PaymentGatewayInterface
{
    public function charge(PaymentRequest $request): PaymentResult
    {
        $orderId = 'SUB-'.strtoupper(Str::random(12));
        $amount = (int) round((float) $request->amount);

        $response = $this->snap()->post('/transactions', [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'item_details' => [[
                'id' => $request->plan->getKey(),
                'price' => $amount,
                'quantity' => 1,
                'name' => $request->plan->name,
            ]],
            'customer_details' => [
                'first_name' => $request->customerName,
                'email' => $request->customerEmail,
            ],
            'callbacks' => [
                'finish' => $request->returnUrl,
                'error' => $request->cancelUrl,
                'unfinish' => $request->cancelUrl,
            ],
            'credit_card' => ['secure' => true],
        ])->throw();

        $payload = $response->json();

        if (empty($payload['token']) || empty($payload['redirect_url'])) {
            throw new RuntimeException('Midtrans did not return a Snap token.');
        }

        return new PaymentResult('pending', $orderId, $payload['redirect_url'], [
            'provider' => 'midtrans',
            'snap_token' => $payload['token'],
        ]);
    }

    public function cancel(string $transactionId): PaymentResult
    {
        $response = $this->api()->post('/v2/'.$transactionId.'/cancel')->throw();

        return new PaymentResult('canceled', $transactionId, null, [
            'cancel' => $response->json(),
        ]);
    }

    public function handleWebhook(array $payload, array $headers = []): PaymentResult
    {
        $this->verifySignature($payload);

        return new PaymentResult(
            $this->mapStatus($payload),
            $payload['order_id'] ?? null,
            null,
            [
                'event_id' => isset($payload['transaction_id'], $payload['transaction_status'])
                    ? $payload['transaction_id'].':'.$payload['transaction_status']
                    : ($payload['transaction_id'] ?? $payload['order_id'] ?? null),
                'event_type' => $payload['transaction_status'] ?? null,
                'payload' => $payload,
            ],
        );
    }

    public function name(): string
    {
        return 'Midtrans';
    }

        public function logo(): ?string
    {
        if (file_exists(public_path('vendor/subbase-payment/images/midtrans.png'))) {
            return asset('vendor/subbase-payment/images/midtrans.png');
        }

        return 'https://assets.midtrans.com/images/logo/midtrans-logo.png';
    }

    protected function snap(): PendingRequest
    {
        return $this->authorized()->baseUrl(
            $this->isProduction()
                ? 'https://app.midtrans.com/snap/v1'
                : 'https://app.sandbox.midtrans.com/snap/v1'
        );
    }

    protected function api(): PendingRequest
    {
        return $this->authorized()->baseUrl(
            $this->isProduction()
                ? 'https://api.midtrans.com'
                : 'https://api.sandbox.midtrans.com'
        );
    }

    private function authorized(): PendingRequest
    {
        $serverKey = config('subbase-payment.gateways.midtrans.server_key');

        if (! $serverKey) {
            throw new PaymentConfigurationException('Midtrans server key is not configured.');
        }

        return Http::withBasicAuth($serverKey, '')
            ->acceptJson()
            ->asJson();
    }

    private function isProduction(): bool
    {
        return filter_var(
            config('subbase-payment.gateways.midtrans.is_production', false),
            FILTER_VALIDATE_BOOLEAN
        );
    }

    private function verifySignature(array $payload): void
    {
        $serverKey = config('subbase-payment.gateways.midtrans.server_key');

        if (! $serverKey) {
            throw new PaymentConfigurationException('Midtrans server key is not configured.');
        }

        $signature = $payload['signature_key'] ?? null;
        $expected = hash(
            'sha512',
            ($payload['order_id'] ?? '')
            .($payload['status_code'] ?? '')
            .($payload['gross_amount'] ?? '')
            .$serverKey
        );

        if (! $signature || ! hash_equals($expected, (string) $signature)) {
            throw new InvalidWebhookSignatureException('Midtrans webhook signature verification failed.');
        }
    }

    private function mapStatus(array $payload): string
    {
        $status = (string) ($payload['transaction_status'] ?? '');
        $fraud = (string) ($payload['fraud_status'] ?? '');

        return match (true) {
            $status === 'settlement' => 'paid',
            $status === 'capture' && $fraud === 'accept' => 'paid',
            $status === 'pending' => 'pending',
            in_array($status, ['deny', 'failure', 'expire'], true) => 'failed',
            in_array($status, ['cancel', 'refund', 'partial_refund'], true) => 'canceled',
            default => 'pending',
        };
    }
}