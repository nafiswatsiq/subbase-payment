<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('subbase-payment::subbase-payment/frontend.checkout.title', ['plan' => $plan->name]) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative min-h-screen bg-slate-950 p-6 font-sans text-white">
    <div class="absolute -top-32 -left-32 h-96 w-96 rounded-full bg-indigo-600/30 blur-3xl"></div>
    <div class="absolute -bottom-32 -right-32 h-96 w-96 rounded-full bg-purple-600/30 blur-3xl"></div>

    <div class="relative mx-auto max-w-4xl rounded-3xl border border-white/20 bg-white/10 p-8 backdrop-blur-2xl shadow-2xl">
        <header class="flex items-center justify-between border-b border-white/10 pb-6">
            <a href="{{ url('/') }}" class="text-xl font-bold tracking-tight text-white">
                {{ config('app.name') }}
            </a>
            <span class="rounded-full bg-indigo-500/20 px-3 py-1 text-xs font-semibold text-indigo-300 ring-1 ring-indigo-500/40">
                {{ __('subbase-payment::subbase-payment/frontend.checkout.badge') }}
            </span>
        </header>

        <div class="mt-8 grid grid-cols-1 gap-8 md:grid-cols-2">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur-md">
                <h2 class="text-xl font-bold text-white">{{ $plan->name }}</h2>
                <p class="mt-2 text-sm text-slate-300">{{ $plan->description }}</p>

                <div class="mt-6 border-t border-white/10 pt-4">
                    <div class="flex justify-between font-bold text-lg">
                        <span>{{ __('subbase-payment::subbase-payment/frontend.checkout.total') }}</span>
                        <span>{{ $currency }} {{ number_format((float)$pricing['final_amount'], 2) }}</span>
                    </div>
                </div>
            </div>

            <div>
                @if(isset($subscriptionAction) && $subscriptionAction === 'active')
                    <div class="mb-6 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-xs text-emerald-200 backdrop-blur-md">
                        <span class="rounded-md bg-emerald-500 px-2 py-0.5 font-bold text-white uppercase text-[10px] mr-2">{{ __('subbase-payment::subbase-payment/frontend.checkout.active_badge') }}</span>
                        {{ __('subbase-payment::subbase-payment/frontend.checkout.active_notice') }}
                    </div>
                @elseif(isset($subscriptionAction) && $subscriptionAction === 'renew')
                    <div class="mb-6 rounded-2xl border border-indigo-500/30 bg-indigo-500/10 p-4 text-xs text-indigo-200 backdrop-blur-md">
                        <span class="rounded-md bg-indigo-500 px-2 py-0.5 font-bold text-white uppercase text-[10px] mr-2">{{ __('subbase-payment::subbase-payment/frontend.checkout.renewal_badge') }}</span>
                        {{ __('subbase-payment::subbase-payment/frontend.checkout.renewal_notice') }}
                    </div>
                @elseif(isset($subscriptionAction) && $subscriptionAction === 'switch')
                    <div class="mb-6 rounded-2xl border border-purple-500/30 bg-purple-500/10 p-4 text-xs text-purple-200 backdrop-blur-md">
                        <span class="rounded-md bg-purple-500 px-2 py-0.5 font-bold text-white uppercase text-[10px] mr-2">{{ __('subbase-payment::subbase-payment/frontend.checkout.switch_badge') }}</span>
                        {{ __('subbase-payment::subbase-payment/frontend.checkout.switch_notice') }}
                    </div>
                @endif

                <form action="{{ route('subbase-payment.checkout.store', $plan->slug) }}" method="POST"@unless($isFreePlan) target="subbase_payment_popup" onsubmit="window.open('about:blank', 'subbase_payment_popup', 'width=580,height=700,top=' + Math.max(0, (screen.height - 700) / 2) + ',left=' + Math.max(0, (screen.width - 580) / 2) + ',resizable=yes,scrollbars=yes');"@endunless class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-slate-200">{{ __('subbase-payment::subbase-payment/frontend.checkout.full_name') }}</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" required class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 p-3 text-white placeholder-slate-400 backdrop-blur-md focus:border-indigo-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-200">{{ __('subbase-payment::subbase-payment/frontend.checkout.email_address') }}</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 p-3 text-white placeholder-slate-400 backdrop-blur-md focus:border-indigo-400 focus:outline-none">
                    </div>

                    <button type="submit" @disabled(isset($subscriptionAction) && $subscriptionAction === 'active') class="w-full rounded-xl {{ isset($subscriptionAction) && $subscriptionAction === 'active' ? 'bg-slate-700/50 cursor-not-allowed opacity-50' : 'bg-gradient-to-r from-indigo-500 to-purple-500 hover:opacity-90 shadow-lg' }} py-3.5 font-bold text-white transition">
                        {{ $isFreePlan ? __('subbase-payment::subbase-payment/frontend.checkout.subscribe') : __('subbase-payment::subbase-payment/frontend.checkout.pay_now', ['driver' => ucfirst($driverName)]) }}
                    </button>
                    <p class="mt-2 text-center text-xs text-slate-400">{{ $isFreePlan ? __('subbase-payment::subbase-payment/frontend.checkout.free_plan_notice') : __('subbase-payment::subbase-payment/frontend.checkout.redirect_notice') }}</p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
