<?php

namespace App\Jobs;

use App\Mail\PaymentReminder;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPaymentReminder implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    public function __construct(public Invoice $invoice, public User $user) {}

    public function handle(): void
    {
        try {
            if (! $this->invoiceStillExists()) {
                Log::info('Invoice deleted before sending reminder email', [
                    'invoice_id' => $this->invoice->id,
                    'email' => $this->user->email,
                ]);

                return;
            }

            // Envoyer l'email
            Mail::to($this->user->email)
                ->send(new PaymentReminder(
                    $this->invoice,
                    $this->user,
                    route('invoices.show', ['invoice' => $this->invoice->id])
                ));

            Log::info('Invoice reminder sent successfully', [
                'invoice_id' => $this->invoice->id,
                'email' => $this->user->email,
                'invoice_name' => $this->invoice->name,
                'due_date' => $this->invoice->payment_due_date,
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du rappel : '.$e->getMessage());
        }
    }

    private function invoiceStillExists(): bool
    {
        return Invoice::where('id', $this->invoice->id)->exists();
    }
}
