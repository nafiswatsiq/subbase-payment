<?php

namespace Nafiswatsiq\SubbasePayment\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureCanCheckoutPlan
{
    /**
     * Handle an incoming request.
     *
     * Prevents checkout if user is already actively subscribed to the same plan,
     * unless perpanjang/renewal is allowed.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && method_exists($user, 'planSubscriptions')) {
            $planSlug = $request->route('plan');

            if (is_string($planSlug)) {
                $planModel = config('subbase.models.plan', \Nafiswatsiq\Subbase\Models\Plan::class);
                $plan = $planModel::query()->where('slug', $planSlug)->first();

                if ($plan && method_exists($user, 'subscribedTo') && $user->subscribedTo($plan->getKey())) {
                    $message = __('subbase-payment::subbase-payment/frontend.checkout.already_subscribed');

                    if ($request->expectsJson()) {
                        return response()->json(['message' => $message], 422);
                    }

                    return redirect()->back()->withErrors(['payment' => $message]);
                }
            }
        }

        return $next($request);
    }
}
