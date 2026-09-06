<?php

namespace Nafiswatsiq\SubbasePayment\Tests;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Nafiswatsiq\SubbasePayment\Data\PaymentRequest;
use Nafiswatsiq\SubbasePayment\Exceptions\InvalidWebhookSignatureException;
use Nafiswatsiq\SubbasePayment\Exceptions\PaymentConfigurationException;
use Nafiswatsiq\SubbasePayment\Gateways\PaddleGateway;
use Nafiswatsiq\Subbase\Models\Plan;

class PaddleGatewayTest extends TestCase
{
    protected PaddleGateway $gateway;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gateway = new PaddleGateway();
        Config::set('subbase-payment.gateways.paddle.api_key', 'pdl_api_test_key');
        Config::set('subbase-payment.gateways.paddle.webhook_secret', 'pdl_ntf_test_secret');
        Config::set('subbase-payment.gateways.paddle.environment', 'sandbox');
    }

    public function test_name_and_logo(): void
    {
        $this->assertEquals('Paddle', $this->gateway->name());
        $this->assertNotEmpty($this->gateway->logo());
    }

    public function test_charge_success(): void
    {
        Http::fake([
            'https://sandbox-api.paddle.com/transactions' => Http::response([
                'data' => [
                    'id' => 'txn_01h1234567890',
                    'url' => 'https://sandbox-checkout.paddle.com/txn_01h1234567890',
                ],
            ], 200),
        ]);

        $plan = new Plan(['name' => 'Pro Plan']);
        $plan->id = 1;

        $request = new PaymentRequest(
            plan: $plan,
            amount: '19.99',
            currency: 'USD',
            customerEmail: 'user@example.com',
            customerName: 'John Doe',
            returnUrl: 'https://example.com/success',
            cancelUrl: 'https://example.com/cancel'
        );

        $result = $this->gateway->charge($request);

        $this->assertEquals('pending', $result->status);
        $this->assertEquals('txn_01h1234567890', $result->transactionId);
        $this->assertEquals('https://sandbox-checkout.paddle.com/txn_01h1234567890', $result->approvalUrl);
    }

    public function test_cancel_success(): void
    {
        Http::fake([
            'https://sandbox-api.paddle.com/transactions/txn_01h1234567890/cancel' => Http::response([
                'data' => [
                    'id' => 'txn_01h1234567890',
                    'status' => 'canceled',
                ],
            ], 200),
        ]);

        $result = $this->gateway->cancel('txn_01h1234567890');

        $this->assertEquals('canceled', $result->status);
        $this->assertEquals('txn_01h1234567890', $result->transactionId);
    }

    public function test_webhook_verification_success(): void
    {
        $payload = [
            'event_id' => 'evt_123',
            'event_type' => 'transaction.completed',
            'data' => [
                'id' => 'txn_01h1234567890',
                'status' => 'completed',
            ],
        ];

        $ts = time();
        $rawBody = json_encode($payload);
        $signedPayload = $ts.':'.$rawBody;
        $h1 = hash_hmac('sha256', $signedPayload, 'pdl_ntf_test_secret');

        $headers = [
            'paddle-signature' => "ts={$ts};h1={$h1}",
        ];

        $result = $this->gateway->handleWebhook($payload, $headers);

        $this->assertEquals('paid', $result->status);
        $this->assertEquals('txn_01h1234567890', $result->transactionId);
    }

    public function test_webhook_verification_failure(): void
    {
        $this->expectException(InvalidWebhookSignatureException::class);

        $payload = ['event_id' => 'evt_123', 'event_type' => 'transaction.completed'];
        $headers = ['paddle-signature' => 'ts=12345678;h1=invalid_hash'];

        $this->gateway->handleWebhook($payload, $headers);
    }

    public function test_missing_config_throws_exception(): void
    {
        Config::set('subbase-payment.gateways.paddle.api_key', null);

        $this->expectException(PaymentConfigurationException::class);

        $plan = new Plan(['name' => 'Pro Plan']);
        $plan->id = 1;
        $request = new PaymentRequest($plan, '100', 'USD', 'user@example.com', 'John', 'http://a', 'http://b');

        $this->gateway->charge($request);
    }
}
