<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoicePaymentReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected Invoice $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function via(): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Rappel de paiement : '.$this->invoice->name)
            ->view(
                'emails.invoices.payment-reminder',
                [
                    'invoice' => $this->invoice,
                    'userName' => $notifiable->name,
                    'appName' => config('app.name'),
                ]
            );
    }

    public function toArray(): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'name' => $this->invoice->name,
            'amount' => $this->invoice->amount,
            'currency' => $this->invoice->symbol ?? '€',
            'payment_due_date' => $this->invoice->payment_due_date,
        ];
    }
}
