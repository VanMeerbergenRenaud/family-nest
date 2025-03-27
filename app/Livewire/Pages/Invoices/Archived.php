<?php

namespace App\Livewire\Pages\Invoices;

use App\Livewire\Forms\InvoiceForm;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

class Archived extends Component
{
    use WithPagination;

    public InvoiceForm $form;

    public bool $showDeleteFormModal = false;

    public bool $showDeleteAllInvoicesFormModal = false;

    public bool $showArchiveExempleModal = false;

    // 1. Restore
    public function restoreInvoice($invoiceId): void
    {
        $invoice = auth()->user()->invoices()
            ->where('id', $invoiceId)
            ->where('is_archived', true)
            ->firstOrFail();

        $this->form->setFromInvoice($invoice);

        $this->form->restore();

        Toaster::success('Facture restaurée avec succès !');

        $this->redirectRoute('invoices.archived');
    }

    // 2. Delete
    public function showDeleteForm($id): void
    {
        $invoice = auth()->user()->invoices()->findOrFail($id);

        $this->form->setFromInvoice($invoice);

        $this->showDeleteFormModal = true;
    }

    public function deleteDefinitelyInvoice(): void
    {
        $this->form->delete();

        $this->showDeleteFormModal = false;

        Toaster::success('Facture supprimée définitivement !');

        $this->redirectRoute('invoices.archived');
    }

    // 3. Delete all
    public function deleteAllInvoicesForm(): void
    {
        $this->showDeleteAllInvoicesFormModal = true;
    }

    public function deleteDefinitelyAllInvoice(): void
    {
        $archivedInvoices = auth()->user()->invoices()
            ->where('is_archived', true)
            ->get();

        foreach ($archivedInvoices as $invoice) {
            $this->form->setFromInvoice($invoice);
            $this->form->delete();
        }

        $this->showDeleteAllInvoicesFormModal = false;

        Toaster::success('Corbeille vidée avec succès !::Vous factures ont été supprimées définitivement.');

        $this->redirectRoute('invoices.archived');
    }

    // 4. Archive exemple
    public function showArchiveExemple(): void
    {
        $this->showArchiveExempleModal = true;
    }

    public function render()
    {
        $archivedInvoices = auth()->user()->invoices()
            ->where('is_archived', true)
            ->orderBy('created_at', 'desc')
            ->paginate(7);

        return view('livewire.pages.invoices.archived', [
            'archivedInvoices' => $archivedInvoices,
        ])->layout('layouts.app-sidebar');
    }
}
