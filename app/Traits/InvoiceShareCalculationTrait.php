<?php

namespace App\Traits;

use App\Enums\InvoiceCurrencyEnum;
use Illuminate\Support\Number;
use ValueError;

trait InvoiceShareCalculationTrait
{
    public string $shareMode = 'amount';

    public float $remainingAmount = 0;

    public float $remainingPercentage = 100;

    public function calculateRemainingShares(): void
    {
        $totalAmount = $totalPercentage = 0;

        foreach ($this->form->user_shares ?? [] as $share) {
            $totalAmount += $share['amount'] ?? 0;
            $totalPercentage += $share['percentage'] ?? 0;
        }

        $this->remainingAmount = max(0, $this->form->amount - $totalAmount);
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
            $amount = $this->form->amount;
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

        return number_format(($percentage / 100) * $this->form->amount, 2, '.', '');
    }

    private function calculatePercentageFromAmount($amount): float
    {
        if (! is_numeric($this->form->amount) || ! $this->form->amount || ! is_numeric($amount)) {
            return 0;
        }

        return number_format(($amount / $this->form->amount) * 100, 2, '.', '');
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
            $totalPercent += $share['percentage'] ?? 0;
            $totalAmount += $share['amount'] ?? 0;
        }

        return [
            'totalShares' => $totalShares,
            'totalPercent' => $totalPercent,
            'totalAmount' => $totalAmount,
            'isComplete' => $totalPercent >= 99.9 || (is_numeric($this->form->amount) && abs($this->form->amount - $totalAmount) < 0.01),
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
            if ($share['id'] == $memberId) {
                return ['hasShare' => true, 'shareIndex' => $index, 'shareData' => $share];
            }
        }

        return ['hasShare' => false, 'shareIndex' => null, 'shareData' => null];
    }

    public function getCurrencySymbol(): string
    {
        try {
            return InvoiceCurrencyEnum::from($this->form->currency ?? 'EUR')->symbol();
        } catch (ValueError) {
            return $this->form->currency ?? '€';
        }
    }

    public function getShareDetailSummary($familyMembers): array
    {
        if (empty($this->form->amount)) {
            return ['hasAmount' => false];
        }

        // Initialiser les informations du payeur
        $payer = ['name' => 'Non spécifié', 'id' => null, 'avatar' => null];

        if ($this->form->paid_by_user_id) {
            foreach ($familyMembers as $member) {
                if ($member->id == $this->form->paid_by_user_id) {
                    $payer = [
                        'name' => $member->name,
                        'id' => $member->id,
                        'avatar' => $member->avatar_url,
                    ];
                    break;
                }
            }
        } else {
            $payer['name'] = $this->form->issuer_name;
        }

        // Calculer les totaux
        $totalShared = $totalPercentage = 0;
        foreach ($this->form->user_shares ?? [] as $share) {
            $totalShared += $share['amount'] ?? 0;
            $totalPercentage += $share['percentage'] ?? 0;
        }

        $remainingAmount = $this->form->amount - $totalShared;
        $remainingPercentage = 100 - $totalPercentage;
        $isFullyShared = abs($totalPercentage - 100) < 0.1 || abs($totalShared - $this->form->amount) < 0.01;
        $isOverShared = $totalPercentage > 100.1 || $totalShared > ($this->form->amount + 0.01);

        // Construire les détails des membres
        $memberDetails = [];
        foreach ($this->form->user_shares ?? [] as $share) {
            $memberInfo = [
                'id' => $share['id'],
                'name' => 'Membre inconnu',
                'avatar' => null,
                'sharePercentage' => $share['percentage'] ?? 0,
                'shareAmount' => $share['amount'] ?? 0,
                'isPayer' => $share['id'] == $payer['id'],
                'formattedAmount' => Number::format($share['amount'] ?? 0, 2, locale: 'fr_FR'),
                'formattedPercentage' => Number::format($share['percentage'] ?? 0, 2, locale: 'fr_FR'),
            ];

            foreach ($familyMembers as $member) {
                if ($member->id == $share['id']) {
                    $memberInfo['name'] = $member->name;
                    $memberInfo['avatar'] = $member->avatar_url;
                    break;
                }
            }

            $memberDetails[] = $memberInfo;
        }

        return [
            'hasAmount' => true,
            'payer' => $payer,
            'totalShared' => $totalShared,
            'totalPercentage' => $totalPercentage,
            'isFullyShared' => $isFullyShared,
            'isOverShared' => $isOverShared,
            'remainingAmount' => $remainingAmount,
            'remainingPercentage' => $remainingPercentage,
            'formattedTotal' => Number::format($this->form->amount, 2, locale: 'fr_FR'),
            'formattedShared' => Number::format($totalShared, 2, locale: 'fr_FR'),
            'formattedRemaining' => Number::format(abs($remainingAmount), 2, locale: 'fr_FR'),
            'memberDetails' => $memberDetails,
            'hasDetails' => ! empty($memberDetails),
            'currencySymbol' => $this->getCurrencySymbol(),
        ];
    }
}
