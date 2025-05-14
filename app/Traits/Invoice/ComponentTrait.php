<?php

namespace App\Traits\Invoice;

use Illuminate\Support\Collection;

trait ComponentTrait
{
    /**
     * Charge les membres de la famille de l'utilisateur
     */
    protected function loadFamilyMembers(): void
    {
        $user = auth()->user();
        $family = $user->family();

        // Si l'utilisateur a une famille, on récupère tous les membres
        // Sinon, on crée une collection avec juste l'utilisateur actuel
        $this->family_members = $family
            ? $family->users()->get()
            : collect([$user]);

        // S'assurer que l'utilisateur authentifié est dans la liste
        if (! $this->family_members->contains('id', $user->id)) {
            $this->family_members->prepend($user);
        }
    }

    /**
     * Met à jour les parts quand le montant change
     */
    public function updatedFormAmount(): void
    {
        // Normaliser le montant
        if (isset($this->form->amount)) {
            $this->form->amount = is_object($this->form) && method_exists($this->form, 'normalizeAmount')
                ? $this->form->normalizeAmount($this->form->amount)
                : $this->normalizeNumber($this->form->amount);
        }

        // Recalculer les montants des parts si elles existent
        if (! empty($this->form->user_shares)) {
            $this->recalculateShareAmounts();
        }

        $this->calculateRemainingShares();
    }

    /**
     * Recalcule les montants des parts en fonction des pourcentages
     */
    private function recalculateShareAmounts(): void
    {
        foreach ($this->form->user_shares as &$share) {
            if (isset($share['percentage']) && $share['percentage'] > 0) {
                $share['amount'] = $this->calculateAmountFromPercentage($share['percentage']);
            }
        }
    }

    /**
     * Met à jour les catégories disponibles lorsque le type change
     */
    public function updatedFormType(): void
    {
        if (is_object($this->form) && method_exists($this->form, 'updateAvailableCategories')) {
            $this->form->updateAvailableCategories();
            $this->form->category = null;
        }
    }

    /**
     * Supprime le fichier téléchargé
     */
    public function removeUploadedFile(): void
    {
        if (is_object($this->form) && method_exists($this->form, 'removeFile')) {
            $this->form->removeFile();
            $this->form->resetErrorBag('uploadedFile');
        }
    }

    /**
     * Prépare la liste de sélection du payeur
     */
    public function preparePayerSelectionList(): Collection
    {
        if (! isset($this->family_members) || $this->family_members->isEmpty()) {
            return collect([auth()->user()]);
        }

        $members = clone $this->family_members;

        if (! $members->contains('id', auth()->id())) {
            $members->prepend(auth()->user());
        }

        return $members;
    }
}
