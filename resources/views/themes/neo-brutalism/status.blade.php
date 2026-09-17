<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $status === 'pending' ? __('subbase-payment::subbase-payment/frontend.status.title_pending') : ($status === 'success' ? __('subbase-payment::subbase-payment/frontend.status.title_success') : __('subbase-payment::subbase-payment/frontend.status.title_canceled')) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="grid min-h-screen place-items-center bg-amber-50 p-6 font-sans text-black">
    <main class="w-full max-w-lg border-4 border-black bg-white p-8 shadow-[10px_10px_0px_0px_rgba(0,0,0,1)]">
        <div class="flex items-center justify-between border-b-4 border-black pb-4">
            <span class="text-xl font-black uppercase">{{ config('app.name') }}</span>
            <span class="border-2 border-black bg-yellow-300 px-2 py-0.5 text-xs font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">STATUS</span>
        </div>

        <div class="mt-8 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center border-4 border-black font-black text-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] {{ $status === 'canceled' ? 'bg-rose-300' : 'bg-emerald-300' }}">
                {{ $status === 'canceled' ? '✕' : '✓' }}
            </div>
            <h1 class="mt-6 text-3xl font-black uppercase text-black">
                {{ $status === 'pending' ? __('subbase-payment::subbase-payment/frontend.status.heading_pending') : ($status === 'success' ? __('subbase-payment::subbase-payment/frontend.status.heading_success') : __('subbase-payment::subbase-payment/frontend.status.heading_canceled')) }}
            </h1>
            <p class="mt-4 text-sm font-bold text-gray-800">
                {{ $status === 'pending' ? __('subbase-payment::subbase-payment/frontend.status.subtitle_pending') : ($status === 'success' ? __('subbase-payment::subbase-payment/frontend.status.subtitle_success') : __('subbase-payment::subbase-payment/frontend.status.subtitle_canceled')) }}
            </p>
        </div>

        @if(!empty($redirectUrl))
            <a href="{{ $redirectUrl }}" class="mt-8 block w-full border-4 border-black bg-black py-4 text-center font-black uppercase text-white shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] transition-all hover:bg-gray-800">
                {{ __('subbase-payment::subbase-payment/frontend.status.continue_to_destination') }}
            </a>
        @else
            <a href="{{ url('/') }}" class="mt-8 block w-full border-4 border-black bg-cyan-300 py-4 text-center font-black uppercase text-black shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] transition-all hover:bg-cyan-200">
                {{ __('subbase-payment::subbase-payment/frontend.status.back_to_plans') }}
            </a>
        @endif
    </main>
</body>
</html>
