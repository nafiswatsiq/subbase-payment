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

class XenditGateway implements PaymentGatewayInterface
{
    public function charge(PaymentRequest $request): PaymentResult
    {
        $externalId = 'SUB-'.strtoupper(bin2hex(random_bytes(6)));
        $amount = (float) $request->amount;

        $response = $this->api()->post('/v2/invoices', [
            'external_id' => $externalId,
            'amount' => $amount,
            'currency' => strtoupper($request->currency),
            'payer_email' => $request->customerEmail,
            'description' => $request->plan->name,
            'success_redirect_url' => $request->returnUrl,
            'failure_redirect_url' => $request->cancelUrl,
            'customer' => [
                'given_names' => $request->customerName,
                'email' => $request->customerEmail,
            ],
            'items' => [[
                'name' => $request->plan->name,
                'quantity' => 1,
                'price' => $amount,
            ]],
        ])->throw();

        $payload = $response->json();

        if (empty($payload['id']) || empty($payload['invoice_url'])) {
            throw new RuntimeException('Xendit did not return an invoice URL.');
        }

        return new PaymentResult('pending', $payload['id'], $payload['invoice_url'], [
            'provider' => 'xendit',
            'external_id' => $externalId,
            'invoice' => $payload,
        ]);
    }

    public function cancel(string $transactionId): PaymentResult
    {
        $response = $this->api()->post('/v2/invoices/'.$transactionId.'/expire!')->throw();

        return new PaymentResult('canceled', $transactionId, null, [
            'expire' => $response->json(),
        ]);
    }

    public function handleWebhook(array $payload, array $headers = []): PaymentResult
    {
        $this->verifyWebhook($headers);

        $status = (string) ($payload['status'] ?? '');
        $mappedStatus = match ($status) {
            'PAID', 'SETTLED' => 'paid',
            'EXPIRED' => 'failed',
            default => 'pending',
        };

        $transactionId = $payload['id'] ?? $payload['invoice_id'] ?? null;

        return new PaymentResult(
            $mappedStatus,
            $transactionId,
            null,
            [
                'event_id' => isset($payload['id'], $payload['status'])
                    ? $payload['id'].':'.$payload['status']
                    : ($payload['id'] ?? null),
                'event_type' => $payload['status'] ?? null,
                'payload' => $payload,
            ],
        );
    }

    public function name(): string
    {
        return 'Xendit';
    }

    public function logo(): ?string
    {
        if (file_exists(public_path('vendor/subbase-payment/images/xendit.png'))) {
            return asset('vendor/subbase-payment/images/xendit.png');
        }

        return 'https://assets.xendit.co/images/xendit-logo.png';
    }

    protected function api(): PendingRequest
    {
        $secretKey = config('subbase-payment.gateways.xendit.secret_key');

        if (! $secretKey) {
            throw new PaymentConfigurationException('Xendit secret key is not configured.');
        }

        return Http::baseUrl('https://api.xendit.co')
            ->withBasicAuth($secretKey, '')
            ->acceptJson()
            ->asJson();
    }

    private function verifyWebhook(array $headers): void
    {
        $verificationToken = config('subbase-payment.gateways.xendit.webhook_verification_token');

        if (! $verificationToken) {
            throw new PaymentConfigurationException('Xendit webhook verification token is not configured.');
        }

        $headerToken = null;
        foreach ($headers as $key => $value) {
            if (strtolower($key) === 'x-callback-token') {
                $headerToken = is_array($value) ? ($value[0] ?? null) : $value;
                break;
            }
        }

        if (! $headerToken || ! hash_equals($verificationToken, (string) $headerToken)) {
            throw new InvalidWebhookSignatureException('Xendit webhook verification token mismatch.');
        }
    }
}
