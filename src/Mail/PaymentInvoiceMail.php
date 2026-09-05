<?php

namespace Nafiswatsiq\SubbasePayment\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentInvoiceMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public object $payment,
        public array $planFeatures = []
    ) {}

    public function envelope(): Envelope
    {
        $appName = config('app.name', 'Subbase');
        $planName = $this->payment->plan_name ?? 'Subscription';

        return new Envelope(
            subject: "Invoice & Payment Receipt: {$planName} - {$appName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'subbase-payment::mail.invoice',
            with: [
                'payment' => $this->payment,
                'planFeatures' => $this->planFeatures,
            ],
        );
    }
}
