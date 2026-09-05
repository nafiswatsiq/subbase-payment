<?php

namespace Nafiswatsiq\SubbasePayment\Tests;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Nafiswatsiq\SubbasePayment\Data\PaymentRequest;
use Nafiswatsiq\SubbasePayment\Exceptions\InvalidWebhookSignatureException;
use Nafiswatsiq\SubbasePayment\Exceptions\PaymentConfigurationException;
use Nafiswatsiq\SubbasePayment\Gateways\XenditGateway;
use Nafiswatsiq\Subbase\Models\Plan;

class XenditGatewayTest extends TestCase
{
    protected XenditGateway $gateway;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gateway = new XenditGateway();
        Config::set('subbase-payment.gateways.xendit.secret_key', 'test_secret_key');
        Config::set('subbase-payment.gateways.xendit.webhook_verification_token', 'test_webhook_token');
    }

    public function test_name_and_logo(): void
    {
        $this->assertEquals('Xendit', $this->gateway->name());
        $this->assertNotEmpty($this->gateway->logo());
    }

    public function test_charge_success(): void
    {
        Http::fake([
            'https://api.xendit.co/v2/invoices' => Http::response([
                'id' => 'inv_12345',
                'invoice_url' => 'https://checkout.xendit.co/web/inv_12345',
            ], 200),
        ]);

        $plan = new Plan(['name' => 'Pro Plan']);
        $plan->id = 1;

        $request = new PaymentRequest(
            plan: $plan,
            amount: '100000',
            currency: 'IDR',
            customerEmail: 'user@example.com',
            customerName: 'John Doe',
            returnUrl: 'https://example.com/success',
            cancelUrl: 'https://example.com/cancel'
        );

        $result = $this->gateway->charge($request);

        $this->assertEquals('pending', $result->status);
        $this->assertEquals('inv_12345', $result->transactionId);
        $this->assertEquals('https://checkout.xendit.co/web/inv_12345', $result->redirectUrl);
    }

    public function test_cancel_success(): void
    {
        Http::fake([
            'https://api.xendit.co/v2/invoices/inv_12345/expire!' => Http::response([
                'id' => 'inv_12345',
                'status' => 'EXPIRED',
            ], 200),
        ]);

        $result = $this->gateway->cancel('inv_12345');

        $this->assertEquals('canceled', $result->status);
        $this->assertEquals('inv_12345', $result->transactionId);
    }

    public function test_webhook_verification_success(): void
    {
        $payload = [
            'id' => 'inv_12345',
            'status' => 'PAID',
        ];

        $headers = [
            'x-callback-token' => 'test_webhook_token',
        ];

        $result = $this->gateway->handleWebhook($payload, $headers);

        $this->assertEquals('paid', $result->status);
        $this->assertEquals('inv_12345', $result->transactionId);
    }

    public function test_webhook_verification_failure(): void
    {
        $this->expectException(InvalidWebhookSignatureException::class);

        $payload = ['id' => 'inv_12345', 'status' => 'PAID'];
        $headers = ['x-callback-token' => ['wrong_token']];

        $this->gateway->handleWebhook($payload, $headers);
    }

    public function test_missing_config_throws_exception(): void
    {
        Config::set('subbase-payment.gateways.xendit.secret_key', null);

        $this->expectException(PaymentConfigurationException::class);

        $plan = new Plan(['name' => 'Pro Plan']);
        $plan->id = 1;
        $request = new PaymentRequest($plan, '100', 'USD', 'user@example.com', 'John', 'http://a', 'http://b');

        $this->gateway->charge($request);
    }
}
