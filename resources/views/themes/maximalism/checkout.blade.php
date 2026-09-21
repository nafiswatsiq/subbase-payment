<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('subbase-payment::subbase-payment/frontend.checkout.title', ['plan' => $plan->name]) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#34135c] p-4 font-sans text-black sm:p-8">
    <div class="mx-auto max-w-5xl border-4 border-black bg-pink-400 p-5 shadow-[12px_12px_0px_0px_rgba(250,204,21,1)] sm:p-9">
        <header class="flex flex-col gap-5 border-b-4 border-black pb-6 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ url('/') }}" class="bg-black px-4 py-1 text-2xl font-black uppercase text-yellow-300">
                {{ config('app.name') }}
            </a>
            <span class="w-fit border-2 border-black bg-emerald-400 px-3 py-1 text-xs font-black uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                {{ __('subbase-payment::subbase-payment/frontend.checkout.badge') }}
            </span>
        </header>

        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-[0.9fr_1.1fr]">
            <div class="border-4 border-black bg-white p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] sm:p-8">
                <p class="text-[10px] font-black uppercase tracking-[0.25em] text-purple-700">Order summary / 01</p>
                <h2 class="mt-4 text-3xl font-black uppercase leading-none">{{ $plan->name }}</h2>
                <p class="mt-4 text-sm font-bold leading-6">{{ $plan->description }}</p>

                <div class="mt-7 border-y-4 border-black py-5">
                    <div class="flex justify-between font-black uppercase text-xl">
                        <span>{{ __('subbase-payment::subbase-payment/frontend.checkout.total') }}</span>
                        <span>{{ $pricing['final_price'] }}</span>
                    </div>
                </div>
            </div>

            <div>
                @if(isset($subscriptionAction) && $subscriptionAction === 'active')
                    <div class="mb-6 border-4 border-black bg-emerald-400 p-4 font-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-xs uppercase">
                        <span class="bg-black text-white px-2 py-0.5 mr-2">{{ __('subbase-payment::subbase-payment/frontend.checkout.active_badge') }}</span>
                        {{ __('subbase-payment::subbase-payment/frontend.checkout.active_notice') }}
                    </div>
                @elseif(isset($subscriptionAction) && $subscriptionAction === 'renew')
                    <div class="mb-6 border-4 border-black bg-cyan-400 p-4 font-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-xs uppercase">
                        <span class="bg-black text-white px-2 py-0.5 mr-2">{{ __('subbase-payment::subbase-payment/frontend.checkout.renewal_badge') }}</span>
                        {{ __('subbase-payment::subbase-payment/frontend.checkout.renewal_notice') }}
                    </div>
                @elseif(isset($subscriptionAction) && $subscriptionAction === 'switch')
                    <div class="mb-6 border-4 border-black bg-purple-400 p-4 font-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-xs uppercase">
                        <span class="bg-black text-white px-2 py-0.5 mr-2">{{ __('subbase-payment::subbase-payment/frontend.checkout.switch_badge') }}</span>
                        {{ __('subbase-payment::subbase-payment/frontend.checkout.switch_notice') }}
                    </div>
                @endif

                <form action="{{ route('subbase-payment.checkout.store', $plan->slug) }}" method="POST"@unless($isFreePlan) target="subbase_payment_popup" onsubmit="window.open('about:blank', 'subbase_payment_popup', 'width=580,height=700,top=' + Math.max(0, (screen.height - 700) / 2) + ',left=' + Math.max(0, (screen.width - 580) / 2) + ',resizable=yes,scrollbars=yes');"@endunless class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-black uppercase text-black">{{ __('subbase-payment::subbase-payment/frontend.checkout.full_name') }}</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" required class="mt-2 w-full border-4 border-black bg-white p-3 font-bold shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-black uppercase text-black">{{ __('subbase-payment::subbase-payment/frontend.checkout.email_address') }}</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required class="mt-2 w-full border-4 border-black bg-white p-3 font-bold shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] focus:outline-none">
                    </div>

                    <button type="submit" @disabled(isset($subscriptionAction) && $subscriptionAction === 'active') class="w-full border-4 border-black {{ isset($subscriptionAction) && $subscriptionAction === 'active' ? 'bg-gray-400 cursor-not-allowed opacity-60 shadow-none' : 'bg-yellow-300 hover:bg-emerald-400 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]' }} py-4 font-black uppercase tracking-wider text-black transition-all">
                        {{ $isFreePlan ? __('subbase-payment::subbase-payment/frontend.checkout.subscribe') : __('subbase-payment::subbase-payment/frontend.checkout.pay_now', ['driver' => ucfirst($driverName)]) }}
                    </button>
                    <p class="mt-2 text-center text-xs font-black uppercase text-black">{{ $isFreePlan ? __('subbase-payment::subbase-payment/frontend.checkout.free_plan_notice') : __('subbase-payment::subbase-payment/frontend.checkout.redirect_notice') }}</p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
