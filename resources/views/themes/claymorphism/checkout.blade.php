<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('subbase-payment::subbase-payment/frontend.checkout.title', ['plan' => $plan->name]) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 p-6 font-sans text-slate-800">
    <div class="mx-auto max-w-4xl rounded-3xl bg-slate-50 p-8 shadow-[14px_14px_28px_0px_rgba(0,0,0,0.06),-14px_-14px_28px_0px_rgba(255,255,255,0.9)] border border-white">
        <header class="flex items-center justify-between border-b border-slate-200 pb-6">
            <a href="{{ url('/') }}" class="text-xl font-bold tracking-tight text-slate-900">
                {{ config('app.name') }}
            </a>
            <span class="rounded-full bg-indigo-100 px-3.5 py-1 text-xs font-bold text-indigo-600 shadow-[2px_2px_4px_0px_rgba(0,0,0,0.05)]">
                {{ __('subbase-payment::subbase-payment/frontend.checkout.badge') }}
            </span>
        </header>

        <div class="mt-8 grid grid-cols-1 gap-8 md:grid-cols-2">
            <div class="rounded-2xl bg-indigo-50/60 p-6 shadow-[inset_4px_4px_8px_0px_rgba(0,0,0,0.03)] border border-indigo-100">
                <h2 class="text-xl font-bold text-slate-900">{{ $plan->name }}</h2>
                <p class="mt-2 text-sm text-slate-600">{{ $plan->description }}</p>

                <div class="mt-6 border-t border-slate-200 pt-4">
                    <div class="flex justify-between font-bold text-lg text-slate-900">
                        <span>{{ __('subbase-payment::subbase-payment/frontend.checkout.total') }}</span>
                        <span>{{ $currency }} {{ number_format((float)$pricing['final_amount'], 2) }}</span>
                    </div>
                </div>
            </div>

            <div>
                <form action="{{ route('subbase-payment.checkout.store', $plan->slug) }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-bold text-slate-700">{{ __('subbase-payment::subbase-payment/frontend.checkout.full_name') }}</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" required class="mt-2 w-full rounded-2xl bg-slate-100 p-3.5 text-slate-800 shadow-inner focus:outline-none border border-slate-200">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700">{{ __('subbase-payment::subbase-payment/frontend.checkout.email_address') }}</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required class="mt-2 w-full rounded-2xl bg-slate-100 p-3.5 text-slate-800 shadow-inner focus:outline-none border border-slate-200">
                    </div>

                    <button type="submit" class="w-full rounded-2xl bg-indigo-600 py-3.5 font-bold text-white shadow-[6px_6px_12px_0px_rgba(99,102,241,0.3)] transition hover:bg-indigo-500">
                        {{ __('subbase-payment::subbase-payment/frontend.checkout.pay_now', ['driver' => ucfirst($driverName)]) }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
