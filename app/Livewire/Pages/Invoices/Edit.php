<?php

namespace App\Livewire\Pages\Invoices;

use App\Enums\InvoiceTypeEnum;
use App\Livewire\Forms\InvoiceForm;
use App\Models\Invoice;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;

    public InvoiceForm $form;

    public $isEditMode = true;

    public function mount($id)
    {
        $invoice = Invoice::findOrFail($id);
        $this->form->setFromInvoice($invoice);
    }

    public function update()
    {
        $invoice = $this->form->update();

        if ($invoice) {
            $this->redirectRoute('invoices.show', $invoice);
        } else {
            session()->flash('error', 'Une erreur est survenue lors de la mise à jour de la facture');
        }
    }

    public function render()
    {
        return view('livewire.pages.invoices.edit', [
            'invoiceTypes' => InvoiceTypeEnum::getTypesOptions(),
        ])->layout('layouts.app');
    }
}
