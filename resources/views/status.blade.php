<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment {{ ucfirst($status) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="grid min-h-screen place-items-center bg-slate-950 px-6 text-white">
    <main class="w-full max-w-md rounded-3xl bg-white p-8 text-center text-slate-950 shadow-2xl sm:p-12">
        <div class="mx-auto grid h-14 w-14 place-items-center rounded-full {{ $status === 'pending' ? 'bg-cyan-100 text-cyan-700' : 'bg-slate-100 text-slate-500' }} text-xl">
            {{ $status === 'pending' ? '&#10003;' : '&#8592;' }}
        </div>
        <h1 class="mt-6 text-2xl font-semibold">{{ $status === 'pending' ? 'Payment received' : 'Payment canceled' }}</h1>
        <p class="mt-3 text-sm leading-6 text-slate-500">
            {{ $status === 'pending' ? 'Your payment is pending verification. Your subscription will activate only after confirmation.' : 'No subscription was activated.' }}
        </p>
        <a href="{{ url('/') }}" class="mt-8 inline-flex rounded-xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white hover:bg-cyan-700">Back to plans</a>
    </main>
</body>
</html>