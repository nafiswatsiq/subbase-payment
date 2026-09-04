<?php

namespace Nafiswatsiq\SubbasePayment\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
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

        return view('subbase-payment::checkout', [
            'plan' => $plan,
            'pricing' => PlanPriceHelper::formatWithDiscounts($plan, $currency),
            'currency' => $currency,
        ]);
    }

    public function store(Request $request, string $plan, PaymentManager $payments)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $planModel = config('subbase.models.plan', \Nafiswatsiq\Subbase\Models\Plan::class);
        $plan = $planModel::query()->where('slug', $plan)->active()->firstOrFail();
        $currency = $planModel::currencyFromLocale(app()->getLocale());
        $pricing = PlanPriceHelper::resolveWithDiscounts($plan, $currency);

        try {
            $result = $payments->driver()->charge(new PaymentRequest(
                $plan,
                number_format((float) $pricing['final_amount'], 2, '.', ''),
                $currency,
                $request->string('name')->toString(),
                $request->string('email')->toString(),
                route('subbase-payment.checkout.return', $plan->slug),
                route('subbase-payment.checkout.cancel', $plan->slug),
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
            'metadata' => json_encode(['plan_id' => $plan->getKey(), 'plan_slug' => $plan->slug]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->away($result->approvalUrl);
    }

    public function returned(string $plan)
    {
        return view('subbase-payment::status', ['plan' => $plan, 'status' => 'pending']);
    }

    public function canceled(string $plan)
    {
        return view('subbase-payment::status', ['plan' => $plan, 'status' => 'canceled']);
    }
}