<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $status === 'pending' ? __('subbase-payment::subbase-payment/frontend.status.title_pending') : ($status === 'success' ? __('subbase-payment::subbase-payment/frontend.status.title_success') : __('subbase-payment::subbase-payment/frontend.status.title_canceled')) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes status-check {
            from { stroke-dashoffset: 24; }
            to { stroke-dashoffset: 0; }
        }

        @keyframes status-return {
            from { opacity: 0; transform: translateX(5px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .payment-status-icon {
            position: relative;
            width: 5rem;
            height: 5rem;
            margin-inline: auto;
        }

        .payment-status-icon__surface,
        .payment-status-icon__orbit,
        .payment-status-icon__ripple,
        .payment-status-icon__svg {
            position: absolute;
            inset: 0;
        }

        .payment-status-icon__surface {
            border-radius: 9999px;
            background: #eff6ff;
            box-shadow: 0 0 0 8px rgb(239 246 255 / 70%);
        }

        .payment-status-icon__surface--canceled {
            background: #f3f4f6;
            box-shadow: 0 0 0 8px #f9fafb;
        }

        .payment-status-icon__orbit {
            inset: 0.25rem;
            border: 3px solid #dbeafe;
            border-top-color: #2563eb;
            border-radius: 9999px;
            animation: spin 1s linear infinite;
        }

        .payment-status-icon__svg {
            width: 2.5rem;
            height: 2.5rem;
            margin: auto;
            color: #2563eb;
        }

        .payment-status-icon__svg--pending {
            width: 2rem;
            height: 2rem;
            animation: status-pulse 1.6s ease-in-out infinite;
        }

        .payment-status-icon__svg--canceled {
            width: 2.25rem;
            height: 2.25rem;
            color: #6b7280;
        }

        .payment-status-icon__ripple {
            border: 1px solid #bfdbfe;
            border-radius: 9999px;
            animation: status-ripple 2.4s ease-out infinite;
        }

        .payment-status-icon__check {
            transform-origin: center;
            animation: status-check 600ms ease-out both;
        }

        .payment-status-icon__return {
            transform-origin: center;
            animation: status-return 500ms ease-out both;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes status-pulse {
            50% { opacity: 0.45; transform: scale(0.86); }
        }

        @keyframes status-ripple {
            0% { opacity: 0.9; transform: scale(0.8); }
            100% { opacity: 0; transform: scale(1.3); }
        }

        @media (prefers-reduced-motion: reduce) {
            .payment-status-icon__orbit,
            .payment-status-icon__ripple,
            .payment-status-icon__svg--pending,
            .payment-status-icon__check,
            .payment-status-icon__return {
                animation: none;
            }
        }
    </style>
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
            <div class="payment-status-icon" aria-hidden="true">
                <div class="payment-status-icon__surface {{ $status === 'canceled' ? 'payment-status-icon__surface--canceled' : '' }}"></div>
                @if($status === 'pending')
                    <div class="payment-status-icon__orbit"></div>
                    <svg class="payment-status-icon__svg payment-status-icon__svg--pending" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="3.25" />
                        <path d="M12 6.5v1.25M12 16.25v1.25M6.5 12h1.25M16.25 12h1.25" />
                    </svg>
                @elseif($status === 'success')
                    <svg class="payment-status-icon__svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 12.5 9.25 17 19 7" pathLength="24" stroke-dasharray="24" stroke-dashoffset="24" class="payment-status-icon__check" />
                    </svg>
                    <span class="payment-status-icon__ripple"></span>
                @else
                    <svg class="payment-status-icon__svg payment-status-icon__svg--canceled" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M19 12H5M10 7l-5 5 5 5" class="payment-status-icon__return" />
                    </svg>
                @endif
            </div>
            <p class="mt-8 text-xs font-bold uppercase tracking-[0.2em] {{ $status === 'canceled' ? 'text-gray-500' : 'text-blue-600' }}">
                {{ $status === 'pending' ? __('subbase-payment::subbase-payment/frontend.status.label_received') : ($status === 'success' ? __('subbase-payment::subbase-payment/frontend.status.label_success') : __('subbase-payment::subbase-payment/frontend.status.label_canceled')) }}
            </p>
            <h1 class="mx-auto mt-3 max-w-md text-2xl font-bold leading-tight tracking-tight text-gray-900 sm:text-3xl">{{ $status === 'pending' ? __('subbase-payment::subbase-payment/frontend.status.heading_pending') : ($status === 'success' ? __('subbase-payment::subbase-payment/frontend.status.heading_success') : __('subbase-payment::subbase-payment/frontend.status.heading_canceled')) }}</h1>
        </div>

        <p class="mt-8 text-sm leading-6 text-gray-500">
            {{ $status === 'pending' ? __('subbase-payment::subbase-payment/frontend.status.subtitle_pending') : ($status === 'success' ? __('subbase-payment::subbase-payment/frontend.status.subtitle_success') : __('subbase-payment::subbase-payment/frontend.status.subtitle_canceled')) }}
        </p>

        <div class="mt-8 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-left text-xs leading-5 text-gray-500">
            <span class="font-semibold text-gray-700">{{ __('subbase-payment::subbase-payment/frontend.status.next_title') }}</span>
            {{ $status === 'pending' ? ' ' . __('subbase-payment::subbase-payment/frontend.status.next_pending') : ($status === 'success' ? ' ' . __('subbase-payment::subbase-payment/frontend.status.next_success') : ' ' . __('subbase-payment::subbase-payment/frontend.status.next_canceled')) }}
        </div>

        @if(!empty($redirectUrl))
            <a href="{{ $redirectUrl }}" class="mt-8 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gray-900 px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-gray-900/15 transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                {{ __('subbase-payment::subbase-payment/frontend.status.continue_to_destination') }}
                <span aria-hidden="true" class="text-lg leading-none">&#8594;</span>
            </a>
            <p id="timer-notice" class="mt-3 text-center text-xs text-gray-500 font-medium"></p>
        @else
            <a href="{{ url('/') }}" class="mt-8 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gray-900 px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-gray-900/15 transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                {{ __('subbase-payment::subbase-payment/frontend.status.back_to_plans') }}
                <span aria-hidden="true" class="text-lg leading-none">&#8594;</span>
            </a>
        @endif
        <p class="mt-5 text-center text-xs text-gray-400">{{ __('subbase-payment::subbase-payment/frontend.status.footer_secure', ['app' => config('app.name')]) }}</p>
    </main>

    <script>
        (function() {
            function handleAutoClose() {
                if (window.opener && !window.opener.closed) {
                    try {
                        window.opener.location.href = window.location.href;
                        window.close();
                        return true;
                    } catch (e) {}
                }
                return false;
            }

            if (!handleAutoClose()) {
                @if(!empty($redirectUrl))
                    let seconds = 10;
                    const targetUrl = @json($redirectUrl);
                    const noticeEl = document.getElementById('timer-notice');
                    const template = @json(__('subbase-payment::subbase-payment/frontend.status.redirecting_in'));

                    function updateNotice() {
                        if (noticeEl) {
                            noticeEl.textContent = template.replace(':seconds', seconds);
                        }
                    }

                    updateNotice();
                    const interval = setInterval(function() {
                        seconds--;
                        if (seconds <= 0) {
                            clearInterval(interval);
                            window.location.href = targetUrl;
                        } else {
                            updateNotice();
                        }
                    }, 1000);
                @endif
            }
        })();
    </script>
</body>
</html>