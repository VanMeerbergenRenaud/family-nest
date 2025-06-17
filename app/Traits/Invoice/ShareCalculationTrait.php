<?php

namespace App\Traits\Invoice;

use App\Enums\CurrencyEnum;
use Illuminate\Support\Number;

trait ShareCalculationTrait
{
    public string $shareMode = 'amount';

    public float $remainingAmount = 0;

    public float $remainingPercentage = 100;

    /**
     * Calcule le montant et le pourcentage restants non attribués
     */
    public function calculateRemainingShares(): void
    {
        $this->normalizeUserShares();

        $invoiceAmount = floatval($this->form->amount ?? 0);
        $this->remainingAmount = $invoiceAmount;
        $this->remainingPercentage = 100;

        if (empty($this->form->user_shares)) {
            return;
        }

        $totalAmount = $totalPercentage = 0;

        foreach ($this->form->user_shares as $share) {
            $totalAmount += floatval($share['amount'] ?? 0);
            $totalPercentage += floatval($share['percentage'] ?? 0);
        }

        $this->remainingAmount = max(0, $invoiceAmount - $totalAmount);
        $this->remainingPercentage = max(0, 100 - $totalPercentage);
    }

    /**
     * Met à jour ou ajoute la part d'un utilisateur
     */
    public function updateShare(int $userId, $value, string $type = 'percentage'): void
    {
        $this->normalizeUserShares();

        $value = $this->normalizeNumber($value);
        $this->form->user_shares ??= [];
        $invoiceAmount = floatval($this->form->amount ?? 0);

        // Trouver ou créer la part de l'utilisateur
        $index = $this->findShareIndex($userId);
        $share = [
            'id' => $userId,
            'amount' => 0,
            'percentage' => 0,
        ];

        // Si la part existe déjà, récupérer ses valeurs
        if ($index !== false) {
            $share = $this->form->user_shares[$index];
        }

        // Mettre à jour selon le type (pourcentage ou montant)
        if ($type === 'percentage') {
            $share['percentage'] = min(100, max(0, $value));
            $share['amount'] = $invoiceAmount > 0
                ? $this->normalizeNumber(($share['percentage'] / 100) * $invoiceAmount)
                : 0;
        } else {
            $share['amount'] = min($invoiceAmount, max(0, $value));
            $share['percentage'] = $invoiceAmount > 0
                ? $this->normalizeNumber(($share['amount'] / $invoiceAmount) * 100)
                : 0;
        }

        // Sauvegarder la part
        if ($index !== false) {
            $this->form->user_shares[$index] = $share;
        } else {
            $this->form->user_shares[] = $share;
        }

        $this->calculateRemainingShares();
    }

    /**
     * Supprime la part d'un utilisateur
     */
    public function removeShare(int $userId): void
    {
        $shareIndex = $this->findShareIndex($userId);

        if ($shareIndex !== false) {
            unset($this->form->user_shares[$shareIndex]);
            $this->form->user_shares = array_values($this->form->user_shares);
        }

        $this->calculateRemainingShares();
    }

    /**
     * Distribue équitablement les parts entre les utilisateurs
     */
    public function distributeEvenly(array $userIds = []): void
    {
        $userIds = $userIds ?: $this->family_members->pluck('id')->toArray();
        $count = count($userIds);

        // S'il y a moins de 2 membres, pas besoin de répartition
        if ($count < 2) {
            return;
        }

        $this->form->user_shares = [];
        $invoiceAmount = floatval($this->form->amount ?? 0);

        // Si le montant est nul, pas besoin de calculer
        if ($invoiceAmount <= 0) {
            $this->calculateRemainingShares();

            return;
        }

        // Calculer la part par personne selon le mode
        if ($this->shareMode === 'percentage') {
            $sharePercentage = $this->normalizeNumber(100 / $count);
            $totalDistributed = 0;

            // Distribuer les parts à tous sauf le dernier
            foreach ($userIds as $i => $userId) {
                // Pour le dernier utilisateur, prendre le reste
                if ($i === $count - 1) {
                    $percentage = 100 - $totalDistributed;
                } else {
                    $percentage = $sharePercentage;
                    $totalDistributed += $percentage;
                }

                $this->form->user_shares[] = [
                    'id' => $userId,
                    'percentage' => $percentage,
                    'amount' => $this->normalizeNumber(($percentage / 100) * $invoiceAmount),
                ];
            }
        } else {
            $shareAmount = $this->normalizeNumber($invoiceAmount / $count);
            $totalDistributed = 0;

            // Distribuer les parts à tous sauf le dernier
            foreach ($userIds as $i => $userId) {
                // Pour le dernier utilisateur, prendre le reste
                if ($i === $count - 1) {
                    $amount = $invoiceAmount - $totalDistributed;
                } else {
                    $amount = $shareAmount;
                    $totalDistributed += $amount;
                }

                $this->form->user_shares[] = [
                    'id' => $userId,
                    'amount' => $amount,
                    'percentage' => $this->normalizeNumber(($amount / $invoiceAmount) * 100),
                ];
            }
        }

        $this->calculateRemainingShares();
    }

    /**
     * Réinitialise toutes les parts
     */
    public function resetShares(): void
    {
        $this->form->user_shares = [];
        $this->calculateRemainingShares();
    }

    /**
     * Trouve l'index d'un utilisateur dans le tableau des parts
     */
    protected function findShareIndex(int $userId): int|false
    {
        if (empty($this->form->user_shares)) {
            return false;
        }

        foreach ($this->form->user_shares as $index => $share) {
            if (!isset($share['id'])) {
                continue;
            }

            if ((int) $share['id'] === $userId) {
                return $index;
            }
        }

        return false;
    }

    /**
     * Normalise un nombre à 2 décimales
     */
    protected function normalizeNumber($value): float
    {
        if (! is_numeric($value)) {
            return 0;
        }

        return round((float) $value, 2);
    }

    protected function normalizeUserShares(): void
    {
        if (empty($this->form->user_shares)) {
            $this->form->user_shares = [];
            return;
        }

        // Filtrer pour garder uniquement les entrées valides
        $this->form->user_shares = array_filter($this->form->user_shares, function($share) {
            return isset($share['id']) && is_numeric($share['id']);
        });

        // Réindexer le tableau pour éviter les indices non séquentiels
        $this->form->user_shares = array_values($this->form->user_shares);
    }

    /**
     * Obtient un résumé des parts pour l'interface utilisateur
     */
    public function getShareSummary(): array
    {
        $shares = $this->form->user_shares ?? [];
        $totalShares = count($shares);
        $totalPercent = $totalAmount = 0;
        $invoiceAmount = floatval($this->form->amount ?? 0);

        foreach ($shares as $share) {
            $totalPercent += floatval($share['percentage'] ?? 0);
            $totalAmount += floatval($share['amount'] ?? 0);
        }

        $isComplete = ($totalPercent >= 99.9) ||
            ($invoiceAmount > 0 && abs($invoiceAmount - $totalAmount) < 0.01);

        return [
            'totalShares' => $totalShares,
            'totalPercent' => $totalPercent,
            'totalAmount' => $totalAmount,
            'isComplete' => $isComplete,
            'formattedTotalPercent' => Number::format($totalPercent, 0, locale: 'fr_FR'),
            'formattedTotalAmount' => Number::currency($totalAmount, $this->form->currency ?? 'EUR', locale: 'fr_FR'),
        ];
    }

    /**
     * Obtient les informations de part pour un membre spécifique
     */
    public function getMemberShareInfo($memberId): array
    {
        $index = $this->findShareIndex($memberId);

        if ($index !== false) {
            return [
                'hasShare' => true,
                'shareIndex' => $index,
                'shareData' => $this->form->user_shares[$index],
            ];
        }

        return ['hasShare' => false, 'shareIndex' => null, 'shareData' => null];
    }

    /**
     * Obtient le symbole de la devise actuelle
     */
    public function getCurrencySymbol(): string
    {
        if (! isset($this->form->currency)) {
            return '€';
        }

        return CurrencyEnum::tryFrom($this->form->currency)?->symbol() ?? '€';
    }

    /**
     * Initialise les parts pour la facture
     */
    protected function initializeShares(): void
    {
        if (! isset($this->form->paid_by_user_id)) {
            $this->form->paid_by_user_id = auth()->user()->id;
        }

        $this->calculateRemainingShares();
    }
}
