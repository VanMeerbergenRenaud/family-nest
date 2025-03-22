<?php

namespace App\Livewire\Pages\Invoices;

use App\Livewire\Forms\InvoiceForm;
use Livewire\Component;
use Livewire\WithPagination;

class Archived extends Component
{
    use WithPagination;

    public InvoiceForm $form;

    public bool $showDeleteFormModal = false;

    public bool $deletedWithSuccess = false;

    public function mount(): void
    {

    }

    public function restoreInvoice($invoiceId): void
    {
        $invoice = auth()->user()->invoices()
            ->where('id', $invoiceId)
            ->where('is_archived', true)
            ->firstOrFail();

        $this->form->setFromInvoice($invoice);

        $this->form->restore();
    }

    public function showDeleteForm($id): void
    {
        $invoice = auth()->user()->invoices()->findOrFail($id);

        $this->form->setFromInvoice($invoice);

        $this->showDeleteFormModal = true;
    }

    public function deleteDefinitelyInvoice(): void
    {
        $this->form->delete();

        /*$this->showDeleteFormModal = false;
        $this->deletedWithSuccess = true;*/

        $this->js('window.location.reload()');
    }

    public function render()
    {
        $archivedInvoices = auth()->user()->invoices()
            ->where('is_archived', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.pages.invoices.archived', [
            'archivedInvoices' => $archivedInvoices,
        ])->layout('layouts.app-sidebar');
    }
}
