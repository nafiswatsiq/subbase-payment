<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('subbase-payment::subbase-payment/frontend.invoice.subject', ['id' => $payment->transaction_id ?? $payment->id]) }}</title>
</head>
<body style="margin:0; padding:24px; background-color:#f1f5f9; font-family: system-ui, sans-serif; color:#334155;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width:560px; background-color:#f8fafc; border-radius:24px; border:2px solid #ffffff; box-shadow:0 20px 25px -5px rgba(0, 0, 0, 0.05); padding:32px;">
                    <tr>
                        <td style="border-bottom:1px solid #e2e8f0; padding-bottom:16px;">
                            <h1 style="margin:0; font-size:22px; color:#0f172a;">{{ config('app.name') }} RECEIPT</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px 0;">
                            <p style="margin:0 0 8px 0; font-size:14px; color:#64748b;"><strong>BILLED TO:</strong> {{ $payment->customer_name }} ({{ $payment->customer_email }})</p>
                            <div style="background-color:#e0e7ff; border-radius:16px; padding:20px; margin:16px 0;">
                                <p style="margin:0; font-size:16px; font-weight:bold; color:#4338ca;">{{ $payment->plan_name ?? 'Subscription' }}</p>
                                <p style="margin:8px 0 0 0; font-size:24px; font-weight:bold; color:#1e1b4b;">{{ $payment->currency }} {{ number_format((float)$payment->amount, 2) }}</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="border-top:1px solid #e2e8f0; padding-top:24px;">
                            <a href="{{ url('/') }}" style="display:inline-block; background-color:#4f46e5; color:#ffffff; border-radius:14px; font-size:14px; font-weight:bold; text-decoration:none; padding:12px 24px;">
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