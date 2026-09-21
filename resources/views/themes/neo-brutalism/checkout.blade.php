<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('subbase-payment::subbase-payment/frontend.checkout.title', ['plan' => $plan->name]) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f4efe6] p-4 font-sans text-black sm:p-8">
    <div class="mx-auto max-w-5xl border-4 border-black bg-white p-5 shadow-[12px_12px_0px_0px_rgba(0,0,0,1)] sm:p-9">
        <header class="flex flex-col gap-5 border-b-4 border-black pb-6 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ url('/') }}" class="text-2xl font-black uppercase tracking-wider text-black">
                {{ config('app.name') }}
            </a>
            <span class="w-fit border-2 border-black bg-yellow-300 px-3 py-1 text-xs font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                {{ __('subbase-payment::subbase-payment/frontend.checkout.badge') }}
            </span>
        </header>

        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-[0.9fr_1.1fr]">
            <div class="border-4 border-black bg-cyan-100 p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] sm:p-8">
                <p class="text-[10px] font-black uppercase tracking-[0.25em] text-cyan-800">ORDER SUMMARY / 01</p>
                <h2 class="mt-4 text-3xl font-black uppercase leading-none text-black">{{ $plan->name }}</h2>
                <p class="mt-4 text-sm font-bold leading-6 text-gray-800">{{ $plan->description }}</p>

                <div class="mt-6 border-t-4 border-black pt-4">
                    <div class="flex justify-between font-black uppercase text-lg">
                        <span>{{ __('subbase-payment::subbase-payment/frontend.checkout.total') }}</span>
                        <span>{{ $pricing['final_price'] }}</span>
                    </div>
                </div>

                <ul class="mt-7 space-y-3 border-t-2 border-black pt-5">
                    @foreach($plan->features as $feature)
                        <li class="flex items-center gap-2 text-sm font-bold">
                            <span class="font-black text-black">►</span> {{ $feature->name }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                @if(isset($subscriptionAction) && $subscriptionAction === 'active')
                    <div class="mb-6 border-4 border-black bg-green-300 p-4 font-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-xs uppercase">
                        <span class="bg-black text-white px-2 py-0.5 mr-2">{{ __('subbase-payment::subbase-payment/frontend.checkout.active_badge') }}</span>
                        {{ __('subbase-payment::subbase-payment/frontend.checkout.active_notice') }}
                    </div>
                @elseif(isset($subscriptionAction) && $subscriptionAction === 'renew')
                    <div class="mb-6 border-4 border-black bg-blue-300 p-4 font-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-xs uppercase">
                        <span class="bg-black text-white px-2 py-0.5 mr-2">{{ __('subbase-payment::subbase-payment/frontend.checkout.renewal_badge') }}</span>
                        {{ __('subbase-payment::subbase-payment/frontend.checkout.renewal_notice') }}
                    </div>
                @elseif(isset($subscriptionAction) && $subscriptionAction === 'switch')
                    <div class="mb-6 border-4 border-black bg-purple-300 p-4 font-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-xs uppercase">
                        <span class="bg-black text-white px-2 py-0.5 mr-2">{{ __('subbase-payment::subbase-payment/frontend.checkout.switch_badge') }}</span>
                        {{ __('subbase-payment::subbase-payment/frontend.checkout.switch_notice') }}
                    </div>
                @endif

                <form action="{{ route('subbase-payment.checkout.store', $plan->slug) }}" method="POST"@unless($isFreePlan) target="subbase_payment_popup" onsubmit="window.open('about:blank', 'subbase_payment_popup', 'width=580,height=700,top=' + Math.max(0, (screen.height - 700) / 2) + ',left=' + Math.max(0, (screen.width - 580) / 2) + ',resizable=yes,scrollbars=yes');"@endunless class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-black uppercase text-black">{{ __('subbase-payment::subbase-payment/frontend.checkout.full_name') }}</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" required class="mt-2 w-full border-4 border-black bg-white p-3 font-bold text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] focus:outline-none focus:ring-0">
                    </div>

                    <div>
                        <label class="block text-sm font-black uppercase text-black">{{ __('subbase-payment::subbase-payment/frontend.checkout.email_address') }}</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required class="mt-2 w-full border-4 border-black bg-white p-3 font-bold text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] focus:outline-none focus:ring-0">
                    </div>

                    <button type="submit" @disabled(isset($subscriptionAction) && $subscriptionAction === 'active') class="w-full border-4 border-black {{ isset($subscriptionAction) && $subscriptionAction === 'active' ? 'bg-gray-300 cursor-not-allowed opacity-60' : 'bg-pink-400 hover:bg-pink-300 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] active:translate-x-1 active:translate-y-1 active:shadow-none' }} py-4 font-black uppercase tracking-wider text-black transition-all">
                        {{ $isFreePlan ? __('subbase-payment::subbase-payment/frontend.checkout.subscribe') : __('subbase-payment::subbase-payment/frontend.checkout.pay_now', ['driver' => ucfirst($driverName)]) }}
                    </button>
                    <p class="mt-2 text-center text-xs font-bold uppercase text-gray-700">{{ $isFreePlan ? __('subbase-payment::subbase-payment/frontend.checkout.free_plan_notice') : __('subbase-payment::subbase-payment/frontend.checkout.redirect_notice') }}</p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
