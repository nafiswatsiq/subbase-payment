@php($theme = config('subbase.theme', 'default'))
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $plan->name }} | {{ __('subbase-payment::subbase-payment/frontend.checkout.title') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .theme-neo-brutalism { background: #f4efe6 !important; }
        .theme-neo-brutalism main > div { border: 4px solid #000; border-radius: 0; box-shadow: 12px 12px 0 #000; }
        .theme-neo-brutalism main > div > section:first-child { background: #ffd447; color: #000; }
        .theme-neo-brutalism main > div > section:first-child a, .theme-neo-brutalism main > div > section:first-child h1, .theme-neo-brutalism main > div > section:first-child p, .theme-neo-brutalism main > div > section:first-child span { color: #000; }
        .theme-neo-brutalism main > div > section:last-child { background: #fff; }
        .theme-neo-brutalism input { border: 3px solid #000; border-radius: 0; background: #fff; }
        .theme-neo-brutalism button { border: 3px solid #000; border-radius: 0; background: #000; }
        .theme-glassmorphism { background: #0b1020 !important; color: #fff; }
        .theme-glassmorphism main > div { background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.25); border-radius: 2rem; box-shadow: 0 24px 80px rgba(0,0,0,.4); backdrop-filter: blur(24px); }
        .theme-glassmorphism main > div > section:first-child, .theme-glassmorphism main > div > section:last-child { background: transparent; }
        .theme-glassmorphism main > div > section:first-child { background: linear-gradient(145deg, rgba(99,102,241,.6), rgba(15,23,42,.3)); }
        .theme-glassmorphism input { border-color: rgba(255,255,255,.3); background: rgba(255,255,255,.12); color: #fff; }
        .theme-glassmorphism button { background: linear-gradient(100deg, #6366f1, #c026d3); }
        .theme-neumorphism { background: #e0e5ec !important; color: #0f172a; }
        .theme-neumorphism main > div { background: #e0e5ec; border: none; border-radius: 2.25rem; box-shadow: 16px 16px 32px #b8c2d1, -16px -16px 32px #ffffff; }
        .theme-neumorphism main > div > section:first-child, .theme-neumorphism main > div > section:last-child { background: transparent; }
        .theme-neumorphism main > div > section:first-child { background: linear-gradient(145deg, #4338ca, #3730a3); color: #ffffff; border-radius: 2.25rem 0 0 2.25rem; box-shadow: inset 4px 4px 10px rgba(0,0,0,.2), inset -4px -4px 10px rgba(255,255,255,.15); }
        .theme-neumorphism main > div > section:first-child a,
        .theme-neumorphism main > div > section:first-child h1,
        .theme-neumorphism main > div > section:first-child span { color: #ffffff; }
        .theme-neumorphism main > div > section:first-child p { color: #e0e7ff; }
        .theme-neumorphism main > div > section:first-child .text-blue-400 { color: #818cf8 !important; }
        .theme-neumorphism main > div > section:first-child .text-gray-200,
        .theme-neumorphism main > div > section:first-child .text-gray-300,
        .theme-neumorphism main > div > section:first-child .text-gray-400 { color: #c7d2fe !important; }
        .theme-neumorphism main > div > section:first-child .border-white\/10 { border-color: rgba(255,255,255,.2); }
        .theme-neumorphism main > div > section:first-child .bg-blue-50 { background: #3730a3 !important; box-shadow: inset 2px 2px 4px rgba(0,0,0,.3), inset -2px -2px 4px rgba(255,255,255,.1); }
        .theme-neumorphism main > div > section:first-child .text-blue-600 { color: #ffffff !important; }
        .theme-neumorphism main > div > section:last-child > div:first-child h2,
        .theme-neumorphism main > div > section:last-child > div:first-child p { color: #0f172a; }
        .theme-neumorphism main > div > section:last-child > div:nth-child(2) { border: none; border-radius: 1.5rem; background: #e0e5ec; box-shadow: inset 4px 4px 8px #b8c2d1, inset -4px -4px 8px #ffffff; }
        .theme-neumorphism main > div > section:last-child > div:nth-child(2) p,
        .theme-neumorphism main > div > section:last-child > div:nth-child(2) div { color: #0f172a; }
        .theme-neumorphism main > div > section:last-child form { border: none; border-radius: 1.75rem; background: #e0e5ec; box-shadow: 8px 8px 18px #b8c2d1, -8px -8px 18px #ffffff; }
        .theme-neumorphism main > div > section:last-child form label { color: #0f172a !important; font-weight: 700; }
        .theme-neumorphism main > div > section:last-child form p { color: #475569 !important; }
        .theme-neumorphism main > div > section:last-child form input { border: none; border-radius: 1rem; background: #e0e5ec; color: #0f172a; box-shadow: inset 3px 3px 6px #b8c2d1, inset -3px -3px 6px #ffffff; }
        .theme-neumorphism main > div > section:last-child form input::placeholder { color: #94a3b8; }
        .theme-neumorphism main > div > section:last-child form input:focus { outline: none; box-shadow: inset 4px 4px 8px #a3b1c6, inset -4px -4px 8px #ffffff, 0 0 0 2px #4f46e5; }
        .theme-neumorphism main > div > section:last-child form button { border: none; border-radius: 1.25rem; background: #4f46e5; color: #ffffff; box-shadow: 6px 6px 14px #b8c2d1, -6px -6px 14px #ffffff; transition: all 0.2s ease; }
        .theme-neumorphism main > div > section:last-child form button:hover { background: #4338ca; box-shadow: 4px 4px 10px #b8c2d1, -4px -4px 10px #ffffff; }
        .theme-neumorphism main > div > section:last-child form button:active { box-shadow: inset 3px 3px 6px #312e81, inset -3px -3px 6px #6366f1; }
        .theme-neumorphism main > div > section:last-child form .border-gray-100 { border-color: rgba(148,163,184,.3) !important; }
        .theme-neumorphism main > div > section:last-child form span.bg-blue-500 { background: #e0e5ec !important; color: #4f46e5 !important; box-shadow: 3px 3px 6px #b8c2d1, -3px -3px 6px #ffffff; }
        .theme-neumorphism .bg-white { background-color: #e0e5ec !important; }
        .theme-neumorphism .border-gray-200 { border: none !important; }
        .theme-neumorphism .text-gray-500,
        .theme-neumorphism .text-gray-400,
        .theme-neumorphism .text-gray-300 { color: #475569 !important; }
        .theme-neumorphism .text-gray-800,
        .theme-neumorphism .text-gray-900 { color: #0f172a !important; }
        .theme-neumorphism .bg-blue-50 { background: #e0e5ec !important; box-shadow: inset 3px 3px 6px #b8c2d1, inset -3px -3px 6px #ffffff !important; }
        .theme-neumorphism .text-blue-600 { color: #4f46e5 !important; }
        .theme-neumorphism .bg-green-50 { background: #e0e5ec !important; box-shadow: inset 3px 3px 6px #b8c2d1, inset -3px -3px 6px #ffffff !important; }
        .theme-neumorphism .text-green-800 { color: #15803d !important; }
        .theme-neumorphism .bg-amber-50 { background: #e0e5ec !important; box-shadow: inset 3px 3px 6px #b8c2d1, inset -3px -3px 6px #ffffff !important; }
        .theme-neumorphism .text-amber-800 { color: #b45309 !important; }
        .theme-cyberpunk { background: #05070d !important; color: #ffffff; }
        .theme-cyberpunk main > div { background: #020617; border: 1px solid #22d3ee; border-radius: 8px; box-shadow: 0 0 20px rgba(6,182,212,.2); }
        .theme-cyberpunk main > div > section:first-child, .theme-cyberpunk main > div > section:last-child { background: #020617; }
        .theme-cyberpunk main > div > section:first-child { border-right: 1px solid rgba(34,211,238,.2); }
        .theme-cyberpunk input { border: 1px solid #22d3ee; border-radius: 4px; background: #000; color: #fff; }
        .theme-cyberpunk button { border: 1px solid #facc15; border-radius: 4px; background: #facc15; color: #000; box-shadow: 0 0 12px rgba(250,204,21,.3); }
        .theme-maximalism { background: #34135c !important; }
        .theme-maximalism main > div { border: 4px solid #000; border-radius: 0; background: #f472b6; box-shadow: 12px 12px 0 #facc15; }
        .theme-maximalism main > div > section:first-child { background: #7e22ce; }
        .theme-maximalism main > div > section:last-child { background: #f472b6; }
        .theme-maximalism input { border: 3px solid #000; border-radius: 0; background: #fff; }
        .theme-maximalism button { border: 3px solid #000; border-radius: 0; background: #facc15; color: #000; }

        /* The canonical markup keeps all payment data identical; these selectors skin each structural surface. */
        .theme-neo-brutalism main > div > section:last-child > div:nth-child(2) { border: 3px solid #000; border-radius: 0; background: #cffafe; box-shadow: 5px 5px 0 #000; }
        .theme-neo-brutalism main > div > section:last-child > div:nth-child(2) p,
        .theme-neo-brutalism main > div > section:last-child > div:nth-child(2) div { color: #000; }
        .theme-neo-brutalism main > div > section:last-child > div:nth-child(2) span { background: #67e8f9; color: #000; }
        .theme-neo-brutalism main > div > section:last-child form > div:first-child { border-color: #000; }
        .theme-neo-brutalism main > div > section:last-child form label,
        .theme-neo-brutalism main > div > section:last-child form p { color: #000; }
        .theme-neo-brutalism main > div > section:last-child form button { color: #fff; }
        .theme-glassmorphism main > div > section:last-child > div:nth-child(2) { border-color: rgba(255,255,255,.3); border-radius: 1.25rem; background: rgba(255,255,255,.15); box-shadow: none; backdrop-filter: blur(16px); }
        .theme-glassmorphism main > div > section:last-child > div:nth-child(2) p,
        .theme-glassmorphism main > div > section:last-child > div:nth-child(2) div { color: #fff; }
        .theme-glassmorphism main > div > section:last-child form label,
        .theme-glassmorphism main > div > section:last-child form p,
        .theme-glassmorphism main > div > section:last-child > div:first-child p,
        .theme-glassmorphism main > div > section:last-child > div:first-child h2 { color: #ffffff; }
        .theme-glassmorphism main > div > section:last-child form input { border-color: rgba(255,255,255,.3); background: rgba(255,255,255,.12); color: #fff; }
        .theme-glassmorphism .bg-white { background-color: rgba(255,255,255,.15) !important; }
        .theme-glassmorphism .border-gray-200 { border-color: rgba(255,255,255,.25) !important; }
        .theme-glassmorphism .text-gray-500,
        .theme-glassmorphism .text-gray-400,
        .theme-glassmorphism .text-gray-300 { color: #cbd5e1 !important; }
        .theme-glassmorphism .text-gray-800,
        .theme-glassmorphism .text-gray-900 { color: #ffffff !important; }
        .theme-glassmorphism .bg-blue-50 { background-color: rgba(59,130,246,.2) !important; }
        .theme-glassmorphism .text-blue-600 { color: #93c5fd !important; }
        .theme-glassmorphism .bg-green-50 { background-color: rgba(34,197,94,.2) !important; }
        .theme-glassmorphism .text-green-800 { color: #bbf7d0 !important; }
        .theme-glassmorphism .bg-amber-50 { background-color: rgba(251,191,36,.2) !important; }
        .theme-glassmorphism .text-amber-800 { color: #fef08a !important; }
        .theme-neumorphism main > div > section:last-child > div:nth-child(2) { border-color: #94a3b8; border-radius: 1.5rem; background: #ffffff; box-shadow: inset 2px 2px 8px rgba(15,23,42,.05), 4px 4px 12px rgba(15,23,42,.08); }
        .theme-neumorphism main > div > section:last-child > div:nth-child(2) p,
        .theme-neumorphism main > div > section:last-child > div:nth-child(2) div { color: #0f172a; }
        .theme-neumorphism main > div > section:last-child form { border-color: #94a3b8; background: #ffffff; box-shadow: inset 2px 2px 8px rgba(15,23,42,.05), 6px 6px 16px rgba(15,23,42,.1); }
        .theme-neumorphism main > div > section:last-child form label { color: #0f172a !important; font-weight: 700; }
        .theme-neumorphism main > div > section:last-child form p { color: #334155 !important; }
        .theme-neumorphism main > div > section:last-child form input { border-color: #94a3b8; border-radius: 1rem; background: #f1f5f9; color: #0f172a; }
        .theme-neumorphism main > div > section:last-child form input::placeholder { color: #94a3b8; }
        .theme-neumorphism main > div > section:last-child form input:focus { border-color: #4f46e5; background: #ffffff; }
        .theme-neumorphism main > div > section:last-child form button { border-radius: 1rem; background: #4f46e5; color: #fff; box-shadow: 4px 4px 12px rgba(79,70,229,.3); }
        .theme-neumorphism main > div > section:last-child form .border-gray-100 { border-color: #cbd5e1 !important; }
        .theme-neumorphism main > div > section:last-child > div:first-child p,
        .theme-neumorphism main > div > section:last-child > div:first-child h2 { color: #0f172a; }
        .theme-neumorphism .bg-white { background-color: #ffffff !important; }
        .theme-neumorphism .border-gray-200 { border-color: #cbd5e1 !important; }
        .theme-neumorphism .text-gray-500,
        .theme-neumorphism .text-gray-400,
        .theme-neumorphism .text-gray-300 { color: #475569 !important; }
        .theme-neumorphism .text-gray-800,
        .theme-neumorphism .text-gray-900 { color: #0f172a !important; }
        .theme-neumorphism .bg-blue-50 { background-color: #dbeafe !important; }
        .theme-neumorphism .text-blue-600 { color: #2563eb !important; }
        .theme-neumorphism .bg-green-50 { background-color: #dcfce7 !important; }
        .theme-neumorphism .text-green-800 { color: #166534 !important; }
        .theme-neumorphism .bg-amber-50 { background-color: #fef3c7 !important; }
        .theme-neumorphism .text-amber-800 { color: #92400e !important; }
        .theme-cyberpunk main > div > section:last-child > div:nth-child(2) { border: 1px solid rgba(34,211,238,.4); border-radius: 8px; background: #05070d; box-shadow: 0 0 12px rgba(6,182,212,.1); }
        .theme-cyberpunk main > div > section:last-child > div:nth-child(2) p,
        .theme-cyberpunk main > div > section:last-child > div:nth-child(2) div { color: #ffffff; }
        .theme-cyberpunk main > div > section:last-child > div:nth-child(2) span { background: transparent; color: #facc15; }
        .theme-cyberpunk main > div > section:last-child form label,
        .theme-cyberpunk main > div > section:last-child form p,
        .theme-cyberpunk main > div > section:last-child > div:first-child p,
        .theme-cyberpunk main > div > section:last-child > div:first-child h2 { color: #67e8f9; }
        .theme-cyberpunk .text-gray-500,
        .theme-cyberpunk .text-gray-400,
        .theme-cyberpunk .text-gray-300 { color: #94a3b8 !important; }
        .theme-cyberpunk .text-gray-800,
        .theme-cyberpunk .text-gray-900 { color: #ffffff !important; }
        .theme-cyberpunk .bg-white { background-color: #020617 !important; }
        .theme-cyberpunk .border-gray-200 { border-color: rgba(34,211,238,.2) !important; }
        .theme-cyberpunk .bg-blue-50 { background-color: rgba(6,182,212,.1) !important; }
        .theme-cyberpunk .text-blue-600 { color: #22d3ee !important; }
        .theme-cyberpunk .bg-green-50 { background-color: rgba(22,163,74,.1) !important; }
        .theme-cyberpunk .text-green-800 { color: #bbf7d0 !important; }
        .theme-cyberpunk .border-green-200 { border-color: rgba(34,211,238,.3) !important; }
        .theme-cyberpunk .bg-amber-50 { background-color: rgba(251,191,36,.1) !important; }
        .theme-cyberpunk .text-amber-800 { color: #fef08a !important; }
        .theme-maximalism main > div > section:last-child > div:nth-child(2) { border: 4px solid #000; border-radius: 0; background: #fff; box-shadow: 6px 6px 0 #000; }
        .theme-maximalism main > div > section:last-child > div:nth-child(2) p,
        .theme-maximalism main > div > section:last-child > div:nth-child(2) div { color: #000; }
        .theme-maximalism main > div > section:last-child form label,
        .theme-maximalism main > div > section:last-child form p,
        .theme-maximalism main > div > section:last-child > div:first-child p,
        .theme-maximalism main > div > section:last-child > div:first-child h2 { color: #000; }
    </style>
</head>
<body class="theme-{{ $theme }} min-h-screen bg-gray-50 text-gray-900 antialiased">
    <main class="mx-auto flex min-h-screen w-full max-w-7xl items-center px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
        <div class="grid w-full overflow-hidden rounded-2xl bg-white shadow-2xl shadow-gray-900/10 ring-1 ring-gray-200 lg:grid-cols-[0.92fr_1.08fr]">
            <section class="relative overflow-hidden bg-gray-900 px-7 py-8 text-white sm:px-12 sm:py-10 lg:px-14 lg:py-12">
                <div class="absolute -right-20 -top-24 h-72 w-72 rounded-full border-[32px] border-blue-500/10"></div>
                <div class="absolute -bottom-24 -left-20 h-64 w-64 rounded-full bg-blue-500/10 blur-3xl"></div>
                <div class="relative flex h-full flex-col">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-sm font-semibold tracking-wide text-gray-200">
                        {{ config('app.name') }}
                    </a>
                    <div class="mt-16 max-w-lg lg:mt-24">
                        <p class="text-xs font-bold uppercase tracking-[0.24em] text-blue-400">{{ __('subbase-payment::subbase-payment/frontend.checkout.selected_plan') }}</p>
                        <h1 class="mt-4 text-4xl font-semibold tracking-tight sm:text-5xl">{{ $plan->name }}</h1>
                    @if($plan->description)
                        <p class="mt-5 max-w-md text-base leading-7 text-gray-300">{{ $plan->description }}</p>
                    @endif
                    <div class="mt-10 border-t border-white/10 pt-6">
                        @foreach($plan->features as $feature)
                            <div class="flex items-start gap-3 py-2.5 text-sm text-gray-200">
                                <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full bg-blue-50 text-xs font-bold text-blue-600">&#10003;</span>
                                <span>{{ $feature->name }}</span>
                            </div>
                        @endforeach
                    </div>
                    </div>
                    <p class="mt-auto pt-12 text-xs text-gray-400">{{ __('subbase-payment::subbase-payment/frontend.checkout.secure_tagline') }}</p>
                </div>
            </section>

            <section class="bg-white px-5 py-6 sm:px-10 sm:py-10 lg:px-14 lg:py-12">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-gray-500">{{ __('subbase-payment::subbase-payment/frontend.checkout.title') }}</p>
                        <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">{{ __('subbase-payment::subbase-payment/frontend.checkout.header_title') }}</h2>
                    </div>
                    <div class="hidden rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-600 ring-1 ring-blue-100 sm:block">{{ __('subbase-payment::subbase-payment/frontend.checkout.step_indicator') }}</div>
                </div>

                <div class="mt-8 flex items-center justify-between rounded-2xl border border-gray-200 bg-white px-5 py-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        @if($driverLogo)
                            <img src="{{ $driverLogo }}" alt="{{ $driverName }}" class="h-8 w-auto rounded-lg bg-white p-1" />
                        @else
                            <span class="grid h-8 w-8 place-items-center rounded-lg bg-blue-50 text-sm font-bold text-blue-600">{{ substr($driverName, 0, 1) }}</span>
                        @endif
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $plan->name }}</p>
                            <p class="mt-1 text-xs text-gray-500">{{ __('subbase-payment::subbase-payment/frontend.checkout.billed_via', ['driver' => $driverName]) }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xl font-bold tracking-tight text-gray-900">{{ $pricing['final_price'] }}</p>
                    </div>
                </div>

                @if(isset($subscriptionAction) && $subscriptionAction === 'active')
                    <div class="mt-4 flex items-center gap-2.5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-xs text-green-800">
                        <span class="rounded-md bg-green-600 px-2 py-0.5 font-bold text-white uppercase tracking-wider text-[10px]">{{ __('subbase-payment::subbase-payment/frontend.checkout.active_badge') }}</span>
                        <span>{{ __('subbase-payment::subbase-payment/frontend.checkout.active_notice') }}</span>
                    </div>
                @elseif(isset($subscriptionAction) && $subscriptionAction === 'renew')
                    <div class="mt-4 flex items-center gap-2.5 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-xs text-blue-800">
                        <span class="rounded-md bg-blue-600 px-2 py-0.5 font-bold text-white uppercase tracking-wider text-[10px]">{{ __('subbase-payment::subbase-payment/frontend.checkout.renewal_badge') }}</span>
                        <span>{{ __('subbase-payment::subbase-payment/frontend.checkout.renewal_notice') }}</span>
                    </div>
                @elseif(isset($subscriptionAction) && $subscriptionAction === 'switch')
                    <div class="mt-4 flex items-center gap-2.5 rounded-xl border border-purple-200 bg-purple-50 px-4 py-3 text-xs text-purple-800">
                        <span class="rounded-md bg-purple-600 px-2 py-0.5 font-bold text-white uppercase tracking-wider text-[10px]">{{ __('subbase-payment::subbase-payment/frontend.checkout.switch_badge') }}</span>
                        <span>{{ __('subbase-payment::subbase-payment/frontend.checkout.switch_notice') }}</span>
                    </div>
                @endif

                @if($errors->has('payment'))
                    <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800" role="alert">
                        {{ $errors->first('payment') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('subbase-payment.checkout.store', $plan->slug) }}"@unless($isFreePlan) target="subbase_payment_popup" onsubmit="window.open('about:blank', 'subbase_payment_popup', 'width=580,height=700,top=' + Math.max(0, (screen.height - 700) / 2) + ',left=' + Math.max(0, (screen.width - 580) / 2) + ',resizable=yes,scrollbars=yes');"@endunless class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-lg shadow-gray-900/5 sm:p-7">
                    @csrf
                    <div class="mb-6 flex items-center gap-3 border-b border-gray-100 pb-5">
                        <span class="grid h-9 w-9 place-items-center rounded-full bg-blue-500 text-sm font-bold text-white">1</span>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ __('subbase-payment::subbase-payment/frontend.checkout.step_1_title') }}</p>
                            <p class="mt-0.5 text-xs text-gray-500">{{ __('subbase-payment::subbase-payment/frontend.checkout.step_1_subtitle') }}</p>
                        </div>
                    </div>
                    <div class="space-y-5">
                        <div>
                            <label for="name" class="text-sm font-semibold text-gray-800">{{ __('subbase-payment::subbase-payment/frontend.checkout.full_name') }}</label>
                            <input id="name" name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="Jane Smith" class="mt-2 block w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3.5 text-sm shadow-sm outline-none transition placeholder:text-gray-400 focus:border-blue-500 focus:bg-white focus:ring-blue-100" />
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="email" class="text-sm font-semibold text-gray-800">{{ __('subbase-payment::subbase-payment/frontend.checkout.email_address') }}</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="jane@example.com" class="mt-2 block w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3.5 text-sm shadow-sm outline-none transition placeholder:text-gray-400 focus:border-blue-500 focus:bg-white focus:ring-blue-100" />
                        @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="mt-7 border-t border-gray-100 pt-6">
                        <button type="submit" @disabled(isset($subscriptionAction) && $subscriptionAction === 'active') aria-disabled="{{ isset($subscriptionAction) && $subscriptionAction === 'active' ? 'true' : 'false' }}" class="flex w-full items-center justify-center gap-3 rounded-xl px-4 py-4 text-sm font-bold text-white shadow-lg transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ isset($subscriptionAction) && $subscriptionAction === 'active' ? 'cursor-not-allowed bg-gray-400 shadow-gray-900/5' : 'bg-gray-900 shadow-gray-900/15 hover:bg-gray-700' }}">
                            {{ $isFreePlan ? __('subbase-payment::subbase-payment/frontend.checkout.subscribe') : __('subbase-payment::subbase-payment/frontend.checkout.continue_to_payment') }}
                            <span aria-hidden="true" class="text-lg leading-none">&#8594;</span>
                        </button>
                        <p class="mt-4 text-center text-xs leading-5 text-gray-500">{{ $isFreePlan ? __('subbase-payment::subbase-payment/frontend.checkout.free_plan_notice') : __('subbase-payment::subbase-payment/frontend.checkout.redirect_notice') }}</p>
                    </div>
                </form>
                <div class="mt-6 flex items-center justify-center gap-2 text-xs font-medium text-gray-500">
                    <span class="text-blue-600">&#10003;</span>
                    {{ __('subbase-payment::subbase-payment/frontend.checkout.secure_checkout') }}
                    <span class="text-gray-300">|</span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    {{ __('subbase-payment::subbase-payment/frontend.checkout.info_protected') }}
                </div>
            </section>
        </div>
    </main>
</body>
</html>