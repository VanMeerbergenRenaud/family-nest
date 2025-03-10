<?php

namespace App\Livewire\Pages\Invoices;

use Livewire\Component;
use Livewire\WithPagination;

class Archived extends Component
{
    use WithPagination;

    public function restoreInvoice($invoiceId)
    {
        // Restauration de la facture
        dd('Restauration de la facture : '.$invoiceId);
    }

    public function deleteDefinitelyInvoice($invoiceId)
    {
        // Suppression définitive de la facture
        dd('Suppression définitive de la facture : '.$invoiceId);
    }

    public function render()
    {
        $archivedInvoices = auth()->user()->invoices()
            ->where('is_archived', true)
            ->latest()
            ->paginate(10);

        // Pour récupérer aussi les factures soft-deleted
        // $archivedInvoicesAll = auth()->user()->invoices()
        //  ->withTrashed()
        //  ->whereNotNull('deleted_at')
        //  ->latest()
        //  ->paginate(10);

        return view('livewire.pages.invoices.archived', [
            'archivedInvoices' => $archivedInvoices,
        ])->layout('layouts.app-sidebar');
    }
}
