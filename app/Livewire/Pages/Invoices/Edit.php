<?php

namespace App\Livewire\Pages\Invoices;

use App\Enums\InvoiceTypeEnum;
use App\Livewire\Forms\InvoiceForm;
use App\Models\Invoice;
use App\Traits\InvoiceTagManagement;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use InvoiceTagManagement;
    use WithFileUploads;

    public InvoiceForm $form;

    public Invoice $invoice;

    public $isEditMode = true;

    public $family_members = [];

    public function mount($id)
    {
        $this->invoice = auth()->user()->invoices()
            ->with('file')
            ->findOrFail($id);

        $this->form->setFromInvoice($this->invoice);

        // Charger les membres de famille
        $this->family_members = auth()->user()->get();

        // Initialiser les tags
        $this->initializeTagManagement();

        // Initialiser les membres associés
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
     * Met à jour la facture existante
     */
    public function updateInvoice()
    {
        $invoice = $this->form->update();

        if ($invoice) {
            $this->redirectRoute('invoices.index', $invoice);
        } else {
            session()->flash('error', 'Une erreur est survenue lors de la mise à jour de la facture');
        }
    }

    /**
     * Rend la vue
     */
    public function render()
    {
        return view('livewire.pages.invoices.edit', [
            'invoiceTypes' => InvoiceTypeEnum::getTypesOptions(),
        ])->layout('layouts.app');
    }
}
