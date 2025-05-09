<?php

namespace App\Traits\Invoice;

use Masmerise\Toaster\Toaster;

/**
 * Trait commun pour les composants liés aux factures (Create, Edit, Show)
 * Contient les méthodes partagées par ces composants
 */
trait ComponentTrait
{
    /**
     * Charge les membres de la famille de l'utilisateur
     */
    protected function loadFamilyMembers(): void
    {
        $family = auth()->user()->family();

        if ($family) {
            // Récupérer les membres de la famille
            $this->family_members = $family->users()->get();
        } else {
            // Si pas de famille, uniquement l'utilisateur authentifié
            $this->family_members = collect([auth()->user()]);
        }

        // S'assurer que l'utilisateur authentifié est dans la liste
        if (! $this->family_members->contains('id', auth()->id())) {
            $this->family_members->prepend(auth()->user());
        }
    }

    /**
     * Recalcule les parts restantes quand le montant change
     */
    public function updatedFormAmount(): void
    {
        // Normalisation du montant pour éviter les erreurs de format
        if (isset($this->form->amount)) {
            $this->form->amount = $this->form->normalizeAmount($this->form->amount);
        }

        // Recalculer uniquement les valeurs restantes
        $this->calculateRemainingShares();
    }

    /**
     * Quand le mode de partage change, recalculer les parts
     */
    public function updatedShareMode(): void
    {
        $this->calculateRemainingShares();
    }

    /**
     * Met à jour les catégories disponibles lorsque le type change
     */
    public function updatedFormType(): void
    {
        $this->form->updateAvailableCategories();
        $this->form->category = null;
    }

    /**
     * Toast pour le rappel de paiement
     */
    public function updatedFormPaymentReminder($value): void
    {
        if ($value) {
            Toaster::info('Un rappel de paiement sera programmé pour le '.date('d/m/Y', strtotime($value)));
        }
    }

    /**
     * Supprime le fichier téléchargé
     */
    public function removeUploadedFile(): void
    {
        $this->form->removeFile();
        $this->form->resetErrorBag('uploadedFile');
    }

    /**
     * Valide les parts avant la sauvegarde finale
     */
    protected function validateShares(): void
    {
        // Si le montant est défini mais qu'aucune part n'est spécifiée,
        // créer automatiquement une part à 100% pour le payeur
        if (floatval($this->form->amount) > 0 && empty($this->form->user_shares)) {
            $this->form->user_shares = [[
                'id' => $this->form->paid_by_user_id,
                'amount' => floatval($this->form->amount),
                'percentage' => 100,
            ]];
        }

        // Assurer que les valeurs numériques sont correctement formatées
        // sans forcer le total à 100% ou au montant complet
        if (! empty($this->form->user_shares)) {
            foreach ($this->form->user_shares as &$share) {
                $share['amount'] = $this->normalizeNumber($share['amount'] ?? 0);
                $share['percentage'] = $this->normalizeNumber($share['percentage'] ?? 0);
            }
        }

        // Recalculer les valeurs restantes sans ajustement
        $this->calculateRemainingShares();
    }

    /**
     * Initialise les parts utilisateur à partir de la facture
     */
    protected function initializeShares(): void
    {
        // Si aucune part n'est définie mais que le montant est supérieur à 0,
        // créer une part à 100% pour le payeur
        if (empty($this->form->user_shares)) {
            if (floatval($this->form->amount ?? 0) > 0) {
                $this->form->user_shares = [[
                    'id' => $this->form->paid_by_user_id ?? auth()->id(),
                    'amount' => floatval($this->form->amount),
                    'percentage' => 100,
                ]];
            }
        }

        // S'assurer que le payeur est défini
        if (! isset($this->form->paid_by_user_id)) {
            $this->form->paid_by_user_id = auth()->id();
        }

        // Initialiser les parts et calculer les valeurs restantes
        $this->initializeUserShares();
        $this->calculateRemainingShares();
    }
}
