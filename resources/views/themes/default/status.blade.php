@php($theme = config('subbase.theme', 'default'))
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $status === 'pending' ? __('subbase-payment::subbase-payment/frontend.status.title_pending') : ($status === 'success' ? __('subbase-payment::subbase-payment/frontend.status.title_success') : __('subbase-payment::subbase-payment/frontend.status.title_canceled')) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .theme-neo-brutalism { background: #f4efe6 !important; }
        .theme-neo-brutalism main { border: 4px solid #000; border-radius: 0; box-shadow: 12px 12px 0 #000; }
        .theme-neo-brutalism main a { border-radius: 0; border: 3px solid #000; box-shadow: 5px 5px 0 #000; }
        .theme-glassmorphism { background: #0b1020 !important; }
        .theme-glassmorphism main { background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.2); border-radius: 2rem; backdrop-filter: blur(24px); }
        .theme-glassmorphism main a { background: linear-gradient(100deg,#6366f1,#c026d3); border-radius: 1rem; }
        .theme-claymorphism { background: #e9edf5 !important; }
        .theme-claymorphism main { background: #f8fafc; border: 1px solid #fff; border-radius: 2rem; box-shadow: 18px 18px 36px rgba(15,23,42,.1), -18px -18px 36px rgba(255,255,255,.95); }
        .theme-claymorphism main a { border-radius: 1rem; background: #4f46e5; }
        .theme-cyberpunk { background: #05070d !important; }
        .theme-cyberpunk main { background: #020617; border: 1px solid #22d3ee; border-radius: 0; box-shadow: 0 0 28px rgba(6,182,212,.35); }
        .theme-cyberpunk main a { border: 1px solid #facc15; border-radius: 0; background: #facc15; color: #000; }
        .theme-maximalism { background: #34135c !important; }
        .theme-maximalism main { border: 4px solid #000; border-radius: 0; background: #f472b6; box-shadow: 12px 12px 0 #facc15; }
        .theme-maximalism main a { border: 3px solid #000; border-radius: 0; background: #facc15; color: #000; box-shadow: 5px 5px 0 #000; }
        .theme-neo-brutalism main > div:nth-child(2) > div { border: 3px solid #000; border-radius: 0; background: #a7f3d0; color: #000; box-shadow: 4px 4px 0 #000; }
        .theme-neo-brutalism main > div:nth-child(2) p,
        .theme-neo-brutalism main > div:nth-child(2) h1 { color: #000; }
        .theme-glassmorphism main > div:nth-child(2) > div { border-color: rgba(255,255,255,.2); border-radius: 1rem; background: rgba(255,255,255,.08); color: #e2e8f0; }
        .theme-glassmorphism main > div:nth-child(2) p,
        .theme-glassmorphism main > div:nth-child(2) h1 { color: #e2e8f0; }
        .theme-claymorphism main > div:nth-child(2) > div { border-color: #e2e8f0; border-radius: 1rem; background: #eef2f7; color: #475569; box-shadow: inset 3px 3px 8px rgba(15,23,42,.04); }
        .theme-claymorphism main > div:nth-child(2) p,
        .theme-claymorphism main > div:nth-child(2) h1 { color: #475569; }
        .theme-cyberpunk main > div:nth-child(2) > div { border: 1px solid rgba(34,211,238,.5); border-radius: 0; background: #000; color: #67e8f9; }
        .theme-cyberpunk main > div:nth-child(2) p,
        .theme-cyberpunk main > div:nth-child(2) h1 { color: #67e8f9; }
        .theme-maximalism main > div:nth-child(2) > div { border: 4px solid #000; border-radius: 0; background: #facc15; color: #000; box-shadow: 4px 4px 0 #000; }
        .theme-maximalism main > div:nth-child(2) p,
        .theme-maximalism main > div:nth-child(2) h1 { color: #000; }
    </style>
</head>
<body class="theme-{{ $theme }} relative grid min-h-screen place-items-center overflow-hidden bg-gray-900 px-6 text-white">
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
            <div class="mx-auto grid h-16 w-16 place-items-center rounded-full {{ $status === 'canceled' ? 'bg-gray-100 text-gray-500 ring-8 ring-gray-50' : 'bg-blue-50 text-blue-600 ring-8 ring-blue-50/70' }} text-2xl">
                @if($status !== 'canceled')
                    &#10003;
                @else
                    &#8592;
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