<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('subbase-payment::subbase-payment/frontend.invoice.subject', ['id' => $payment->transaction_id ?? $payment->id]) }}</title>
</head>
<body style="margin:0; padding:24px; background-color:#581c87; font-family: Impact, sans-serif; color:#000000;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width:560px; background-color:#f472b6; border:4px solid #000000; box-shadow:10px 10px 0px 0px #facc15; padding:32px;">
                    <tr>
                        <td style="border-bottom:4px solid #000000; padding-bottom:16px;">
                            <h1 style="margin:0; font-size:26px; text-transform:uppercase; color:#000000;">★ {{ config('app.name') }} RECEIPT ★</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px 0;">
                            <p style="margin:0 0 8px 0; font-size:14px;"><strong>BILLED TO:</strong> {{ $payment->customer_name }} ({{ $payment->customer_email }})</p>
                            <div style="background-color:#facc15; border:4px solid #000000; padding:20px; margin:16px 0; box-shadow:5px 5px 0px 0px #000000;">
                                <p style="margin:0; font-size:20px; text-transform:uppercase;">{{ $payment->plan_name ?? 'Subscription' }}</p>
                                <p style="margin:8px 0 0 0; font-size:28px;">{{ $payment->currency }} {{ number_format((float)$payment->amount, 2) }}</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="border-top:4px solid #000000; padding-top:24px;">
                            <a href="{{ url('/') }}" style="display:inline-block; background-color:#34d399; color:#000000; border:4px solid #000000; font-size:16px; font-weight:bold; text-transform:uppercase; text-decoration:none; padding:12px 24px; box-shadow:4px 4px 0px 0px #000000;">
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