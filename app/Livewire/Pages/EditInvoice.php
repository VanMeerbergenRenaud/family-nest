<?php

namespace App\Livewire\Pages;

use App\Enums\InvoiceTypeEnum;
use App\Livewire\Forms\InvoiceForm;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditInvoice extends Component
{
    use WithFileUploads;

    public InvoiceForm $form;

    public $family_members = [];

    public $engagements = [];

    public $invoiceOriginalFilePath = null;

    public $fileWasRemoved = false;

    public $isEditMode = true;

    public function mount($id)
    {
        $invoice = $this->form->setInvoice($id); // Charge la facture dans le formulaire

        $this->invoiceOriginalFilePath = $invoice->file_path;

        $this->engagements = [
            ['id' => 'abc123', 'name' => 'Abonnement Internet Orange'],
        ];
    }

    public function updatedFormType()
    {
        $this->form->updateAvailableCategories();
        $this->form->category = null;
    }

    // Enregistre la facture depuis le formulaire InvoiceForm
    public function updateInvoice()
    {
        $this->validate();

        try {
            $updatedInvoice = $this->form->update();

            if ($updatedInvoice) {
                return redirect()->route('invoices');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage());
        }

        return false;
    }

    /**
     * Supprime le fichier uploadé (pour la prévisualisation)
     */
    public function removeUploadedFile()
    {
        // Si nous utilisons la nouvelle approche avec existingFilePath
        if (property_exists($this->form, 'existingFilePath')) {
            $this->form->existingFilePath = null;
        }

        // Réinitialiser la propriété uploadedFile
        $this->form->uploadedFile = null;
    }

    public function removeExistingFile()
    {
        $this->invoiceOriginalFilePath = null;
        $this->fileWasRemoved = true;
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

    public function render()
    {
        return view('livewire.pages.edit-invoice', [
            'invoiceTypes' => InvoiceTypeEnum::getTypesOptions(),
        ])->layout('layouts.app');
    }
}
