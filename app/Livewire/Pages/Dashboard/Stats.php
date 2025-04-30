<?php

namespace App\Livewire\Pages\Dashboard;

use App\Enums\PaymentStatusEnum;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class Stats extends Component
{
    #[Reactive]
    public Filters $filters;

    #[Computed]
    public function statistics(): array
    {
        $baseQuery = $this->filters->getBaseQuery();
        $filteredQuery = $this->filters->apply($baseQuery);

        // Dates importantes pour les calculs
        $now = Carbon::now();
        $nextWeek = $now->copy()->addDays(7);

        // Factures qui arrivent à échéance dans les 7 prochains jours
        $upcomingCount = clone $filteredQuery;
        $upcomingCount = $upcomingCount
            ->where('payment_status', '!=', PaymentStatusEnum::Paid->value)
            ->whereBetween('payment_due_date', [$now, $nextWeek])
            ->count();

        // Montant total des factures impayées avec une échéance dépassée
        $overdueQuery = clone $filteredQuery;
        $overdueAmount = $overdueQuery
            ->where('payment_status', '!=', PaymentStatusEnum::Paid->value)
            ->where('payment_due_date', '<', $now)
            ->sum('amount');

        // Montant le plus élevé parmi les factures
        $maxInvoiceQuery = clone $filteredQuery;
        $maxInvoice = $maxInvoiceQuery->orderBy('amount', 'desc')->first();
        $maxAmount = $maxInvoice ? $maxInvoice->amount : 0;

        // Factures à échéance ce mois-ci
        $thisMonthStart = $now->copy()->startOfMonth();
        $thisMonthEnd = $now->copy()->endOfMonth();
        $thisMonthQuery = clone $filteredQuery;
        $thisMonthAmount = $thisMonthQuery
            ->whereBetween('payment_due_date', [$thisMonthStart, $thisMonthEnd])
            ->sum('amount');

        // Calcul des statistiques standards
        $totalAmount = $filteredQuery->sum('amount') ?: 0;
        $invoiceCount = $filteredQuery->count() ?: 0;
        $paidAmount = clone $filteredQuery;
        $paidAmount = $paidAmount
            ->where('payment_status', PaymentStatusEnum::Paid->value)
            ->sum('amount');
        $paidPercentage = $totalAmount > 0 ? round(($paidAmount / $totalAmount) * 100) : 0;

        // Répartition par statut de paiement
        $statusDistribution = [];
        foreach (PaymentStatusEnum::cases() as $status) {
            $statusQuery = clone $filteredQuery;
            $count = $statusQuery->where('payment_status', $status->value)->count();
            if ($count > 0) {
                $statusDistribution[$status->label()] = [
                    'count' => $count,
                    'percentage' => $invoiceCount > 0 ? round(($count / $invoiceCount) * 100) : 0,
                    'color' => $status->color(),
                    'emoji' => $status->emoji(),
                ];
            }
        }

        return [
            'totalAmount' => $totalAmount,
            'paidAmount' => $paidAmount,
            'paidPercentage' => $paidPercentage,
            'invoiceCount' => $invoiceCount,
            'upcomingDueCount' => $upcomingCount,
            'overdueAmount' => $overdueAmount,
            'maxAmount' => $maxAmount,
            'thisMonthAmount' => $thisMonthAmount,
            'statusDistribution' => $statusDistribution,
        ];
    }

    public function render()
    {
        return view('livewire.pages.dashboard.stats');
    }
}
