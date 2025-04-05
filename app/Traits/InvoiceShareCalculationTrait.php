<?php

namespace App\Traits;

use App\Enums\CurrencyEnum;
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
                    // Ajuster le dernier pour éviter les problèmes d'arrondi
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
                    // Ajuster le dernier pour éviter les problèmes d'arrondi
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

    public function getCurrencySymbol(): string
    {
        try {
            return CurrencyEnum::tryFrom($this->form->currency ?? 'EUR')?->symbol() ?? '€';
        } catch (ValueError) {
            return $this->form->currency ?? '€';
        }
    }

    public function getShareDetailSummary($familyMembers): array
    {
        $formAmount = floatval($this->form->amount ?? 0);

        if (empty($formAmount)) {
            return ['hasAmount' => false];
        }

        // Initialiser les informations du payeur
        $payer = $this->getPayerInfo($familyMembers);

        // Calculer les totaux
        [$totalShared, $totalPercentage] = $this->calculateShareTotals();

        $remainingAmount = $formAmount - $totalShared;
        $remainingPercentage = 100 - $totalPercentage;

        $isFullyShared = abs($totalPercentage - 100) < 0.1 || abs($totalShared - $formAmount) < 0.01;
        $isOverShared = $totalPercentage > 100.1 || $totalShared > ($formAmount + 0.01);

        // Construire les détails des membres
        $memberDetails = $this->buildMemberDetails($familyMembers);

        return [
            'hasAmount' => true,
            'payer' => $payer,
            'totalShared' => $totalShared,
            'totalPercentage' => $totalPercentage,
            'isFullyShared' => $isFullyShared,
            'isOverShared' => $isOverShared,
            'remainingAmount' => $remainingAmount,
            'remainingPercentage' => $remainingPercentage,
            'formattedTotal' => Number::format($formAmount, 2, locale: 'fr_FR'),
            'formattedShared' => Number::format($totalShared, 2, locale: 'fr_FR'),
            'formattedRemaining' => Number::format(abs($remainingAmount), 2, locale: 'fr_FR'),
            'memberDetails' => $memberDetails,
            'hasDetails' => ! empty($memberDetails),
            'currencySymbol' => $this->getCurrencySymbol(),
        ];
    }

    private function getPayerInfo($familyMembers): array
    {
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
            $payer['name'] = $this->form->issuer_name ?? 'Non spécifié';
        }

        return $payer;
    }

    private function calculateShareTotals(): array
    {
        $totalShared = $totalPercentage = 0;

        foreach ($this->form->user_shares ?? [] as $share) {
            $totalShared += floatval($share['amount'] ?? 0);
            $totalPercentage += floatval($share['percentage'] ?? 0);
        }

        return [$totalShared, $totalPercentage];
    }

    private function buildMemberDetails($familyMembers): array
    {
        $memberDetails = [];

        foreach ($this->form->user_shares ?? [] as $share) {
            $memberInfo = [
                'id' => $share['id'],
                'name' => 'Membre inconnu',
                'avatar' => null,
                'sharePercentage' => floatval($share['percentage'] ?? 0),
                'shareAmount' => floatval($share['amount'] ?? 0),
                'isPayer' => (string) $share['id'] === (string) ($this->form->paid_by_user_id ?? ''),
                'formattedAmount' => Number::format(floatval($share['amount'] ?? 0), 2, locale: 'fr_FR'),
                'formattedPercentage' => Number::format(floatval($share['percentage'] ?? 0), 2, locale: 'fr_FR'),
            ];

            foreach ($familyMembers as $member) {
                if ((string) $member->id === (string) $share['id']) {
                    $memberInfo['name'] = $member->name;
                    $memberInfo['avatar'] = $member->avatar_url;
                    break;
                }
            }

            $memberDetails[] = $memberInfo;
        }

        return $memberDetails;
    }
}
