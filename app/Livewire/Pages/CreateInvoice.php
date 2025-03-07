<?php

namespace App\Livewire\Pages;

use App\Enums\InvoiceTypeEnum;
use App\Livewire\Forms\InvoiceForm;
use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateInvoice extends Component
{
    use WithFileUploads;

    public InvoiceForm $form;

    public $family_members = [];

    public $engagements = [];

    public function mount()
    {
        $this->family_members = auth()->user()->get();

        $this->engagements = [
            ['id' => 'abc123', 'name' => 'Abonnement Internet Orange'],
        ];
    }

    public function updatedFormType()
    {
        $this->form->updateAvailableCategories();
        $this->form->category = null; // Réinitialiser la catégorie lorsque le type change
    }

    public function removeUploadedFile()
    {
        $this->form->uploadedFile = null;
        $this->resetValidation('form.uploadedFile');
    }

    public function addTag()
    {
        if (! empty($this->form->tagInput)) {
            $this->form->tags[] = $this->form->tagInput;
            $this->form->tagInput = '';
        }
    }

    public function removeTag($index)
    {
        unset($this->form->tags[$index]);
        $this->form->tags = array_values($this->form->tags); // Réindexer le tableau
    }

    public function createInvoice()
    {
        $invoice = $this->form->store();

        if ($invoice) {
            return Redirect::route('invoices');
        }
    }

    public function render()
    {
        return view('livewire.pages.create-invoice', [
            'invoiceTypes' => InvoiceTypeEnum::getTypesOptions(),
        ])->layout('layouts.app');
    }
}
