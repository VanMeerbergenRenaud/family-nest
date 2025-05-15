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

        // Calculer les valeurs restantes
        $this->remainingAmount = max(0, $invoiceAmount - $totalAmount);
        $this->remainingPercentage = max(0, 100 - $totalPercentage);
    }

    /**
     * Met à jour la part d'un utilisateur
     */
    public function updateShare(int $userId, $value, string $type = 'percentage'): void
    {
        // Normaliser la valeur à 2 décimales
        $value = $this->normalizeNumber($value);
        $this->form->user_shares ??= [];

        // Rechercher l'index de l'utilisateur dans les parts
        $userIndex = $this->findShareIndex($userId);
        $invoiceAmount = floatval($this->form->amount ?? 0);

        if ($userIndex !== false) {
            // Mettre à jour une part existante
            $this->updateExistingShare($userIndex, $value, $type, $invoiceAmount);
        } else {
            // Ajouter une nouvelle part
            $this->addNewShare($userId, $value, $type, $invoiceAmount);
        }

        $this->calculateRemainingShares();
    }

    /**
     * Met à jour une part existante
     */
    private function updateExistingShare(int $index, float $value, string $type, float $invoiceAmount): void
    {
        if ($type === 'percentage') {
            $value = min(100, max(0, $value));
            $this->form->user_shares[$index]['percentage'] = $value;
            $this->form->user_shares[$index]['amount'] = $invoiceAmount > 0
                ? $this->calculateAmountFromPercentage($value)
                : 0;
        } else {
            $value = min($invoiceAmount, max(0, $value));
            $this->form->user_shares[$index]['amount'] = $value;
            $this->form->user_shares[$index]['percentage'] = $invoiceAmount > 0
                ? $this->calculatePercentageFromAmount($value)
                : 0;
        }
    }

    /**
     * Ajoute une nouvelle part
     */
    private function addNewShare(int $userId, float $value, string $type, float $invoiceAmount): void
    {
        $newShare = [
            'id' => $userId,
            'amount' => 0,
            'percentage' => 0,
        ];

        if ($type === 'percentage') {
            $value = min(100, max(0, $value));
            $newShare['percentage'] = $value;
            $newShare['amount'] = $invoiceAmount > 0 ? $this->calculateAmountFromPercentage($value) : 0;
        } else {
            $value = min($invoiceAmount, max(0, $value));
            $newShare['amount'] = $value;
            $newShare['percentage'] = $invoiceAmount > 0 ? $this->calculatePercentageFromAmount($value) : 0;
        }

        $this->form->user_shares[] = $newShare;
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

        if ($count < 2) {
            return;
        }

        $this->form->user_shares = [];
        $invoiceAmount = floatval($this->form->amount ?? 0);

        if ($invoiceAmount <= 0) {
            $this->calculateRemainingShares();

            return;
        }

        $this->shareMode === 'percentage'
            ? $this->distributeByPercentage($userIds, $invoiceAmount, $count)
            : $this->distributeByAmount($userIds, $invoiceAmount, $count);

        $this->calculateRemainingShares();
    }

    /**
     * Distribue par pourcentage
     */
    private function distributeByPercentage(array $userIds, float $invoiceAmount, int $count): void
    {
        $sharePercentage = $this->normalizeNumber(100 / $count);
        $remainingPercentage = 100;

        foreach ($userIds as $index => $userId) {
            // Le dernier utilisateur prend le reste pour éviter les erreurs d'arrondi
            $percentage = ($index === $count - 1) ? $remainingPercentage : $sharePercentage;
            $remainingPercentage -= $sharePercentage;

            $this->form->user_shares[] = [
                'id' => $userId,
                'percentage' => $percentage,
                'amount' => $invoiceAmount > 0 ? $this->calculateAmountFromPercentage($percentage) : 0,
            ];
        }
    }

    /**
     * Distribue par montant
     */
    private function distributeByAmount(array $userIds, float $invoiceAmount, int $count): void
    {
        $shareAmount = $invoiceAmount > 0 ? $this->normalizeNumber($invoiceAmount / $count) : 0;
        $remainingAmount = $invoiceAmount;

        foreach ($userIds as $index => $userId) {
            // Le dernier utilisateur prend le reste pour éviter les erreurs d'arrondi
            $amount = ($index === $count - 1) ? $remainingAmount : $shareAmount;
            $remainingAmount -= $shareAmount;

            $this->form->user_shares[] = [
                'id' => $userId,
                'amount' => $amount,
                'percentage' => $invoiceAmount > 0 ? $this->calculatePercentageFromAmount($amount) : 0,
            ];
        }
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
     * Calcule le montant à partir d'un pourcentage
     */
    protected function calculateAmountFromPercentage(float $percentage): float
    {
        $invoiceAmount = floatval($this->form->amount ?? 0);

        if ($invoiceAmount <= 0 || $percentage <= 0) {
            return 0;
        }

        return $this->normalizeNumber(($percentage / 100) * $invoiceAmount);
    }

    /**
     * Calcule le pourcentage à partir d'un montant
     */
    protected function calculatePercentageFromAmount(float $amount): float
    {
        $invoiceAmount = floatval($this->form->amount ?? 0);

        if ($invoiceAmount <= 0 || $amount <= 0) {
            return 0;
        }

        return $this->normalizeNumber(($amount / $invoiceAmount) * 100);
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

    /**
     * Obtient un résumé des parts pour l'interface utilisateur lors de l'édition
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

        try {
            return CurrencyEnum::tryFrom($this->form->currency)?->symbol() ?? '€';
        } catch (\Exception) {
            return '€';
        }
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

    /**
     * Charge les parts depuis une facture existante dans le formulaire d'édition
     */
    protected function loadSharesFromInvoice(): void
    {
        if (! isset($this->invoice) || $this->invoice->sharings->isEmpty()) {
            return;
        }

        foreach ($this->invoice->sharings as $sharing) {
            $this->form->user_shares[] = [
                'id' => $sharing->user_id,
                'amount' => floatval($sharing->share_amount ?? 0),
                'percentage' => floatval($sharing->share_percentage ?? 0),
            ];
        }
    }
}
