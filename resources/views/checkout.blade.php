<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $plan->name }} | Checkout</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
    <main class="mx-auto flex min-h-screen w-full max-w-7xl items-center px-4 py-6 sm:px-6 lg:px-8 lg:py-10">
        <div class="grid w-full overflow-hidden rounded-2xl bg-white shadow-2xl shadow-gray-900/10 ring-1 ring-gray-200 lg:grid-cols-[0.92fr_1.08fr]">
            <section class="relative overflow-hidden bg-gray-900 px-7 py-8 text-white sm:px-12 sm:py-10 lg:px-14 lg:py-12">
                <div class="absolute -right-20 -top-24 h-72 w-72 rounded-full border-[32px] border-blue-500/10"></div>
                <div class="absolute -bottom-24 -left-20 h-64 w-64 rounded-full bg-blue-500/10 blur-3xl"></div>
                <div class="relative flex h-full flex-col">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-sm font-semibold tracking-wide text-gray-200">
                        <span class="grid h-8 w-8 place-items-center rounded-lg bg-blue-500 text-sm font-black text-white">{{ substr(config('app.name'), 0, 1) }}</span>
                        {{ config('app.name') }}
                    </a>
                    <div class="mt-16 max-w-lg lg:mt-24">
                        <p class="text-xs font-bold uppercase tracking-[0.24em] text-blue-400">Selected plan</p>
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
                    <p class="mt-auto pt-12 text-xs text-gray-400">Secure payments. Fast checkout. No hidden fees.</p>
                </div>
            </section>

            <section class="bg-white px-5 py-6 sm:px-10 sm:py-10 lg:px-14 lg:py-12">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-gray-500">Checkout</p>
                        <h2 class="mt-2 text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">Complete your order</h2>
                    </div>
                    <div class="hidden rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-600 ring-1 ring-blue-100 sm:block">Step 1 of 2</div>
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
                            <p class="mt-1 text-xs text-gray-500">Billed securely via {{ $driverName }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xl font-bold tracking-tight text-gray-900">{{ $pricing['final_price'] }}</p>
                        <p class="mt-0.5 text-xs font-medium text-gray-500">{{ $currency }}</p>
                    </div>
                </div>

                @if($errors->has('payment'))
                    <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800" role="alert">
                        {{ $errors->first('payment') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('subbase-payment.checkout.store', $plan->slug) }}" target="subbase_payment_popup" onsubmit="window.open('about:blank', 'subbase_payment_popup', 'width=580,height=700,top=' + Math.max(0, (screen.height - 700) / 2) + ',left=' + Math.max(0, (screen.width - 580) / 2) + ',resizable=yes,scrollbars=yes');" class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-lg shadow-gray-900/5 sm:p-7">
                    @csrf
                    <div class="mb-6 flex items-center gap-3 border-b border-gray-100 pb-5">
                        <span class="grid h-9 w-9 place-items-center rounded-full bg-blue-500 text-sm font-bold text-white">1</span>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Your details</p>
                            <p class="mt-0.5 text-xs text-gray-500">Where should we send your payment receipt?</p>
                        </div>
                    </div>
                    <div class="space-y-5">
                        <div>
                            <label for="name" class="text-sm font-semibold text-gray-800">Full name</label>
                            <input id="name" name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="Jane Smith" class="mt-2 block w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3.5 text-sm shadow-sm outline-none transition placeholder:text-gray-400 focus:border-blue-500 focus:bg-white focus:ring-blue-100" />
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="email" class="text-sm font-semibold text-gray-800">Email address</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="jane@example.com" class="mt-2 block w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3.5 text-sm shadow-sm outline-none transition placeholder:text-gray-400 focus:border-blue-500 focus:bg-white focus:ring-blue-100" />
                        @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="mt-7 border-t border-gray-100 pt-6">
                        <button type="submit" class="flex w-full items-center justify-center gap-3 rounded-xl bg-gray-900 px-4 py-4 text-sm font-bold text-white shadow-lg shadow-gray-900/15 transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Continue to payment
                            <span aria-hidden="true" class="text-lg leading-none">&#8594;</span>
                        </button>
                        <p class="mt-4 text-center text-xs leading-5 text-gray-500">You will be redirected to the selected payment provider in a new window.</p>
                    </div>
                </form>
                <div class="mt-6 flex items-center justify-center gap-2 text-xs font-medium text-gray-500">
                    <span class="text-blue-600">&#10003;</span>
                    Secure checkout
                    <span class="text-gray-300">|</span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Your information is protected
                </div>
            </section>
        </div>
    </main>
</body>
</html>