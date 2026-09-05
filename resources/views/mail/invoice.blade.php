<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice Payment #{{ $payment->transaction_id ?? $payment->id }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f9fafb; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color:#111827; -webkit-font-smoothing:antialiased;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#f9fafb; padding: 32px 16px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width:576px; background-color:#ffffff; border-radius:16px; overflow:hidden; border:1px solid #e5e7eb; box-shadow:0 10px 15px -3px rgba(0,0,0,0.05);">
                    <!-- Header -->
                    <tr>
                        <td style="background-color:#0f172a; padding: 32px; color:#ffffff; position:relative;">
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td>
                                        <div style="display:inline-flex; align-items:center; gap:8px; font-size:14px; font-weight:700; letter-spacing:0.025em; color:#ffffff;">
                                            <span style="margin-left:8px;">{{ config('app.name') }}</span>
                                        </div>
                                    </td>
                                    <td align="right">
                                        <span style="background-color:rgba(59,130,246,0.2); color:#60a5fa; font-size:12px; font-weight:700; padding:6px 12px; border-radius:9999px; text-transform:uppercase; letter-spacing:0.1em;">INVOICE PAID</span>
                                    </td>
                                </tr>
                            </table>
                            <div style="margin-top:32px;">
                                <p style="margin:0; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.2em; color:#60a5fa;">Payment Receipt</p>
                                <h1 style="margin:8px 0 0 0; font-size:28px; font-weight:700; tracking-tight: -0.025em; color:#ffffff;">Thank you for your payment</h1>
                            </div>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding:32px;">
                            <!-- Customer Details -->
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom:24px;">
                                <tr>
                                    <td style="font-size:14px; color:#4b5563;">
                                        <p style="margin:0 0 4px 0;"><strong>Billed To:</strong> {{ $payment->customer_name }}</p>
                                        <p style="margin:0 0 4px 0;"><strong>Email:</strong> {{ $payment->customer_email }}</p>
                                        <p style="margin:0;"><strong>Date:</strong> {{ $payment->verified_at ? (\Carbon\Carbon::parse($payment->verified_at)->format('M d, Y H:i T')) : now()->format('M d, Y H:i T') }}</p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Order Summary Box -->
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="border:1px solid #e5e7eb; border-radius:12px; background-color:#ffffff; margin-bottom:24px; border-collapse:separate;">
                                <tr>
                                    <td style="padding:16px; border-bottom:1px solid #f3f4f6;">
                                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                            <tr>
                                                <td>
                                                    <p style="margin:0; font-size:14px; font-weight:600; color:#111827;">{{ $payment->plan_name ?? 'Subscription Plan' }}</p>
                                                    <p style="margin:4px 0 0 0; font-size:12px; color:#6b7280;">Gateway: {{ ucfirst($payment->gateway_driver) }}</p>
                                                </td>
                                                <td align="right" valign="top">
                                                    <p style="margin:0; font-size:18px; font-weight:700; color:#111827;">{{ $payment->currency }} {{ number_format((float)$payment->amount, 2) }}</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 16px; background-color:#f9fafb; border-bottom-left-radius:12px; border-bottom-right-radius:12px; font-size:12px; color:#6b7280;">
                                        <span style="font-weight:600; color:#374151;">Transaction ID:</span> {{ $payment->gateway_transaction_id ?? $payment->id }}
                                    </td>
                                </tr>
                            </table>

                            <!-- Plan Features -->
                            @if(!empty($planFeatures))
                            <div style="margin-bottom:24px; border-top:1px solid #f3f4f6; padding-top:20px;">
                                <p style="margin:0 0 12px 0; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:#6b7280;">Included Features</p>
                                @foreach($planFeatures as $feature)
                                    <div style="font-size:14px; color:#374151; padding:4px 0;">
                                        <span style="color:#2563eb; font-weight:bold;">&#10003;</span> {{ is_array($feature) ? ($feature['name'] ?? '') : ($feature->name ?? $feature) }}
                                    </div>
                                @endforeach
                            </div>
                            @endif

                            <!-- Action / Note -->
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-top:24px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ url('/') }}" style="display:inline-block; background-color:#0f172a; color:#ffffff; font-size:14px; font-weight:700; text-decoration:none; padding:12px 24px; border-radius:10px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);">
                                            Go to Dashboard &rarr;
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#f9fafb; padding:20px 32px; border-top:1px solid #f3f4f6; text-align:center; font-size:12px; color:#9ca3af;">
                            <p style="margin:0 0 4px 0;">Secure payment receipt powered by {{ config('app.name') }}</p>
                            <p style="margin:0;">If you have any questions, please contact support.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
