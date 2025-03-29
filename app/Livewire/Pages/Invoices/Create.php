<?php

namespace App\Livewire\Pages\Invoices;

use App\Enums\InvoiceTypeEnum;
use App\Livewire\Forms\InvoiceForm;
use App\Services\FileStorageService;
use App\Traits\InvoiceShareCalculationTrait;
use App\Traits\InvoiceTagManagement;
use Livewire\Component;
use Livewire\WithFileUploads;
use Masmerise\Toaster\Toaster;

class Create extends Component
{
    use InvoiceShareCalculationTrait;
    use InvoiceTagManagement;
    use WithFileUploads;

    public InvoiceForm $form;

    public $family_members = [];

    public function mount()
    {
        // Récupérer les membres de la famille
        $this->prepareFamilyMembers();

        // Initialiser le payeur principal
        $this->form->paid_by_user_id = auth()->id();

        $this->form->user_shares = [];

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
    public function createInvoice(FileStorageService $fileStorageService)
    {
        $invoice = $this->form->store($fileStorageService);

        if ($invoice) {
            Toaster::success('Facture créée avec succès !');
            $this->redirectRoute('invoices.index', $invoice);
        } else {
            Toaster::error('Une erreur s\'est produite lors de la création de la facture.');
        }
    }

    /**
     * Rend la vue
     */
    public function render()
    {
        // Recalculer les montants et pourcentages restants à chaque rendu
        $this->calculateRemainingShares();

        return view('livewire.pages.invoices.create', [
            'invoiceTypes' => InvoiceTypeEnum::getTypesOptions(),
            'remainingAmount' => $this->remainingAmount,
            'remainingPercentage' => $this->remainingPercentage,
            'shareMode' => $this->shareMode,
        ])->layout('layouts.app');
    }
}
