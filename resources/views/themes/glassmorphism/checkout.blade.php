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
                <form action="{{ route('subbase-payment.checkout.store', $plan->slug) }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold text-slate-200">{{ __('subbase-payment::subbase-payment/frontend.checkout.full_name') }}</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" required class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 p-3 text-white placeholder-slate-400 backdrop-blur-md focus:border-indigo-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-200">{{ __('subbase-payment::subbase-payment/frontend.checkout.email_address') }}</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required class="mt-2 w-full rounded-xl border border-white/20 bg-white/10 p-3 text-white placeholder-slate-400 backdrop-blur-md focus:border-indigo-400 focus:outline-none">
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-indigo-500 to-purple-500 py-3.5 font-bold text-white shadow-lg transition hover:opacity-90">
                        {{ __('subbase-payment::subbase-payment/frontend.checkout.pay_now', ['driver' => ucfirst($driverName)]) }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
