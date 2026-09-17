<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $status === 'pending' ? __('subbase-payment::subbase-payment/frontend.status.title_pending') : ($status === 'success' ? __('subbase-payment::subbase-payment/frontend.status.title_success') : __('subbase-payment::subbase-payment/frontend.status.title_canceled')) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="grid min-h-screen place-items-center bg-black p-6 font-mono text-yellow-400">
    <main class="w-full max-w-lg border-2 border-cyan-400 bg-slate-950 p-8 shadow-[0_0_25px_rgba(6,182,212,0.4)]">
        <div class="flex items-center justify-between border-b border-cyan-400/40 pb-4">
            <span class="text-sm font-black uppercase text-yellow-400">SYS://{{ config('app.name') }}</span>
            <span class="border border-cyan-400 bg-cyan-400/10 px-2 py-0.5 text-xs text-cyan-400">STATUS_CHECK</span>
        </div>

        <div class="mt-8 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center border-2 border-yellow-400 bg-black text-2xl font-black text-yellow-400 shadow-[0_0_15px_rgba(250,204,21,0.6)]">
                {{ $status === 'canceled' ? '[X]' : '[OK]' }}
            </div>
            <h1 class="mt-6 text-2xl font-black uppercase tracking-wider text-white">
                {{ $status === 'pending' ? __('subbase-payment::subbase-payment/frontend.status.heading_pending') : ($status === 'success' ? __('subbase-payment::subbase-payment/frontend.status.heading_success') : __('subbase-payment::subbase-payment/frontend.status.heading_canceled')) }}
            </h1>
            <p class="mt-3 text-xs text-slate-400">
                {{ $status === 'pending' ? __('subbase-payment::subbase-payment/frontend.status.subtitle_pending') : ($status === 'success' ? __('subbase-payment::subbase-payment/frontend.status.subtitle_success') : __('subbase-payment::subbase-payment/frontend.status.subtitle_canceled')) }}
            </p>
        </div>

        @if(!empty($redirectUrl))
            <a href="{{ $redirectUrl }}" class="mt-8 block w-full border-2 border-yellow-400 bg-yellow-400 py-3.5 text-center font-black uppercase text-black shadow-[0_0_15px_rgba(250,204,21,0.6)] hover:bg-cyan-400 hover:border-cyan-400">
                {{ __('subbase-payment::subbase-payment/frontend.status.continue_to_destination') }}
            </a>
        @else
            <a href="{{ url('/') }}" class="mt-8 block w-full border-2 border-cyan-400 bg-black py-3.5 text-center font-black uppercase text-cyan-400 hover:bg-cyan-400 hover:text-black">
                {{ __('subbase-payment::subbase-payment/frontend.status.back_to_plans') }}
            </a>
        @endif
    </main>
</body>
</html>
