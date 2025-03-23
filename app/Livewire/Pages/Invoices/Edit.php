<?php

namespace App\Livewire\Pages\Invoices;

use App\Enums\InvoiceTypeEnum;
use App\Livewire\Forms\InvoiceForm;
use App\Models\Family;
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

    // Variables pour le partage des montants
    public $remainingAmount;

    public $remainingPercentage = 100;

    public $shareMode = 'percentage'; // 'percentage' ou 'amount'

    public function mount($id)
    {
        $this->invoice = auth()->user()->invoices()
            ->with('file')
            ->findOrFail($id);

        $this->form->setFromInvoice($this->invoice);

        $this->family_members = Family::where('user_id', auth()->id())
            ->where('is_active', true)
            ->orderBy('is_primary', 'desc')
            ->orderBy('name')
            ->get();

        // Initialiser le tableau des tags
        $this->initializeTagManagement();

        // Initialiser le payeur principal (membre principal de la famille)
        $primaryMember = $this->family_members->firstWhere('is_primary', true);
        if ($primaryMember) {
            $this->form->paid_by_id = $primaryMember->id;
        }

        // Initialiser les parts de partage
        $this->calculateRemainingShares();
    }

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
     * Ajouter ou mettre à jour une part pour un membre
     */
    public function updateShare($memberId, $value, $type = 'percentage')
    {
        // Initialiser la structure si elle n'existe pas déjà
        if (empty($this->form->family_shares)) {
            $this->form->family_shares = [];
        }

        // Chercher si ce membre a déjà une part
        $memberIndex = null;
        foreach ($this->form->family_shares as $index => $share) {
            if ($share['id'] == $memberId) {
                $memberIndex = $index;
                break;
            }
        }

        // Mettre à jour ou ajouter la part
        if ($memberIndex !== null) {
            if ($type === 'percentage') {
                $this->form->family_shares[$memberIndex]['percentage'] = $value;
                $this->form->family_shares[$memberIndex]['amount'] = $this->calculateAmountFromPercentage($value);
            } else {
                $this->form->family_shares[$memberIndex]['amount'] = $value;
                $this->form->family_shares[$memberIndex]['percentage'] = $this->calculatePercentageFromAmount($value);
            }
        } else {
            // Nouveau membre
            $newShare = [
                'id' => $memberId,
                'amount' => $type === 'percentage' ? $this->calculateAmountFromPercentage($value) : $value,
                'percentage' => $type === 'percentage' ? $value : $this->calculatePercentageFromAmount($value),
            ];
            $this->form->family_shares[] = $newShare;
        }

        $this->calculateRemainingShares();
    }

    /**
     * Supprimer la part d'un membre
     */
    public function removeShare($memberId)
    {
        foreach ($this->form->family_shares as $index => $share) {
            if ($share['id'] == $memberId) {
                unset($this->form->family_shares[$index]);
                break;
            }
        }

        // Réindexer le tableau
        $this->form->family_shares = array_values($this->form->family_shares);
        $this->calculateRemainingShares();
    }

    /**
     * Distribuer également les parts entre tous les membres sélectionnés
     */
    public function distributeEvenly($memberIds = [])
    {
        if (empty($memberIds)) {
            // Si aucun membre n'est spécifié, utiliser tous les membres
            $memberIds = $this->family_members->pluck('id')->toArray();
        }

        $count = count($memberIds);
        if ($count === 0) {
            return;
        }

        // Réinitialiser les parts existantes
        $this->form->family_shares = [];

        // Calculer la part équitable
        if ($this->shareMode === 'percentage') {
            $share = 100 / $count;
            foreach ($memberIds as $memberId) {
                $this->updateShare($memberId, $share, 'percentage');
            }
        } else {
            $amount = $this->form->amount;
            $share = $amount / $count;
            foreach ($memberIds as $memberId) {
                $this->updateShare($memberId, $share, 'amount');
            }
        }
    }

    /**
     * Calculer le montant restant et le pourcentage restant
     */
    public function calculateRemainingShares()
    {
        $totalAmount = 0;
        $totalPercentage = 0;

        if (! empty($this->form->family_shares)) {
            foreach ($this->form->family_shares as $share) {
                $totalAmount += $share['amount'] ?? 0;
                $totalPercentage += $share['percentage'] ?? 0;
            }
        }

        $this->remainingAmount = max(0, $this->form->amount - $totalAmount);
        $this->remainingPercentage = max(0, 100 - $totalPercentage);
    }

    /**
     * Calculer le montant à partir d'un pourcentage
     */
    private function calculateAmountFromPercentage($percentage)
    {
        if (! is_numeric($this->form->amount) || ! is_numeric($percentage)) {
            return 0;
        }

        return round(($percentage / 100) * $this->form->amount, 2);
    }

    /**
     * Calculer le pourcentage à partir d'un montant
     */
    private function calculatePercentageFromAmount($amount)
    {
        if (! is_numeric($this->form->amount) || $this->form->amount == 0 || ! is_numeric($amount)) {
            return 0;
        }

        return round(($amount / $this->form->amount) * 100, 2);
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
