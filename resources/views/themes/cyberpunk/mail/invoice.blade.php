<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('subbase-payment::subbase-payment/frontend.invoice.subject', ['id' => $payment->transaction_id ?? $payment->id]) }}</title>
</head>
<body style="margin:0; padding:24px; background-color:#000000; font-family: 'Courier New', monospace; color:#facc15;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width:560px; background-color:#020617; border:2px solid #22d3ee; padding:32px;">
                    <tr>
                        <td style="border-bottom:2px solid #22d3ee; padding-bottom:16px;">
                            <h1 style="margin:0; font-size:20px; text-transform:uppercase; color:#facc15;">SYS://{{ config('app.name') }} RECEIPT</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px 0;">
                            <p style="margin:0 0 8px 0; font-size:12px; color:#22d3ee;"><strong>// USER:</strong> {{ $payment->customer_name }} ({{ $payment->customer_email }})</p>
                            <div style="background-color:#000000; border:2px solid #facc15; padding:16px; margin:16px 0;">
                                <p style="margin:0; font-size:16px; font-weight:bold; color:#22d3ee;">{{ $payment->plan_name ?? 'Subscription' }}</p>
                                <p style="margin:8px 0 0 0; font-size:24px; font-weight:bold; color:#ffffff;">{{ $payment->currency }} {{ number_format((float)$payment->amount, 2) }}</p>
                            </div>
                            <p style="margin:12px 0 0 0; font-size:12px; color:#94a3b8;"><strong>// TRANSACTION:</strong> {{ $payment->gateway_transaction_id ?? $payment->id }}</p>
                            @if(!empty($planFeatures))
                                <p style="margin:20px 0 8px 0; font-size:11px; text-transform:uppercase; letter-spacing:.15em; color:#facc15;">// INCLUDED_FEATURES</p>
                                @foreach($planFeatures as $feature)
                                    <p style="margin:4px 0; font-size:13px; color:#67e8f9;">&gt;&gt; {{ is_array($feature) ? ($feature['name'] ?? '') : ($feature->name ?? $feature) }}</p>
                                @endforeach
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="border-top:2px solid #22d3ee; padding-top:24px;">
                            <a href="{{ url('/') }}" style="display:inline-block; background-color:#facc15; color:#000000; font-size:14px; font-weight:bold; text-transform:uppercase; text-decoration:none; padding:12px 24px;">
                                {{ __('subbase-payment::subbase-payment/frontend.invoice.go_to_dashboard') }}
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>