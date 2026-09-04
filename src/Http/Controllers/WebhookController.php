<?php

namespace Nafiswatsiq\SubbasePayment\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Nafiswatsiq\SubbasePayment\Exceptions\InvalidWebhookSignatureException;
use Nafiswatsiq\SubbasePayment\PaymentManager;

class WebhookController extends Controller
{
    public function __invoke(Request $request, PaymentManager $payments): JsonResponse
    {
        try {
            $result = $payments->driver()->handleWebhook(
                $request->json()->all(),
                collect($request->headers->all())
                    ->mapWithKeys(fn (array $value, string $key): array => [strtolower($key) => $value[0] ?? ''])
                    ->all(),
            );
        } catch (InvalidWebhookSignatureException) {
            return response()->json(['message' => 'Invalid webhook signature.'], 401);
        }

        $table = config('subbase-payment.tables.subscription_payments', 'subscription_payments');
        $eventId = $result->data['event_id'] ?? null;

        if ($eventId && DB::table($table)->where('webhook_event_id', $eventId)->exists()) {
            return response()->json(['ok' => true]);
        }

        $found = DB::transaction(function () use ($table, $result, $eventId): bool {
            if ($eventId && DB::table($table)->where('webhook_event_id', $eventId)->exists()) {
                return true;
            }

            $payment = DB::table($table)
                ->where('gateway_transaction_id', $result->transactionId)
                ->lockForUpdate()
                ->first();

            if (! $payment) {
                return false;
            }

            $updates = [
                'payment_status' => $result->status,
                'webhook_event_id' => $eventId,
                'updated_at' => now(),
            ];

            if ($result->status === 'paid') {
                $updates['verified_at'] = now();
            }

            DB::table($table)->where('id', $payment->id)->update($updates);

            if ($result->status === 'paid') {
                $updatedRecord = DB::table($table)->where('id', $payment->id)->first();
                $metadata = is_string($updatedRecord->metadata ?? null) ? (json_decode($updatedRecord->metadata, true) ?? []) : [];
                event(new \Nafiswatsiq\SubbasePayment\Events\PaymentReceived($updatedRecord, $metadata));
            }

            return true;
        });

        if (! $found) {
            return response()->json(['message' => 'Payment transaction was not found.'], 404);
        }

        return response()->json(['ok' => true]);
    }
}