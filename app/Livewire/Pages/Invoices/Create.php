<?php

namespace App\Livewire\Pages\Invoices;

use App\Enums\InvoiceTypeEnum;
use App\Livewire\Forms\InvoiceForm;
use App\Traits\InvoiceTagManagement;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use InvoiceTagManagement;
    use WithFileUploads;

    public InvoiceForm $form;

    public $family_members = [];

    public function mount()
    {
        $this->family_members = auth()->user()->get();

        // Initialiser le tableau des tags
        $this->initializeTagManagement();

        // Initialiser le tableau des membres associés
        if (! is_array($this->form->associated_members)) {
            $this->form->associated_members = [];
        }
    }

    public function updatedFormType()
    {
        $this->form->updateAvailableCategories();
        $this->form->category = null; // Réinitialiser la catégorie lorsque le type change
    }

    /**
     * Méthode pour mettre à jour les montants partagés
     */
    public function updateShareAmounts()
    {
        // Logique pour mettre à jour les montants partagés
    }

    /**
     * Méthode pour mettre à jour les pourcentages
     */
    public function updateShareFromPercentage($percentage, $member)
    {
        // Logique pour mettre à jour les pourcentages
    }

    /**
     * Supprime le fichier uploadé
     */
    public function removeUploadedFile()
    {
        $this->form->removeFile();
        $this->form->resetErrorBag('uploadedFile');
    }

    /**
     * Crée une nouvelle facture
     */
    public function createInvoice()
    {
        $invoice = $this->form->store();

        if ($invoice) {
            $this->redirectRoute('invoices.index', $invoice);
        } else {
            session()->flash('error', 'Une erreur est survenue lors de la création de la facture');
        }
    }

    /**
     * Rend la vue
     */
    public function render()
    {
        return view('livewire.pages.invoices.create', [
            'invoiceTypes' => InvoiceTypeEnum::getTypesOptions(),
        ])->layout('layouts.app');
    }
}
