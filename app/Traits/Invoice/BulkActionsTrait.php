<?php

namespace App\Traits\Invoice;

use App\Enums\PaymentStatusEnum;
use App\Models\Invoice;
use Masmerise\Toaster\Toaster;

trait BulkActionsTrait
{
    public array $selectedInvoiceIds = [];

    public array $invoiceIdsOnPage = [];

    public $selectedPaymentStatus = null;

    /**
     * Archiver toutes les factures sélectionnées
     */
    public function archiveSelected(): void
    {
        if (empty($this->selectedInvoiceIds)) {
            Toaster::error('Aucune facture sélectionnée.');

            return;
        }

        $user = auth()->user();

        // Récupérer toutes les factures sélectionnées
        $invoices = Invoice::whereIn('id', $this->selectedInvoiceIds)
            ->where('is_archived', false)
            ->get();

        if ($invoices->isEmpty()) {
            Toaster::error('Aucune facture sélectionnée ou déjà archivée.');

            return;
        }

        // Vérifier les permissions pour chaque facture
        $archivableInvoiceIds = [];
        foreach ($invoices as $invoice) {
            if ($user->can('archive', $invoice)) {
                $archivableInvoiceIds[] = $invoice->id;
            }
        }

        if (empty($archivableInvoiceIds)) {
            Toaster::error('Vous n\'avez pas la permission d\'archiver ces factures.');

            return;
        }

        try {
            $count = Invoice::whereIn('id', $archivableInvoiceIds)
                ->update([
                    'is_archived' => true,
                    'is_favorite' => false,
                ]);

            if ($count > 1) {
                Toaster::success("$count factures archivées avec succès.");
            } elseif ($count == 1) {
                Toaster::success('La facture a été archivée avec succès.');
            } else {
                Toaster::error('Aucune facture n\'a pu être archivée.');
            }

            $this->selectedInvoiceIds = [];
            $this->selectedPaymentStatus = null;
            $this->dispatch('invoices-bulk-archived', ids: $archivableInvoiceIds);
        } catch (\Exception $e) {
            Toaster::error('Erreur lors de l\'archivage: '.$e->getMessage());
        }
    }

    /**
     * Changer le statut de paiement des factures sélectionnées
     */
    public function markAsPaymentStatusSelected(): void
    {
        try {
            if (empty($this->selectedInvoiceIds)) {
                Toaster::error('Aucune facture sélectionnée.');

                return;
            }

            if ($this->selectedPaymentStatus === null) {
                Toaster::error('Veuillez sélectionner un statut de paiement.');

                return;
            }

            $invoices = Invoice::whereIn('id', $this->selectedInvoiceIds)
                ->where('is_archived', false)
                ->get();

            if ($invoices->isEmpty()) {
                Toaster::error('Aucune facture sélectionnée ou déjà archivée.');

                return;
            }

            // Vérifier les permissions
            $updatableInvoices = [];
            foreach ($invoices as $invoice) {
                if (auth()->user()->can('update', $invoice)) {
                    $updatableInvoices[] = $invoice;
                }
            }

            if (empty($updatableInvoices)) {
                Toaster::error('Vous n\'avez pas la permission de modifier ces factures.');

                return;
            }

            $count = count($updatableInvoices);
            $updatedIds = [];

            foreach ($updatableInvoices as $invoice) {
                $invoice->payment_status = $this->selectedPaymentStatus;
                $invoice->save();
                $updatedIds[] = $invoice->id;
            }

            $statusEnum = PaymentStatusEnum::from($this->selectedPaymentStatus);
            $statusLabel = $statusEnum->label();

            if ($count > 1) {
                Toaster::success("$count factures marquées comme \"$statusLabel\" avec succès.");
            } else {
                Toaster::success("La facture a été marquée comme \"$statusLabel\" avec succès.");
            }

            $this->selectedInvoiceIds = [];
            $this->selectedPaymentStatus = null;
            $this->dispatch('invoices-bulk-updated', ids: $updatedIds);
        } catch (\Exception $e) {
            Toaster::error('Erreur lors de la modification: '.$e->getMessage());
        }
    }
}
