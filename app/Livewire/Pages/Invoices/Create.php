<?php

namespace App\Livewire\Pages\Invoices;

use App\Enums\InvoiceTypeEnum;
use App\Livewire\Forms\InvoiceForm;
use App\Models\User;
use App\Traits\InvoiceTagManagement;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use InvoiceTagManagement;
    use WithFileUploads;

    public InvoiceForm $form;

    public $family_members = [];

    // Variables pour le partage des montants
    public $remainingAmount = 0;

    public $remainingPercentage = 100;

    public $shareMode = 'percentage'; // 'percentage' ou 'amount'

    public function mount()
    {
        // Récupérer la famille de l'utilisateur
        $family = auth()->user()->family();

        if ($family) {
            $this->form->family_id = $family->id;

            // Récupérer tous les membres de la famille
            $this->family_members = $family->users()
                ->where('users.id', '!=', auth()->id())
                ->get();
        } else {
            $this->family_members = collect();
        }

        // Ajouter l'utilisateur actuel à la liste
        $currentUser = User::find(auth()->id());
        if ($currentUser) {
            $this->family_members->prepend($currentUser);
        }

        // Initialiser le tableau des tags
        $this->initializeTagManagement();

        // Initialiser le payeur principal
        $this->form->paid_by_user_id = auth()->id();

        // Initialiser les parts de partage
        $this->form->user_shares = [];
        $this->calculateRemainingShares();
    }

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
     * Ajouter ou mettre à jour une part pour un membre
     */
    public function updateShare($userId, $value, $type = 'percentage')
    {
        // Initialiser la structure si elle n'existe pas déjà
        if (empty($this->form->user_shares)) {
            $this->form->user_shares = [];
        }

        // Chercher si ce membre a déjà une part
        $userIndex = null;
        foreach ($this->form->user_shares as $index => $share) {
            if ($share['id'] == $userId) {
                $userIndex = $index;
                break;
            }
        }

        // Mettre à jour ou ajouter la part
        if ($userIndex !== null) {
            if ($type === 'percentage') {
                $this->form->user_shares[$userIndex]['percentage'] = $value;
                $this->form->user_shares[$userIndex]['amount'] = $this->calculateAmountFromPercentage($value);
            } else {
                $this->form->user_shares[$userIndex]['amount'] = $value;
                $this->form->user_shares[$userIndex]['percentage'] = $this->calculatePercentageFromAmount($value);
            }
        } else {
            // Nouveau membre
            $newShare = [
                'id' => $userId,
                'amount' => $type === 'percentage' ? $this->calculateAmountFromPercentage($value) : $value,
                'percentage' => $type === 'percentage' ? $value : $this->calculatePercentageFromAmount($value),
            ];
            $this->form->user_shares[] = $newShare;
        }

        $this->calculateRemainingShares();
    }

    /**
     * Supprimer la part d'un membre
     */
    public function removeShare($userId)
    {
        foreach ($this->form->user_shares as $index => $share) {
            if ($share['id'] == $userId) {
                unset($this->form->user_shares[$index]);
                break;
            }
        }

        // Réindexer le tableau
        $this->form->user_shares = array_values($this->form->user_shares);
        $this->calculateRemainingShares();
    }

    /**
     * Distribuer également les parts entre tous les membres sélectionnés
     */
    public function distributeEvenly($userIds = [])
    {
        if (empty($userIds)) {
            // Si aucun membre n'est spécifié, utiliser tous les membres
            $userIds = $this->family_members->pluck('id')->toArray();
        }

        $count = count($userIds);
        if ($count === 0) {
            return;
        }

        // Réinitialiser les parts existantes
        $this->form->user_shares = [];

        // Calculer la part équitable
        if ($this->shareMode === 'percentage') {
            $share = 100 / $count;
            foreach ($userIds as $userId) {
                $this->updateShare($userId, $share, 'percentage');
            }
        } else {
            $amount = $this->form->amount;
            $share = $amount / $count;
            foreach ($userIds as $userId) {
                $this->updateShare($userId, $share, 'amount');
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

        if (!empty($this->form->user_shares)) {
            foreach ($this->form->user_shares as $share) {
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
        if (!is_numeric($this->form->amount) || !is_numeric($percentage)) {
            return 0;
        }

        // Limiter à exactement 2 décimales pour éviter les problèmes de précision
        return number_format(($percentage / 100) * $this->form->amount, 2, '.', '');
    }

    /**
     * Calculer le pourcentage à partir d'un montant
     */
    private function calculatePercentageFromAmount($amount)
    {
        if (!is_numeric($this->form->amount) || $this->form->amount == 0 || !is_numeric($amount)) {
            return 0;
        }

        // Limiter à exactement 2 décimales pour le pourcentage
        return number_format(($amount / $this->form->amount) * 100, 2, '.', '');
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
            session()->flash('success', 'La facture a été créée avec succès');
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
