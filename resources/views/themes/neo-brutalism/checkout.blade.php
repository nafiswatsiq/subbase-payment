<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('subbase-payment::subbase-payment/frontend.checkout.title', ['plan' => $plan->name]) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-amber-50 p-6 font-sans text-black">
    <div class="mx-auto max-w-4xl border-4 border-black bg-white p-8 shadow-[10px_10px_0px_0px_rgba(0,0,0,1)]">
        <header class="flex items-center justify-between border-b-4 border-black pb-6">
            <a href="{{ url('/') }}" class="text-2xl font-black uppercase tracking-wider text-black">
                {{ config('app.name') }}
            </a>
            <span class="border-2 border-black bg-yellow-300 px-3 py-1 text-xs font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                {{ __('subbase-payment::subbase-payment/frontend.checkout.badge') }}
            </span>
        </header>

        <div class="mt-8 grid grid-cols-1 gap-8 md:grid-cols-2">
            <div class="border-4 border-black bg-cyan-100 p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                <h2 class="text-xl font-black uppercase text-black">{{ $plan->name }}</h2>
                <p class="mt-2 text-sm font-bold text-gray-800">{{ $plan->description }}</p>

                <div class="mt-6 border-t-4 border-black pt-4">
                    <div class="flex justify-between font-black uppercase text-lg">
                        <span>{{ __('subbase-payment::subbase-payment/frontend.checkout.total') }}</span>
                        <span>{{ $currency }} {{ number_format((float)$pricing['final_amount'], 2) }}</span>
                    </div>
                </div>

                <ul class="mt-6 space-y-2 border-t-2 border-black pt-4">
                    @foreach($plan->features as $feature)
                        <li class="flex items-center gap-2 text-sm font-bold">
                            <span class="font-black text-black">►</span> {{ $feature->name }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <form action="{{ route('subbase-payment.checkout.store', $plan->slug) }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-black uppercase text-black">{{ __('subbase-payment::subbase-payment/frontend.checkout.full_name') }}</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" required class="mt-2 w-full border-4 border-black bg-white p-3 font-bold text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] focus:outline-none focus:ring-0">
                    </div>

                    <div>
                        <label class="block text-sm font-black uppercase text-black">{{ __('subbase-payment::subbase-payment/frontend.checkout.email_address') }}</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required class="mt-2 w-full border-4 border-black bg-white p-3 font-bold text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] focus:outline-none focus:ring-0">
                    </div>

                    <button type="submit" class="w-full border-4 border-black bg-pink-400 py-4 font-black uppercase tracking-wider text-black shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] transition-all hover:bg-pink-300 active:translate-x-1 active:translate-y-1 active:shadow-none">
                        {{ __('subbase-payment::subbase-payment/frontend.checkout.pay_now', ['driver' => ucfirst($driverName)]) }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
