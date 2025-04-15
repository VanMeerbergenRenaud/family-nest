<?php

namespace App\Traits;

use App\Enums\CurrencyEnum;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;

trait InvoiceShareCalculationTrait
{
    public string $shareMode = 'amount';

    public float $remainingAmount = 0;

    public float $remainingPercentage = 100;

    public function calculateRemainingShares(): void
    {
        $totalAmount = $totalPercentage = 0;

        foreach ($this->form->user_shares ?? [] as $share) {
            $totalAmount += floatval($share['amount'] ?? 0);
            $totalPercentage += floatval($share['percentage'] ?? 0);
        }

        $this->remainingAmount = max(0, (float) ($this->form->amount ?? 0) - $totalAmount);
        $this->remainingPercentage = max(0, 100 - $totalPercentage);
    }

    public function updateShare($userId, $value, $type = 'percentage'): void
    {
        $value = number_format((float) $value, 2, '.', '');
        $this->form->user_shares ??= [];
        $userIndex = array_search($userId, array_column($this->form->user_shares, 'id'));

        if ($userIndex !== false) {
            if ($type === 'percentage') {
                $this->form->user_shares[$userIndex]['percentage'] = $value;
                $this->form->user_shares[$userIndex]['amount'] = $this->calculateAmountFromPercentage($value);
            } else {
                $this->form->user_shares[$userIndex]['amount'] = $value;
                $this->form->user_shares[$userIndex]['percentage'] = $this->calculatePercentageFromAmount($value);
            }
        } else {
            $this->form->user_shares[] = [
                'id' => $userId,
                'amount' => $type === 'percentage' ? $this->calculateAmountFromPercentage($value) : $value,
                'percentage' => $type === 'percentage' ? $value : $this->calculatePercentageFromAmount($value),
            ];
        }

        $this->calculateRemainingShares();
    }

    public function removeShare($userId): void
    {
        $this->form->user_shares = array_values(array_filter(
            $this->form->user_shares ?? [],
            fn ($share) => $share['id'] != $userId
        ));

        $this->calculateRemainingShares();
    }

    public function distributeEvenly($userIds = []): void
    {
        $userIds = $userIds ?: $this->family_members->pluck('id')->toArray();
        $count = count($userIds);
        if (! $count) {
            return;
        }

        $this->form->user_shares = [];
        $totalAssigned = 0;

        if ($this->shareMode === 'percentage') {
            $share = number_format(100 / $count, 2, '.', '');

            foreach ($userIds as $index => $userId) {
                if ($index === $count - 1) {
                    $share = number_format(100 - $totalAssigned, 2, '.', '');
                }

                $this->updateShare($userId, $share);
                $totalAssigned += (float) $share;
            }
        } else {
            $amount = (float) ($this->form->amount ?? 0);
            $shareAmount = number_format($amount / $count, 2, '.', '');

            foreach ($userIds as $index => $userId) {
                if ($index === $count - 1) {
                    $shareAmount = number_format($amount - $totalAssigned, 2, '.', '');
                }

                $this->updateShare($userId, $shareAmount, 'amount');
                $totalAssigned += (float) $shareAmount;
            }
        }
    }

    private function calculateAmountFromPercentage($percentage): float
    {
        if (! is_numeric($this->form->amount) || ! is_numeric($percentage)) {
            return 0;
        }

        return number_format(($percentage / 100) * (float) $this->form->amount, 2, '.', '');
    }

    private function calculatePercentageFromAmount($amount): float
    {
        if (! is_numeric($this->form->amount) || ! (float) $this->form->amount || ! is_numeric($amount)) {
            return 0;
        }

        return number_format(($amount / (float) $this->form->amount) * 100, 2, '.', '');
    }

    public function formatMontant($value): string
    {
        return Number::format($value, 2, locale: 'fr_FR');
    }

    public function formatPourcentage($value): string
    {
        return Number::format($value, 2, locale: 'fr_FR').'%';
    }

    public function getShareSummary(): array
    {
        $shares = $this->form->user_shares ?? [];
        $totalShares = count($shares);
        $totalPercent = $totalAmount = 0;

        foreach ($shares as $share) {
            $totalPercent += floatval($share['percentage'] ?? 0);
            $totalAmount += floatval($share['amount'] ?? 0);
        }

        $isComplete = $totalPercent >= 99.9 ||
            (is_numeric($this->form->amount) && abs((float) $this->form->amount - $totalAmount) < 0.01);

        return [
            'totalShares' => $totalShares,
            'totalPercent' => $totalPercent,
            'totalAmount' => $totalAmount,
            'isComplete' => $isComplete,
            'formattedTotalPercent' => Number::format($totalPercent, 0, locale: 'fr_FR'),
            'formattedTotalAmount' => Number::currency($totalAmount, 'EUR', locale: 'fr_FR'),
        ];
    }

    public function getMemberShareInfo($memberId): array
    {
        if (empty($this->form->user_shares)) {
            return ['hasShare' => false, 'shareIndex' => null, 'shareData' => null];
        }

        foreach ($this->form->user_shares as $index => $share) {
            if ((string) $share['id'] === (string) $memberId) {
                return ['hasShare' => true, 'shareIndex' => $index, 'shareData' => $share];
            }
        }

        return ['hasShare' => false, 'shareIndex' => null, 'shareData' => null];
    }

    public function preparePayerSelectionList(): Collection
    {
        // Si l'utilisateur n'a pas de famille, retourner uniquement l'utilisateur authentifié
        if (! isset($this->family_members) || $this->family_members->isEmpty()) {
            return collect([auth()->user()]);
        }

        // S'assurer que l'utilisateur authentifié est dans la liste s'il n'y est pas déjà
        $members = clone $this->family_members;
        if (! $members->contains('id', auth()->id())) {
            $members->prepend(auth()->user());
        }

        return $members;
    }

    public function initializeUserShares(): void
    {
        if (! isset($this->form->user_shares)) {
            $this->form->user_shares = [];

            return;
        }

        $sharesById = [];
        foreach ($this->form->user_shares as $share) {
            if (isset($share['id'])) {
                $sharesById[$share['id']] = $share;
            }
        }

        $this->form->user_shares = array_values($sharesById);
        $this->calculateRemainingShares();
    }

    public function getShareDetailSummary(Collection $familyMembers): array
    {
        if (! isset($this->form->amount) || empty($this->form->amount) || $this->form->amount <= 0) {
            return [
                'hasAmount' => false,
                'hasDetails' => false,
                'formattedShared' => 0,
                'totalPercentage' => 0,
                'memberDetails' => [],
            ];
        }

        $userShares = $this->form->user_shares ?? [];

        if (empty($userShares)) {
            $payerId = $this->form->paid_by_user_id ?? auth()->id();
            $payer = null;

            if (! $familyMembers->isEmpty()) {
                $payer = $familyMembers->firstWhere('id', $payerId);
            }

            if (! $payer) {
                $payer = $payerId === auth()->id()
                    ? auth()->user()
                    : User::find($payerId) ?? auth()->user();
            }

            return $this->getArr($payer);
        }

        $memberDetails = [];
        $totalPercentage = 0;

        $membersById = $familyMembers->keyBy('id');

        if (! isset($membersById[auth()->id()])) {
            $membersById[auth()->id()] = auth()->user();
        }

        foreach ($userShares as $share) {
            if (! isset($share['id'])) {
                continue;
            }

            // Récupérer le membre à partir du mapping
            $member = $membersById[$share['id']] ?? null;

            if (! $member) {
                // Si non trouvé dans le mapping, chercher l'utilisateur
                if ($share['id'] == auth()->id()) {
                    $member = auth()->user();
                } else {
                    // Charger l'utilisateur si nécessaire (rare)
                    $member = User::find($share['id']);
                    if (! $member) {
                        continue;
                    }
                }
            }

            $percentage = isset($share['percentage']) && $share['percentage'] > 0
                ? $share['percentage']
                : (isset($share['amount']) && $this->form->amount > 0
                    ? ($share['amount'] / $this->form->amount) * 100
                    : 0);

            $amount = isset($share['amount']) && $share['amount'] > 0
                ? $share['amount']
                : ($this->form->amount * ($percentage / 100));

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
            'formattedShared' => number_format((float) $this->form->amount, 2, ',', ' '),
            'totalPercentage' => $totalPercentage,
            'memberDetails' => $memberDetails,
        ];
    }

    public function getCurrencySymbol(): string
    {
        if (! isset($this->form->currency)) {
            return '€';
        }

        try {
            $currencyEnum = CurrencyEnum::tryFrom($this->form->currency);

            return $currencyEnum?->symbol() ?? '€';
        } catch (\Exception $e) {
            return '€';
        }
    }

    public function getArr(User|Authenticatable|null $authUser): array
    {
        return [
            'hasAmount' => true,
            'hasDetails' => true,
            'formattedShared' => number_format((float) $this->form->amount, 2, ',', ' '),
            'totalPercentage' => 100,
            'memberDetails' => [
                [
                    'id' => $authUser->id,
                    'name' => $authUser->name,
                    'avatar' => $authUser->avatar_url ?? asset('img/img_placeholder.jpg'),
                    'formattedAmount' => number_format((float) $this->form->amount, 2, ',', ' '),
                    'sharePercentage' => 100,
                    'formattedPercentage' => number_format(100, 0),
                    'isPayer' => true,
                ],
            ],
        ];
    }
}
