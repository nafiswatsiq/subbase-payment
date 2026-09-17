<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('subbase-payment::subbase-payment/frontend.checkout.title', ['plan' => $plan->name]) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-black p-6 font-mono text-yellow-400">
    <div class="mx-auto max-w-4xl border-2 border-cyan-400 bg-slate-950 p-8 shadow-[0_0_25px_rgba(6,182,212,0.4)]">
        <header class="flex items-center justify-between border-b-2 border-cyan-400/50 pb-6">
            <a href="{{ url('/') }}" class="text-xl font-black uppercase text-yellow-400 tracking-wider">
                SYS://{{ config('app.name') }}
            </a>
            <span class="border border-yellow-400 bg-yellow-400/10 px-3 py-1 text-xs font-black uppercase text-yellow-400 shadow-[0_0_10px_rgba(250,204,21,0.4)]">
                TRANSACTION_GATEWAY
            </span>
        </header>

        <div class="mt-8 grid grid-cols-1 gap-8 md:grid-cols-2">
            <div class="border border-cyan-400/40 bg-black p-6">
                <h2 class="text-xl font-black uppercase text-yellow-400">&gt; {{ $plan->name }}</h2>
                <p class="mt-2 text-xs text-slate-400">{{ $plan->description }}</p>

                <div class="mt-6 border-t border-cyan-400/40 pt-4">
                    <div class="flex justify-between font-black text-lg text-white">
                        <span>TOTAL_COST:</span>
                        <span class="text-yellow-400">{{ $currency }} {{ number_format((float)$pricing['final_amount'], 2) }}</span>
                    </div>
                </div>
            </div>

            <div>
                <form action="{{ route('subbase-payment.checkout.store', $plan->slug) }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-xs font-black uppercase text-cyan-400">// USER_IDENTIFIER</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" required class="mt-2 w-full border-2 border-cyan-400 bg-black p-3 text-white focus:border-yellow-400 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-black uppercase text-cyan-400">// COMM_NODE (EMAIL)</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required class="mt-2 w-full border-2 border-cyan-400 bg-black p-3 text-white focus:border-yellow-400 focus:outline-none">
                    </div>

                    <button type="submit" class="w-full border-2 border-yellow-400 bg-yellow-400 py-4 font-black uppercase tracking-widest text-black shadow-[0_0_20px_rgba(250,204,21,0.7)] transition-all hover:bg-cyan-400 hover:border-cyan-400">
                        EXECUTE_PAYMENT [{{ strtoupper($driverName) }}]
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
