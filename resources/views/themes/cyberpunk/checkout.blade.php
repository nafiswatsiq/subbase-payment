<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('subbase-payment::subbase-payment/frontend.checkout.title', ['plan' => $plan->name]) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#05070d] p-4 font-mono text-yellow-400 sm:p-8">
    <div class="mx-auto max-w-5xl border border-cyan-400/80 bg-slate-950 p-5 shadow-[0_0_25px_rgba(6,182,212,0.4)] sm:p-9">
        <header class="flex flex-col gap-5 border-b-2 border-cyan-400/50 pb-6 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ url('/') }}" class="text-xl font-black uppercase text-yellow-400 tracking-wider">
                SYS://{{ config('app.name') }}
            </a>
            <span class="w-fit border border-yellow-400 bg-yellow-400/10 px-3 py-1 text-xs font-black uppercase text-yellow-400 shadow-[0_0_10px_rgba(250,204,21,0.4)]">
                TRANSACTION_GATEWAY
            </span>
        </header>

        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-[0.9fr_1.1fr]">
            <div class="border border-cyan-400/40 bg-black p-6 sm:p-8">
                <p class="text-[10px] font-bold uppercase tracking-[0.25em] text-fuchsia-400">// ORDER_SUMMARY / 01</p>
                <h2 class="mt-4 text-3xl font-black uppercase leading-none text-yellow-400">&gt; {{ $plan->name }}</h2>
                <p class="mt-4 text-xs leading-6 text-slate-400">{{ $plan->description }}</p>

                <div class="mt-7 border-y border-cyan-400/40 py-5">
                    <div class="flex justify-between font-black text-lg text-white">
                        <span>TOTAL_COST:</span>
                        <span class="text-yellow-400">{{ $pricing['final_price'] }}</span>
                    </div>
                </div>
            </div>

            <div>
                @if(isset($subscriptionAction) && $subscriptionAction === 'active')
                    <div class="mb-6 border border-emerald-400 bg-emerald-950/40 p-4 text-xs font-bold text-emerald-400 shadow-[0_0_10px_rgba(52,211,153,0.3)]">
                        <span class="bg-emerald-400 text-black px-2 py-0.5 mr-2 font-black uppercase">{{ __('subbase-payment::subbase-payment/frontend.checkout.active_badge') }}</span>
                        {{ __('subbase-payment::subbase-payment/frontend.checkout.active_notice') }}
                    </div>
                @elseif(isset($subscriptionAction) && $subscriptionAction === 'renew')
                    <div class="mb-6 border border-cyan-400 bg-cyan-950/40 p-4 text-xs font-bold text-cyan-400 shadow-[0_0_10px_rgba(6,182,212,0.3)]">
                        <span class="bg-cyan-400 text-black px-2 py-0.5 mr-2 font-black uppercase">{{ __('subbase-payment::subbase-payment/frontend.checkout.renewal_badge') }}</span>
                        {{ __('subbase-payment::subbase-payment/frontend.checkout.renewal_notice') }}
                    </div>
                @elseif(isset($subscriptionAction) && $subscriptionAction === 'switch')
                    <div class="mb-6 border border-purple-400 bg-purple-950/40 p-4 text-xs font-bold text-purple-400 shadow-[0_0_10px_rgba(192,132,252,0.3)]">
                        <span class="bg-purple-400 text-black px-2 py-0.5 mr-2 font-black uppercase">{{ __('subbase-payment::subbase-payment/frontend.checkout.switch_badge') }}</span>
                        {{ __('subbase-payment::subbase-payment/frontend.checkout.switch_notice') }}
                    </div>
                @endif

                <form action="{{ route('subbase-payment.checkout.store', $plan->slug) }}" method="POST"@unless($isFreePlan) target="subbase_payment_popup" onsubmit="window.open('about:blank', 'subbase_payment_popup', 'width=580,height=700,top=' + Math.max(0, (screen.height - 700) / 2) + ',left=' + Math.max(0, (screen.width - 580) / 2) + ',resizable=yes,scrollbars=yes');"@endunless class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-xs font-black uppercase text-cyan-400">// USER_IDENTIFIER</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" required class="mt-2 w-full border-2 border-cyan-400 bg-black p-3 text-white focus:border-yellow-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase text-cyan-400">// COMM_NODE (EMAIL)</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required class="mt-2 w-full border-2 border-cyan-400 bg-black p-3 text-white focus:border-yellow-400 focus:outline-none">
                    </div>

                    <button type="submit" @disabled(isset($subscriptionAction) && $subscriptionAction === 'active') class="w-full border-2 {{ isset($subscriptionAction) && $subscriptionAction === 'active' ? 'border-gray-600 bg-gray-800 text-gray-500 cursor-not-allowed opacity-50 shadow-none' : 'border-yellow-400 bg-yellow-400 text-black shadow-[0_0_20px_rgba(250,204,21,0.7)] hover:bg-cyan-400 hover:border-cyan-400' }} py-4 font-black uppercase tracking-widest transition-all">
                        {{ $isFreePlan ? __('subbase-payment::subbase-payment/frontend.checkout.subscribe') : 'EXECUTE_PAYMENT [' . strtoupper($driverName) . ']' }}
                    </button>
                    <p class="mt-2 text-center text-xs font-mono text-slate-400">{{ $isFreePlan ? __('subbase-payment::subbase-payment/frontend.checkout.free_plan_notice') : __('subbase-payment::subbase-payment/frontend.checkout.redirect_notice') }}</p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
