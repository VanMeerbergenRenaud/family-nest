<?php

namespace App\Traits;

use Illuminate\Support\Number;

trait InvoiceShareCalculationTrait
{
    public string $shareMode = 'amount';

    public float $remainingAmount = 0;

    public float $remainingPercentage = 100;

    // Calculer le montant restant et le pourcentage restant
    public function calculateRemainingShares(): void
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

    // Ajouter ou mettre à jour une part pour un membre
    public function updateShare($userId, $value, $type = 'percentage'): void
    {
        // Formater la valeur pour garantir exactement 2 décimales
        $value = number_format((float)$value, 2, '.', '');

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
            $this->form->user_shares[] = [
                'id' => $userId,
                'amount' => $type === 'percentage' ? $this->calculateAmountFromPercentage($value) : $value,
                'percentage' => $type === 'percentage' ? $value : $this->calculatePercentageFromAmount($value),
            ];
        }

        $this->calculateRemainingShares();
    }

    // Supprimer la part d'un membre
    public function removeShare($userId): void
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

    // Distribuer également les parts entre tous les membres sélectionnés
    public function distributeEvenly($userIds = []): void
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
            // Pour les pourcentages, distribuer 100% équitablement
            $share = number_format(100 / $count, 2, '.', '');

            // Ajuster pour éviter les erreurs d'arrondi
            $totalAssigned = 0;
            foreach ($userIds as $index => $userId) {
                if ($index === count($userIds) - 1) {
                    // Pour le dernier membre, attribuer ce qui reste pour atteindre 100%
                    $share = number_format(100 - $totalAssigned, 2, '.', '');
                }
                $this->updateShare($userId, $share, 'percentage');
                $totalAssigned += (float)$share;
            }
        } else {
            // Pour les montants, distribuer le montant total équitablement
            $amount = $this->form->amount;
            $shareAmount = number_format($amount / $count, 2, '.', '');

            // Ajuster pour éviter les erreurs d'arrondi
            $totalAssigned = 0;
            foreach ($userIds as $index => $userId) {
                if ($index === count($userIds) - 1) {
                    // Pour le dernier membre, attribuer ce qui reste pour atteindre le montant total
                    $shareAmount = number_format($amount - $totalAssigned, 2, '.', '');
                }
                $this->updateShare($userId, $shareAmount, 'amount');
                $totalAssigned += (float)$shareAmount;
            }
        }
    }

    // Calculer le montant à partir d'un pourcentage
    private function calculateAmountFromPercentage($percentage): float
    {
        if (!is_numeric($this->form->amount) || !is_numeric($percentage)) {
            return 0;
        }

        // Formater à exactement 2 décimales
        return number_format(($percentage / 100) * $this->form->amount, 2, '.', '');
    }

    // Calculer le pourcentage à partir d'un montant
    private function calculatePercentageFromAmount($amount): float
    {
        if (!is_numeric($this->form->amount) || $this->form->amount == 0 || !is_numeric($amount)) {
            return 0;
        }

        // Formater à exactement 2 décimales
        return number_format(($amount / $this->form->amount) * 100, 2, '.', '');
    }

    // Formater le montant pour l'affichage
    public function formatMontant($value): string
    {
        return Number::format($value, 2, locale: 'fr_FR');
    }

    // Formater le pourcentage pour l'affichage
    public function formatPourcentage($value): string
    {
        return Number::format($value, 2, locale: 'fr_FR') . '%';
    }
}
