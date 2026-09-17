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
