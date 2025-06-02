<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 60;

    public array $backoff = [10, 30, 60];

    public function __construct(
        public Invoice $invoice,
        public User $user,
        public string $invoiceUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address'),
            replyTo: config('mail.support.address'),
            subject: "💰 Rappel de paiement : {$this->invoice->name}",
            tags: ['invoice-reminder'],
            metadata: [
                'invoice_id' => $this->invoice->id,
                'user_id' => $this->user->id,
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.views.invoice-reminder',
            with: [
                'invoice' => $this->invoice,
                'user' => $this->user,
                'invoiceUrl' => $this->invoiceUrl,
                'appName' => config('app.name'),
                'supportEmail' => config('mail.support.address', 'support@familynest.com'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error('Invoice reminder email failed', [
            'invoice_id' => $this->invoice->id,
            'email' => $this->user->email,
            'error' => $exception->getMessage(),
        ]);
    }
}
