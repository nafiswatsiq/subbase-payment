<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $plan->name }} | Checkout</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <main class="mx-auto flex min-h-screen w-full max-w-6xl items-center px-6 py-12 lg:px-8">
        <div class="grid w-full overflow-hidden rounded-3xl bg-white shadow-xl shadow-slate-200/70 ring-1 ring-slate-200 lg:grid-cols-[1.1fr_0.9fr]">
            <section class="bg-slate-950 px-7 py-10 text-white sm:px-12 lg:px-16 lg:py-14">
                <a href="{{ url('/') }}" class="text-sm font-semibold tracking-wide text-slate-300">{{ config('app.name') }}</a>
                <div class="mt-20 max-w-lg">
                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-cyan-300">Your plan</p>
                    <h1 class="mt-4 text-4xl font-semibold tracking-tight sm:text-5xl">{{ $plan->name }}</h1>
                    @if($plan->description)
                        <p class="mt-5 text-base leading-7 text-slate-300">{{ $plan->description }}</p>
                    @endif
                    <div class="mt-12 border-t border-white/10 pt-6">
                        @foreach($plan->features as $feature)
                            <div class="flex items-start gap-3 py-2 text-sm text-slate-200">
                                <span class="mt-0.5 text-cyan-300">&#10003;</span>
                                <span>{{ $feature->name }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="px-7 py-10 sm:px-12 lg:px-14 lg:py-14">
                <div class="flex items-end justify-between gap-4 border-b border-slate-200 pb-7">
                    <div>
                        <p class="text-sm text-slate-500">Complete your order</p>
                        <p class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">{{ $pricing['final_price'] }}</p>
                    </div>
                    <span class="pb-1 text-sm text-slate-500">{{ $currency }}</span>
                </div>

                @if($errors->has('payment'))
                    <div class="mt-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800" role="alert">
                        {{ $errors->first('payment') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('subbase-payment.checkout.store', $plan->slug) }}" class="mt-8 space-y-5">
                    @csrf
                    <div>
                        <label for="name" class="text-sm font-medium text-slate-700">Full name</label>
                        <input id="name" name="name" value="{{ old('name') }}" required autocomplete="name" class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm outline-none transition focus:border-cyan-500 focus:ring-cyan-500" />
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="text-sm font-medium text-slate-700">Email address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-sm shadow-sm outline-none transition focus:border-cyan-500 focus:ring-cyan-500" />
                        @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="w-full rounded-xl bg-slate-950 px-4 py-3.5 text-sm font-semibold text-white transition hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2">
                        Continue to payment
                    </button>
                </form>
                <p class="mt-6 text-center text-xs leading-5 text-slate-500">Secure checkout. You will be redirected to the selected payment provider.</p>
            </section>
        </div>
    </main>
</body>
</html>