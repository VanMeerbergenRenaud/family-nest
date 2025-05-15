<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatusEnum;
use App\Models\Invoice;
use App\Notifications\InvoicePaymentReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Command\Command as ReminderCommand;

class SendInvoiceReminders extends Command
{
    protected $signature = 'invoices:send-reminders';

    protected $description = 'Envoie des rappels de paiement pour les factures dont la date de rappel est aujourd\'hui';

    public function handle(): int
    {
        $today = now()->startOfDay();

        // Récupérer toutes les factures avec une date de rappel aujourd'hui et qui ne sont pas encore payées
        $invoices = Invoice::whereDate('payment_reminder', $today)
            ->whereNotIn('payment_status', [PaymentStatusEnum::Paid->value])
            ->with(['user', 'family'])
            ->get();

        $count = 0;

        // Envoyer la notification au propriétaire de la facture
        foreach ($invoices as $invoice) {
            if ($invoice->user) {
                $invoice->user->notify(new InvoicePaymentReminder($invoice));
                $count++;
            }
        }

        $this->info("$count rappels de paiement ont été envoyés.");
        Log::info("$count rappels de paiement ont été envoyés.");

        return ReminderCommand::SUCCESS;
    }
}
