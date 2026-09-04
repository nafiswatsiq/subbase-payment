<?php

namespace Nafiswatsiq\SubbasePayment\Gateways;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Nafiswatsiq\SubbasePayment\Contracts\PaymentGatewayInterface;
use Nafiswatsiq\SubbasePayment\Data\PaymentRequest;
use Nafiswatsiq\SubbasePayment\Data\PaymentResult;
use Nafiswatsiq\SubbasePayment\Exceptions\PaymentConfigurationException;
use Nafiswatsiq\SubbasePayment\Exceptions\InvalidWebhookSignatureException;
use RuntimeException;

class PaypalGateway implements PaymentGatewayInterface
{
    public function charge(PaymentRequest $request): PaymentResult
    {
        $response = $this->api()->post('/v2/checkout/orders', [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'description' => $request->plan->name,
                'amount' => [
                    'currency_code' => strtoupper($request->currency),
                    'value' => $request->amount,
                ],
            ]],
            'payer' => ['name' => ['given_name' => $request->customerName], 'email_address' => $request->customerEmail],
            'application_context' => [
                'return_url' => $request->returnUrl,
                'cancel_url' => $request->cancelUrl,
                'user_action' => 'PAY_NOW',
            ],
        ])->throw();

        $payload = $response->json();
        $approvalLink = collect($payload['links'] ?? [])->firstWhere('rel', 'approve');
        $approvalUrl = is_array($approvalLink) ? ($approvalLink['href'] ?? null) : null;

        if (! $approvalUrl || empty($payload['id'])) {
            throw new RuntimeException('PayPal did not return an approval URL.');
        }

        return new PaymentResult('pending', $payload['id'], $approvalUrl, ['provider' => 'paypal']);
    }

    public function cancel(string $transactionId): PaymentResult
    {
        throw new RuntimeException('PayPal order cancellation is handled by the webhook lifecycle.');
    }

    public function handleWebhook(array $payload, array $headers = []): PaymentResult
    {
        $this->verifyWebhook($payload, $headers);

        $eventType = (string) ($payload['event_type'] ?? '');
        $status = match ($eventType) {
            'PAYMENT.CAPTURE.COMPLETED', 'CHECKOUT.ORDER.COMPLETED' => 'paid',
            'PAYMENT.CAPTURE.DENIED', 'PAYMENT.CAPTURE.DECLINED' => 'failed',
            'PAYMENT.CAPTURE.REVERSED', 'PAYMENT.CAPTURE.REFUNDED' => 'canceled',
            default => 'pending',
        };

        return new PaymentResult(
            $status,
            $payload['resource']['supplementary_data']['related_ids']['order_id']
                ?? $payload['resource']['id']
                ?? null,
            null,
            ['event_id' => $payload['id'] ?? null, 'payload' => $payload],
        );
    }

    protected function api(): PendingRequest
    {
        $clientId = config('subbase-payment.gateways.paypal.client_id');
        $secret = config('subbase-payment.gateways.paypal.secret');

        if (! $clientId || ! $secret) {
            throw new PaymentConfigurationException('PayPal client credentials are not configured.');
        }

        $baseUrl = rtrim(config('subbase-payment.gateways.paypal.base_url'), '/');
        $token = Http::asForm()
            ->withBasicAuth($clientId, $secret)
            ->post($baseUrl.'/v1/oauth2/token', ['grant_type' => 'client_credentials'])
            ->throw()
            ->json('access_token');

        if (! $token) {
            throw new RuntimeException('PayPal did not return an access token.');
        }

        return Http::baseUrl($baseUrl)
            ->withToken($token)
            ->acceptJson()
            ->asJson();
    }

    private function verifyWebhook(array $payload, array $headers): void
    {
        $webhookId = config('subbase-payment.gateways.paypal.webhook_id');

        if (! $webhookId) {
            throw new PaymentConfigurationException('PayPal webhook ID is not configured.');
        }

        $response = $this->api()->post('/v1/notifications/verify-webhook-signature', [
            'auth_algo' => $headers['paypal-auth-algo'] ?? '',
            'cert_url' => $headers['paypal-cert-url'] ?? '',
            'transmission_id' => $headers['paypal-transmission-id'] ?? '',
            'transmission_sig' => $headers['paypal-transmission-sig'] ?? '',
            'transmission_time' => $headers['paypal-transmission-time'] ?? '',
            'webhook_id' => $webhookId,
            'webhook_event' => $payload,
        ])->throw();

        if ($response->json('verification_status') !== 'SUCCESS') {
            throw new InvalidWebhookSignatureException('PayPal webhook signature verification failed.');
        }
    }
}