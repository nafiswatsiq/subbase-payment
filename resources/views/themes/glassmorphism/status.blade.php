<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $status === 'pending' ? __('subbase-payment::subbase-payment/frontend.status.title_pending') : ($status === 'success' ? __('subbase-payment::subbase-payment/frontend.status.title_success') : __('subbase-payment::subbase-payment/frontend.status.title_canceled')) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative grid min-h-screen place-items-center bg-slate-950 p-6 font-sans text-white">
    <div class="absolute -top-32 -left-32 h-96 w-96 rounded-full bg-indigo-600/30 blur-3xl"></div>
    <div class="absolute -bottom-32 -right-32 h-96 w-96 rounded-full bg-purple-600/30 blur-3xl"></div>

    <main class="relative w-full max-w-lg rounded-3xl border border-white/20 bg-white/10 p-8 backdrop-blur-2xl shadow-2xl">
        <div class="flex items-center justify-between border-b border-white/10 pb-4">
            <span class="text-lg font-bold">{{ config('app.name') }}</span>
            <span class="rounded-full bg-indigo-500/20 px-3 py-1 text-xs font-semibold text-indigo-300">STATUS</span>
        </div>

        <div class="mt-8 text-center">
            <div class="mx-auto grid h-16 w-16 place-items-center rounded-full border border-white/20 bg-white/10 text-2xl font-bold backdrop-blur-md">
                {{ $status === 'canceled' ? '✕' : '✓' }}
            </div>
            <h1 class="mt-6 text-2xl font-bold text-white">
                {{ $status === 'pending' ? __('subbase-payment::subbase-payment/frontend.status.heading_pending') : ($status === 'success' ? __('subbase-payment::subbase-payment/frontend.status.heading_success') : __('subbase-payment::subbase-payment/frontend.status.heading_canceled')) }}
            </h1>
            <p class="mt-3 text-sm text-slate-300">
                {{ $status === 'pending' ? __('subbase-payment::subbase-payment/frontend.status.subtitle_pending') : ($status === 'success' ? __('subbase-payment::subbase-payment/frontend.status.subtitle_success') : __('subbase-payment::subbase-payment/frontend.status.subtitle_canceled')) }}
            </p>
        </div>

        @if(!empty($redirectUrl))
            <a href="{{ $redirectUrl }}" class="mt-8 block w-full rounded-xl bg-gradient-to-r from-indigo-500 to-purple-500 py-3.5 text-center font-bold text-white shadow-lg transition hover:opacity-90">
                {{ __('subbase-payment::subbase-payment/frontend.status.continue_to_destination') }}
            </a>
        @else
            <a href="{{ url('/') }}" class="mt-8 block w-full rounded-xl border border-white/20 bg-white/10 py-3.5 text-center font-bold text-white backdrop-blur-md transition hover:bg-white/20">
                {{ __('subbase-payment::subbase-payment/frontend.status.back_to_plans') }}
            </a>
        @endif
    </main>
</body>
</html>
