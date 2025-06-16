<?php

namespace App\Services;

use App\Jobs\SendPaymentReminder;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class InvoiceReminderService
{
    /**
     * Planifie un rappel de paiement pour une facture.
     */
    public function scheduleReminder(Invoice $invoice): bool
    {
        try {
            if (! $invoice->payment_reminder) {
                return false;
            }

            // Si la date de rappel est déjà passée, ne rien faire
            if (Carbon::parse($invoice->payment_reminder)->isPast()) {
                Log::info("La date de rappel pour la facture #{$invoice->id} est déjà passée.");

                return false;
            }

            if ($invoice->user) {
                $delay = Carbon::parse($invoice->payment_reminder)->startOfDay();

                SendPaymentReminder::dispatch($invoice, $invoice->user)
                    ->delay($delay);

                Log::info("Rappel de paiement programmé pour la facture #{$invoice->id} le {$delay->format('Y-m-d')}");

                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la programmation du rappel de paiement : '.$e->getMessage());

            return false;
        }
    }
}
