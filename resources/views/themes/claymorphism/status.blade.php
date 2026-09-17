<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $status === 'pending' ? __('subbase-payment::subbase-payment/frontend.status.title_pending') : ($status === 'success' ? __('subbase-payment::subbase-payment/frontend.status.title_success') : __('subbase-payment::subbase-payment/frontend.status.title_canceled')) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="grid min-h-screen place-items-center bg-slate-100 p-6 font-sans text-slate-800">
    <main class="w-full max-w-lg rounded-3xl bg-slate-50 p-8 shadow-[14px_14px_28px_0px_rgba(0,0,0,0.06),-14px_-14px_28px_0px_rgba(255,255,255,0.9)] border border-white">
        <div class="flex items-center justify-between border-b border-slate-200 pb-4">
            <span class="text-lg font-bold text-slate-900">{{ config('app.name') }}</span>
            <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-bold text-indigo-600">STATUS</span>
        </div>

        <div class="mt-8 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-2xl font-bold text-indigo-600 shadow-inner">
                {{ $status === 'canceled' ? '✕' : '✓' }}
            </div>
            <h1 class="mt-6 text-2xl font-bold text-slate-900">
                {{ $status === 'pending' ? __('subbase-payment::subbase-payment/frontend.status.heading_pending') : ($status === 'success' ? __('subbase-payment::subbase-payment/frontend.status.heading_success') : __('subbase-payment::subbase-payment/frontend.status.heading_canceled')) }}
            </h1>
            <p class="mt-3 text-sm text-slate-600">
                {{ $status === 'pending' ? __('subbase-payment::subbase-payment/frontend.status.subtitle_pending') : ($status === 'success' ? __('subbase-payment::subbase-payment/frontend.status.subtitle_success') : __('subbase-payment::subbase-payment/frontend.status.subtitle_canceled')) }}
            </p>
        </div>

        @if(!empty($redirectUrl))
            <a href="{{ $redirectUrl }}" class="mt-8 block w-full rounded-2xl bg-indigo-600 py-3.5 text-center font-bold text-white shadow-[6px_6px_12px_0px_rgba(99,102,241,0.3)] transition hover:bg-indigo-500">
                {{ __('subbase-payment::subbase-payment/frontend.status.continue_to_destination') }}
            </a>
        @else
            <a href="{{ url('/') }}" class="mt-8 block w-full rounded-2xl bg-slate-200 py-3.5 text-center font-bold text-slate-700 shadow-[4px_4px_8px_0px_rgba(0,0,0,0.05)] transition hover:bg-slate-300">
                {{ __('subbase-payment::subbase-payment/frontend.status.back_to_plans') }}
            </a>
        @endif
    </main>
</body>
</html>
