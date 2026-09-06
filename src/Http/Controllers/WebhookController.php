<?php

namespace Nafiswatsiq\SubbasePayment\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Nafiswatsiq\SubbasePayment\Exceptions\InvalidWebhookSignatureException;
use Nafiswatsiq\SubbasePayment\Exceptions\PaymentConfigurationException;
use Nafiswatsiq\SubbasePayment\Models\PaymentWebhookLog;
use Nafiswatsiq\SubbasePayment\PaymentManager;

class WebhookController extends Controller
{
    public function __invoke(Request $request, PaymentManager $payments): JsonResponse
    {
        $driverName = config('subbase-payment.driver', 'unknown');
        $rawPayload = $request->json()->all();
        $rawPayload['_raw_body'] = $request->getContent();
        $rawHeaders = collect($request->headers->all())
            ->mapWithKeys(fn (array $value, string $key): array => [strtolower($key) => $value[0] ?? ''])
            ->all();

        $log = PaymentWebhookLog::create([
            'gateway_driver' => $driverName,
            'event_id' => $rawPayload['id'] ?? $rawPayload['event_id'] ?? null,
            'event_type' => $rawPayload['event_type'] ?? $rawPayload['type'] ?? null,
            'status' => 'received',
            'payload' => $rawPayload,
            'headers' => $rawHeaders,
        ]);

        try {
            $result = $payments->driver()->handleWebhook(
                $rawPayload,
                $rawHeaders,
            );
        } catch (InvalidWebhookSignatureException $e) {
            $log->update([
                'status' => 'failed',
                'error_message' => 'Invalid webhook signature: ' . $e->getMessage(),
            ]);

            return response()->json(['message' => 'Invalid webhook signature.'], 401);
        } catch (PaymentConfigurationException $e) {
            $log->update([
                'status' => 'failed',
                'error_message' => 'Payment configuration error: ' . $e->getMessage(),
            ]);

            return response()->json(['message' => 'Payment configuration error: ' . $e->getMessage()], 500);
        } catch (\Throwable $e) {
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }

        $eventId = $result->data['event_id'] ?? $log->event_id;
        if ($eventId) {
            $log->update([
                'event_id' => $eventId,
                'event_type' => $result->data['event_type'] ?? $log->event_type,
            ]);
        }

        $table = config('subbase-payment.tables.subscription_payments', 'subscription_payments');

        if ($eventId && DB::table($table)->where('webhook_event_id', $eventId)->exists()) {
            $log->update(['status' => 'duplicate']);

            return response()->json(['ok' => true]);
        }

        if (! $result->transactionId) {
            $log->update([
                'status' => 'ignored',
                'error_message' => null,
            ]);

            return response()->json(['message' => 'Webhook received and ignored (no transaction_id).'], 200);
        }

        $found = DB::transaction(function () use ($table, $result, $eventId, $log): bool {
            if ($eventId && DB::table($table)->where('webhook_event_id', $eventId)->exists()) {
                $log->update(['status' => 'duplicate']);

                return true;
            }

            $payment = DB::table($table)
                ->where('gateway_transaction_id', $result->transactionId)
                ->lockForUpdate()
                ->first();

            if (! $payment) {
                return false;
            }

            $isPaid = $payment->payment_status === 'paid';
            $isFinal = in_array($payment->payment_status, ['paid', 'failed', 'canceled'], true);

            $shouldUpdateStatus = true;
            if ($isFinal && in_array($result->status, ['pending', 'approved', 'completed'], true)) {
                $shouldUpdateStatus = false;
            }

            $updates = [
                'webhook_event_id' => $eventId,
                'updated_at' => now(),
            ];

            if ($shouldUpdateStatus) {
                $updates['payment_status'] = $result->status;
            }

            if ($result->status === 'paid' && ! $payment->verified_at) {
                $updates['verified_at'] = now();
            }

            DB::table($table)->where('id', $payment->id)->update($updates);

            if ($result->status === 'paid' && ! $isPaid) {
                $updatedRecord = DB::table($table)->where('id', $payment->id)->first();
                $metadata = is_string($updatedRecord->metadata ?? null) ? (json_decode($updatedRecord->metadata, true) ?? []) : [];
                event(new \Nafiswatsiq\SubbasePayment\Events\PaymentReceived($updatedRecord, $metadata));
            }

            $log->update(['status' => 'verified']);

            return true;
        });

        if (! $found) {
            $log->update([
                'status' => 'failed',
                'error_message' => 'Payment transaction was not found.',
            ]);

            return response()->json(['message' => 'Payment transaction was not found.'], 200);
        }

        return response()->json(['ok' => true]);
    }
}