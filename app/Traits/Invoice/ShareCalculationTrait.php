<?php

namespace App\Traits\Invoice;

use App\Enums\CurrencyEnum;
use App\Models\User;
use Illuminate\Support\Collection;
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
        $totalAmount = $totalPercentage = 0;
        $invoiceAmount = floatval($this->form->amount ?? 0);

        foreach ($this->form->user_shares ?? [] as $share) {
            $totalAmount += floatval($share['amount'] ?? 0);
            $totalPercentage += floatval($share['percentage'] ?? 0);
        }

        // Calculer les valeurs restantes sans ajuster les parts existantes
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
            if ($type === 'percentage') {
                // Limiter le pourcentage à 100% maximum
                $value = min(100, max(0, $value));
                $this->form->user_shares[$userIndex]['percentage'] = $value;

                // Calculer le montant à partir du pourcentage
                if ($invoiceAmount > 0) {
                    $this->form->user_shares[$userIndex]['amount'] = $this->calculateAmountFromPercentage($value);
                }
            } else {
                // Limiter le montant au montant total de la facture maximum
                $value = min($invoiceAmount, max(0, $value));
                $this->form->user_shares[$userIndex]['amount'] = $value;

                // Calculer le pourcentage à partir du montant
                if ($invoiceAmount > 0) {
                    $this->form->user_shares[$userIndex]['percentage'] = $this->calculatePercentageFromAmount($value);
                }
            }
        } else {
            // Ajouter une nouvelle part
            $newShare = [
                'id' => $userId,
                'amount' => 0,
                'percentage' => 0,
            ];

            if ($type === 'percentage') {
                $value = min(100, max(0, $value)); // Limiter entre 0 et 100
                $newShare['percentage'] = $value;
                $newShare['amount'] = $invoiceAmount > 0 ? $this->calculateAmountFromPercentage($value) : 0;
            } else {
                $value = min($invoiceAmount, max(0, $value)); // Limiter entre 0 et montant max
                $newShare['amount'] = $value;
                $newShare['percentage'] = $invoiceAmount > 0 ? $this->calculatePercentageFromAmount($value) : 0;
            }

            $this->form->user_shares[] = $newShare;
        }

        $this->calculateRemainingShares();
    }

    /**
     * Supprime la part d'un utilisateur
     */
    public function removeShare(int $userId): void
    {
        $this->form->user_shares = array_values(array_filter(
            $this->form->user_shares ?? [],
            fn ($share) => $share['id'] != $userId
        ));

        $this->calculateRemainingShares();
    }

    /**
     * Distribue équitablement les parts entre les utilisateurs spécifiés
     * Cette méthode est appelée uniquement quand l'utilisateur clique sur "Partager équitablement"
     */
    public function distributeEvenly(array $userIds = []): void
    {
        // Si aucun ID n'est fourni, utiliser tous les membres de la famille
        $userIds = $userIds ?: $this->family_members->pluck('id')->toArray();
        $count = count($userIds);

        if (! $count) {
            return;
        }

        // Réinitialiser les parts pour une distribution équitable
        $this->form->user_shares = [];
        $invoiceAmount = floatval($this->form->amount ?? 0);

        if ($this->shareMode === 'percentage') {
            // Distribuer en pourcentage
            $sharePercentage = $this->normalizeNumber(100 / $count);
            $remainingPercentage = 100;

            foreach ($userIds as $index => $userId) {
                // Pour le dernier utilisateur, attribuer le reste pour éviter les erreurs d'arrondi
                if ($index === $count - 1) {
                    $percentage = $remainingPercentage;
                } else {
                    $percentage = $sharePercentage;
                    $remainingPercentage -= $sharePercentage;
                }

                $this->form->user_shares[] = [
                    'id' => $userId,
                    'percentage' => $percentage,
                    'amount' => $invoiceAmount > 0 ? $this->calculateAmountFromPercentage($percentage) : 0,
                ];
            }
        } else {
            // Distribuer en montant
            $shareAmount = $invoiceAmount > 0 ? $this->normalizeNumber($invoiceAmount / $count) : 0;
            $remainingAmount = $invoiceAmount;

            foreach ($userIds as $index => $userId) {
                // Pour le dernier utilisateur, attribuer le reste pour éviter les erreurs d'arrondi
                if ($index === $count - 1) {
                    $amount = $remainingAmount;
                } else {
                    $amount = $shareAmount;
                    $remainingAmount -= $shareAmount;
                }

                $this->form->user_shares[] = [
                    'id' => $userId,
                    'amount' => $amount,
                    'percentage' => $invoiceAmount > 0 ? $this->calculatePercentageFromAmount($amount) : 0,
                ];
            }
        }

        $this->calculateRemainingShares();
    }

    /**
     * Calcule le montant à partir d'un pourcentage
     */
    private function calculateAmountFromPercentage(float $percentage): float
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
    private function calculatePercentageFromAmount(float $amount): float
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
    private function findShareIndex(int $userId): int|false
    {
        foreach ($this->form->user_shares ?? [] as $index => $share) {
            if ((int) $share['id'] === $userId) {
                return $index;
            }
        }

        return false;
    }

    /**
     * Normalise un nombre à 2 décimales
     */
    private function normalizeNumber($value): float
    {
        if (! is_numeric($value)) {
            return 0;
        }

        return round((float) $value, 2);
    }

    /**
     * Formate un montant pour l'affichage
     */
    public function formatMontant($value): string
    {
        return Number::format($value, 2, locale: 'fr_FR');
    }

    /**
     * Formate un pourcentage pour l'affichage
     */
    public function formatPourcentage($value): string
    {
        return Number::format($value, 2, locale: 'fr_FR').'%';
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

        // Considérer comme complet si le pourcentage est proche de 100% ou
        // si le montant est proche du montant total de la facture
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
        if (empty($this->form->user_shares)) {
            return ['hasShare' => false, 'shareIndex' => null, 'shareData' => null];
        }

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
     * Prépare la liste de sélection du payeur
     */
    public function preparePayerSelectionList(): Collection
    {
        // Si l'utilisateur n'a pas de famille, retourner uniquement l'utilisateur authentifié
        if (! isset($this->family_members) || $this->family_members->isEmpty()) {
            return collect([auth()->user()]);
        }

        // S'assurer que l'utilisateur authentifié est dans la liste
        $members = clone $this->family_members;
        if (! $members->contains('id', auth()->id())) {
            $members->prepend(auth()->user());
        }

        return $members;
    }

    /**
     * Initialise les parts utilisateur
     */
    public function initializeUserShares(): void
    {
        if (! isset($this->form->user_shares)) {
            $this->form->user_shares = [];

            return;
        }

        // Dédupliquer les parts par ID d'utilisateur
        $sharesById = [];
        foreach ($this->form->user_shares as $share) {
            if (isset($share['id'])) {
                $sharesById[$share['id']] = [
                    'id' => (int) $share['id'],
                    'amount' => $this->normalizeNumber($share['amount'] ?? 0),
                    'percentage' => $this->normalizeNumber($share['percentage'] ?? 0),
                ];
            }
        }

        $this->form->user_shares = array_values($sharesById);
        $this->calculateRemainingShares();
    }

    /**
     * Obtient un résumé détaillé des parts pour l'affichage
     */
    public function getShareDetailSummary(Collection $familyMembers): array
    {
        $invoiceAmount = floatval($this->form->amount ?? 0);

        if ($invoiceAmount <= 0) {
            return [
                'hasAmount' => false,
                'hasDetails' => false,
                'formattedShared' => 0,
                'totalPercentage' => 0,
                'memberDetails' => [],
            ];
        }

        $userShares = $this->form->user_shares ?? [];

        // Si aucune part n'est définie, montrer le payeur comme seul bénéficiaire à 100%
        if (empty($userShares)) {
            $payerId = $this->form->paid_by_user_id ?? auth()->id();
            $payer = $this->findMember($familyMembers, $payerId);

            return $this->getDefaultShareSummary($payer, $invoiceAmount);
        }

        // Construire le résumé des parts existantes
        return $this->buildShareDetailSummary($userShares, $familyMembers, $invoiceAmount);
    }

    /**
     * Construit le résumé détaillé des parts
     */
    private function buildShareDetailSummary(array $userShares, Collection $familyMembers, float $invoiceAmount): array
    {
        $memberDetails = [];
        $totalPercentage = 0;
        $membersById = $familyMembers->keyBy('id');

        // Ajouter l'utilisateur authentifié au mapping s'il n'y est pas
        if (! isset($membersById[auth()->id()])) {
            $membersById[auth()->id()] = auth()->user();
        }

        foreach ($userShares as $share) {
            if (! isset($share['id'])) {
                continue;
            }

            // Récupérer le membre
            $member = $this->findMember($familyMembers, $share['id']);

            if (! $member) {
                continue;
            }

            $percentage = floatval($share['percentage'] ?? 0);
            $amount = floatval($share['amount'] ?? 0);
            $totalPercentage += $percentage;

            $memberDetails[] = [
                'id' => $member->id,
                'name' => $member->name,
                'avatar' => $member->avatar_url ?? asset('img/img_placeholder.jpg'),
                'formattedAmount' => number_format($amount, 2, ',', ' '),
                'sharePercentage' => $percentage,
                'formattedPercentage' => number_format($percentage, 0),
                'isPayer' => $member->id == $this->form->paid_by_user_id,
            ];
        }

        return [
            'hasAmount' => true,
            'hasDetails' => ! empty($memberDetails),
            'formattedShared' => number_format($invoiceAmount, 2, ',', ' '),
            'totalPercentage' => $totalPercentage,
            'memberDetails' => $memberDetails,
        ];
    }

    /**
     * Trouve un membre dans la collection de membres ou recherche dans la base de données
     */
    private function findMember(Collection $familyMembers, int $userId): ?User
    {
        // Chercher d'abord dans la collection
        $member = $familyMembers->firstWhere('id', $userId);

        if ($member) {
            return $member;
        }

        // Si l'utilisateur cherché est l'utilisateur authentifié
        if ($userId === auth()->id()) {
            return auth()->user();
        }

        // En dernier recours, chercher dans la base de données
        return User::find($userId);
    }

    /**
     * Génère un résumé de partage par défaut pour un seul utilisateur (100%)
     */
    private function getDefaultShareSummary(?User $user, float $invoiceAmount): array
    {
        // Si aucun utilisateur n'est trouvé, utiliser l'utilisateur authentifié
        $user = $user ?? auth()->user();

        return [
            'hasAmount' => true,
            'hasDetails' => true,
            'formattedShared' => number_format($invoiceAmount, 2, ',', ' '),
            'totalPercentage' => 100,
            'memberDetails' => [
                [
                    'id' => $user->id,
                    'name' => $user->name,
                    'avatar' => $user->avatar_url ?? asset('img/img_placeholder.jpg'),
                    'formattedAmount' => number_format($invoiceAmount, 2, ',', ' '),
                    'sharePercentage' => 100,
                    'formattedPercentage' => '100',
                    'isPayer' => true,
                ],
            ],
        ];
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
            $currencyEnum = CurrencyEnum::tryFrom($this->form->currency);

            return $currencyEnum?->symbol() ?? '€';
        } catch (\Exception) {
            return '€';
        }
    }
}
