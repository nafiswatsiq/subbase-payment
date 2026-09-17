<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('subbase-payment::subbase-payment/frontend.checkout.title', ['plan' => $plan->name]) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-purple-950 p-6 font-sans text-black">
    <div class="mx-auto max-w-4xl border-4 border-black bg-pink-400 p-8 shadow-[12px_12px_0px_0px_rgba(250,204,21,1)]">
        <header class="flex items-center justify-between border-b-4 border-black pb-6">
            <a href="{{ url('/') }}" class="bg-black px-4 py-1 text-2xl font-black uppercase text-yellow-300">
                {{ config('app.name') }}
            </a>
            <span class="border-2 border-black bg-emerald-400 px-3 py-1 text-xs font-black uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                {{ __('subbase-payment::subbase-payment/frontend.checkout.badge') }}
            </span>
        </header>

        <div class="mt-8 grid grid-cols-1 gap-8 md:grid-cols-2">
            <div class="border-4 border-black bg-white p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                <h2 class="text-2xl font-black uppercase">{{ $plan->name }}</h2>
                <p class="mt-2 text-sm font-bold">{{ $plan->description }}</p>

                <div class="mt-6 border-t-4 border-black pt-4">
                    <div class="flex justify-between font-black uppercase text-xl">
                        <span>{{ __('subbase-payment::subbase-payment/frontend.checkout.total') }}</span>
                        <span>{{ $currency }} {{ number_format((float)$pricing['final_amount'], 2) }}</span>
                    </div>
                </div>
            </div>

            <div>
                <form action="{{ route('subbase-payment.checkout.store', $plan->slug) }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-black uppercase text-black">{{ __('subbase-payment::subbase-payment/frontend.checkout.full_name') }}</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" required class="mt-2 w-full border-4 border-black bg-white p-3 font-bold shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-black uppercase text-black">{{ __('subbase-payment::subbase-payment/frontend.checkout.email_address') }}</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required class="mt-2 w-full border-4 border-black bg-white p-3 font-bold shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] focus:outline-none">
                    </div>

                    <button type="submit" class="w-full border-4 border-black bg-yellow-300 py-4 font-black uppercase tracking-wider text-black shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] transition-all hover:bg-emerald-400">
                        {{ __('subbase-payment::subbase-payment/frontend.checkout.pay_now', ['driver' => ucfirst($driverName)]) }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
