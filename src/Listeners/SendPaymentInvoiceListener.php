<?php

namespace Nafiswatsiq\SubbasePayment\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Nafiswatsiq\SubbasePayment\Events\PaymentReceived;
use Nafiswatsiq\SubbasePayment\Mail\PaymentInvoiceMail;

class SendPaymentInvoiceListener implements ShouldQueue
{
    public function handle(PaymentReceived $event): void
    {
        if (! config('subbase-payment.mail.send_invoice', false)) {
            return;
        }

        $payment = $event->paymentRecord;
        $customerEmail = $payment->customer_email ?? null;

        if (! $customerEmail) {
            return;
        }

        $planFeatures = [];
        $planName = $payment->plan_name ?? null;

        $metadata = is_string($payment->metadata ?? null) 
            ? (json_decode($payment->metadata, true) ?? []) 
            : (array) ($payment->metadata ?? []);

        $planSlug = $payment->plan_slug ?? ($metadata['plan_slug'] ?? null);
        $planId = $payment->plan_id ?? ($metadata['plan_id'] ?? null);

        try {
            if (class_exists('\Nafiswatsiq\Subbase\Models\Plan')) {
                $planModel = config('subbase.models.plan', \Nafiswatsiq\Subbase\Models\Plan::class);
                $query = $planModel::query();

                if ($planSlug) {
                    $query->where('slug', $planSlug);
                } elseif ($planId) {
                    $query->where('id', $planId);
                }

                $plan = $query->first();

                if ($plan) {
                    $planName = $plan->name;
                    if (method_exists($plan, 'features')) {
                        $planFeatures = $plan->features->toArray();
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore if subbase plan table not migrated or accessible
        }

        $paymentData = (object) array_merge((array) $payment, [
            'plan_name' => $planName ?? 'Subscription Plan',
        ]);

        Mail::to($customerEmail)->send(new PaymentInvoiceMail($paymentData, $planFeatures));
    }
}
