<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $status === 'pending' ? __('subbase-payment::subbase-payment/frontend.status.title_pending') : __('subbase-payment::subbase-payment/frontend.status.title_canceled') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative grid min-h-screen place-items-center overflow-hidden bg-gray-900 px-6 text-white">
    <div class="absolute -right-24 -top-24 h-80 w-80 rounded-full border-[32px] border-blue-500/10"></div>
    <div class="absolute -bottom-32 -left-20 h-72 w-72 rounded-full bg-blue-500/10 blur-3xl"></div>

    <main class="relative w-full max-w-lg rounded-2xl bg-white p-7 text-gray-900 shadow-2xl shadow-black/20 ring-1 ring-white/10 sm:p-12">
        <div class="flex items-center justify-between border-b border-gray-100 pb-6">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-sm font-bold tracking-wide text-gray-900">
                {{ config('app.name') }}
            </a>
            <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-600 ring-1 ring-blue-100">{{ __('subbase-payment::subbase-payment/frontend.status.badge') }}</span>
        </div>

        <div class="mt-10 text-center">
            <div class="mx-auto grid h-16 w-16 place-items-center rounded-full {{ $status === 'pending' ? 'bg-blue-50 text-blue-600 ring-8 ring-blue-50/70' : 'bg-gray-100 text-gray-500 ring-8 ring-gray-50' }} text-2xl">
                @if($status === 'pending')
                    &#10003;
                @else
                    &#8592;
                @endif
            </div>
            <p class="mt-8 text-xs font-bold uppercase tracking-[0.2em] {{ $status === 'pending' ? 'text-blue-600' : 'text-gray-500' }}">
                {{ $status === 'pending' ? __('subbase-payment::subbase-payment/frontend.status.label_received') : __('subbase-payment::subbase-payment/frontend.status.label_canceled') }}
            </p>
            <h1 class="mx-auto mt-3 max-w-md text-2xl font-bold leading-tight tracking-tight text-gray-900 sm:text-3xl">{{ $status === 'pending' ? __('subbase-payment::subbase-payment/frontend.status.heading_pending') : __('subbase-payment::subbase-payment/frontend.status.heading_canceled') }}</h1>
        </div>

        <p class="mt-8 text-sm leading-6 text-gray-500">
            {{ $status === 'pending' ? __('subbase-payment::subbase-payment/frontend.status.subtitle_pending') : __('subbase-payment::subbase-payment/frontend.status.subtitle_canceled') }}
        </p>

        <div class="mt-8 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-left text-xs leading-5 text-gray-500">
            <span class="font-semibold text-gray-700">{{ __('subbase-payment::subbase-payment/frontend.status.next_title') }}</span>
            {{ $status === 'pending' ? ' ' . __('subbase-payment::subbase-payment/frontend.status.next_pending') : ' ' . __('subbase-payment::subbase-payment/frontend.status.next_canceled') }}
        </div>

        <a href="{{ url('/') }}" class="mt-8 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gray-900 px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-gray-900/15 transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            {{ __('subbase-payment::subbase-payment/frontend.status.back_to_plans') }}
            <span aria-hidden="true" class="text-lg leading-none">&#8594;</span>
        </a>
        <p class="mt-5 text-center text-xs text-gray-400">{{ __('subbase-payment::subbase-payment/frontend.status.footer_secure', ['app' => config('app.name')]) }}</p>
    </main>

    <script>
        if (window.opener && !window.opener.closed) {
            try {
                window.opener.location.href = window.location.href;
                window.close();
            } catch (e) {}
        }
    </script>
</body>
</html>