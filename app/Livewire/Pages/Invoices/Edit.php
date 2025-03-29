<?php

namespace App\Livewire\Pages\Invoices;

use App\Enums\InvoiceTypeEnum;
use App\Livewire\Forms\InvoiceForm;
use App\Models\Invoice;
use App\Services\FileStorageService;
use App\Traits\InvoiceFileUrlTrait;
use App\Traits\InvoiceShareCalculationTrait;
use App\Traits\InvoiceTagManagement;
use Livewire\Component;
use Livewire\WithFileUploads;
use Masmerise\Toaster\Toaster;

class Edit extends Component
{
    use InvoiceFileUrlTrait;
    use InvoiceShareCalculationTrait;
    use InvoiceTagManagement;
    use WithFileUploads;

    public InvoiceForm $form;

    public Invoice $invoice;

    public $isEditMode = true;

    public $family_members = [];

    public $filePath = null;

    public $fileExtension = null;

    public $fileName = null;

    public $fileExists = false;

    public function mount($id)
    {
        $this->invoice = auth()->user()->invoices()
            ->with('file')
            ->findOrFail($id);

        // Initialiser le formulaire avec les données de la facture
        $this->form->setFromInvoice($this->invoice);

        // Générer l'URL du fichier
        $fileInfo = $this->generateInvoiceFileUrl($this->invoice);

        $this->filePath = $fileInfo['url'];
        $this->fileExtension = $fileInfo['extension'];
        $this->fileExists = $fileInfo['exists'];
        $this->fileName = $this->invoice->file->file_name ?? null;

        // Récupérer les membres de la famille
        $this->prepareFamilyMembers();

        // Initialiser le tableau des tags et le mode de partage
        $this->initializeTagManagement();
        $this->calculateRemainingShares();
    }

    private function prepareFamilyMembers(): void
    {
        // Récupérer la famille de l'utilisateur
        $family = auth()->user()->family();
        $this->family_members = collect();

        if ($family) {
            // Récupérer tous les membres de la famille sauf l'utilisateur courant
            $this->family_members = $family->users()
                ->where('users.id', '!=', auth()->id())
                ->get();
        }

        // Ajouter l'utilisateur actuel au début de la liste
        $this->family_members->prepend(auth()->user());
    }

    /**
     * Réagir aux changements du type de facture
     */
    public function updatedFormType()
    {
        $this->form->updateAvailableCategories();
        $this->form->category = null; // Réinitialiser la catégorie lorsque le type change
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
     * Réagir aux changements du champ montant
     */
    public function updatedFormAmount()
    {
        $this->calculateRemainingShares();
    }

    /**
     * Réagir aux changements du mode de partage
     */
    public function updatedShareMode()
    {
        $this->calculateRemainingShares();
    }

    /**
     * Met à jour la facture existante
     */
    public function updateInvoice(FileStorageService $fileStorageService)
    {
        $invoice = $this->form->update($fileStorageService);

        if ($invoice) {
            Toaster::success('Facture mise à jour avec succès !');
            $this->redirectRoute('invoices.index', $invoice);
        } else {
            Toaster::error('Une erreur s\'est produite lors de la mise à jour de la facture.');
        }
    }

    /**
     * Rend la vue
     */
    public function render()
    {
        return view('livewire.pages.invoices.edit', [
            'invoiceTypes' => InvoiceTypeEnum::getTypesOptions(),
            'remainingAmount' => $this->remainingAmount,
            'remainingPercentage' => $this->remainingPercentage,
            'shareMode' => $this->shareMode,
        ])->layout('layouts.app');
    }
}
