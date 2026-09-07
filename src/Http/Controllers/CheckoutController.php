<?php

namespace Nafiswatsiq\SubbasePayment\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Nafiswatsiq\SubbasePayment\Data\PaymentRequest;
use Nafiswatsiq\SubbasePayment\PaymentManager;
use Nafiswatsiq\Subbase\Helpers\PlanPriceHelper;

class CheckoutController extends Controller
{
    public function show(string $plan)
    {
        $planModel = config('subbase.models.plan', \Nafiswatsiq\Subbase\Models\Plan::class);
        $plan = $planModel::query()
            ->with('features')
            ->where('slug', $plan)
            ->active()
            ->firstOrFail();

        $currency = $planModel::currencyFromLocale(app()->getLocale());
        $driver = app(\Nafiswatsiq\SubbasePayment\PaymentManager::class)->driver();

        $user = Auth::user();
        $subscriptionAction = 'new';

        if ($user && method_exists($user, 'planSubscriptions')) {
            $activeSub = $user->planSubscriptions()->get()->filter(fn ($sub) => $sub->active())->first();
            if ($activeSub) {
                $subscriptionAction = (string) $activeSub->plan_id === (string) $plan->getKey() ? 'renew' : 'switch';
            }
        }

        return view('subbase-payment::checkout', [
            'plan' => $plan,
            'pricing' => PlanPriceHelper::formatWithDiscounts($plan, $currency),
            'currency' => $currency,
            'driverName' => $driver->name(),
            'driverLogo' => $driver->logo(),
            'subscriptionAction' => $subscriptionAction,
        ]);
    }

    public function store(Request $request, string $plan, PaymentManager $payments)
    {
        $user = Auth::user();

        if (! $user) {
            throw ValidationException::withMessages(['payment' => 'You must be logged in to subscribe.']);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $planModel = config('subbase.models.plan', \Nafiswatsiq\Subbase\Models\Plan::class);
        $plan = $planModel::query()->where('slug', $plan)->active()->firstOrFail();
        $currency = $planModel::currencyFromLocale(app()->getLocale());
        $pricing = PlanPriceHelper::resolveWithDiscounts($plan, $currency);

        $subscriptionAction = 'new';
        if (method_exists($user, 'planSubscriptions')) {
            $activeSub = $user->planSubscriptions()->get()->filter(fn ($sub) => $sub->active())->first();
            if ($activeSub) {
                $subscriptionAction = (string) $activeSub->plan_id === (string) $plan->getKey() ? 'renew' : 'switch';
            }
        }

        try {
            $returnUrl = route('subbase-payment.checkout.return', $plan->slug);
            $cancelUrl = route('subbase-payment.checkout.cancel', $plan->slug);

            $result = $payments->driver()->charge(new PaymentRequest(
                $plan,
                number_format((float) $pricing['final_amount'], 2, '.', ''),
                $currency,
                $request->string('name')->toString(),
                $request->string('email')->toString(),
                $returnUrl,
                $cancelUrl,
            ));
        } catch (\Throwable $exception) {
            report($exception);
            throw ValidationException::withMessages(['payment' => 'Unable to start payment. Please try again later.']);
        }

        DB::table(config('subbase-payment.tables.subscription_payments', 'subscription_payments'))->insert([
            'gateway_driver' => config('subbase-payment.driver'),
            'gateway_transaction_id' => $result->transactionId,
            'payment_status' => $result->status,
            'customer_name' => $request->string('name')->toString(),
            'customer_email' => $request->string('email')->toString(),
            'amount' => $pricing['final_amount'],
            'currency' => $currency,
            'metadata' => json_encode([
                'plan_id' => $plan->getKey(),
                'plan_slug' => $plan->slug,
                'user_id' => $user->getAuthIdentifier(),
                'subscription_action' => $subscriptionAction,
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->away($result->approvalUrl);
    }

    public function returned(Request $request, string $plan, PaymentManager $payments)
    {
        $orderId = $request->string('token')->toString()
            ?: $request->string('payment_id')->toString()
            ?: $request->string('_ptxn')->toString();
        $user = Auth::user();

        if ($user && $orderId) {
            $table = config('subbase-payment.tables.subscription_payments', 'subscription_payments');
            $payment = DB::table($table)
                ->where('gateway_transaction_id', $orderId)
                ->whereIn('payment_status', ['pending', 'approved'])
                ->first();
            $metadata = $payment && is_string($payment->metadata)
                ? (json_decode($payment->metadata, true) ?? [])
                : [];

            if ($payment && (string) ($metadata['user_id'] ?? '') === (string) $user->getAuthIdentifier()) {
                try {
                    $driver = $payments->driver();

                    if ($driver instanceof \Nafiswatsiq\SubbasePayment\Contracts\CapturesPayments) {
                        $capture = $driver->capture($orderId);

                        if ($capture->status === 'paid') {
                            DB::transaction(function () use ($table, $payment): void {
                                DB::table($table)
                                    ->where('id', $payment->id)
                                    ->whereIn('payment_status', ['pending', 'approved'])
                                    ->lockForUpdate()
                                    ->update([
                                        'payment_status' => 'completed',
                                        'updated_at' => now(),
                                    ]);
                            });
                        }
                    }
                } catch (\Throwable $exception) {
                    report($exception);
                }
            }
        }

        $redirectTarget = null;
        $returnUrl = config('subbase-payment.checkout.return_url');
        if ($returnUrl) {
            $redirectTarget = Str::startsWith($returnUrl, 'http')
                ? $returnUrl
                : route($returnUrl, $plan);
        }

        return view('subbase-payment::status', [
            'plan' => $plan,
            'status' => 'pending',
            'redirectUrl' => $redirectTarget,
        ]);
    }

    public function canceled(string $plan)
    {
        $redirectTarget = null;
        $cancelUrl = config('subbase-payment.checkout.cancel_url');
        if ($cancelUrl) {
            $redirectTarget = Str::startsWith($cancelUrl, 'http')
                ? $cancelUrl
                : route($cancelUrl, $plan);
        }

        return view('subbase-payment::status', [
            'plan' => $plan,
            'status' => 'canceled',
            'redirectUrl' => $redirectTarget,
        ]);
    }
}