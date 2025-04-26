<?php

namespace App\Livewire\Pages\Dashboard;

use App\Models\Invoice;
use App\Enums\PaymentStatusEnum;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class Stats extends Component
{
    #[Reactive]
    public Filters $filters;

    public $totalStats = [
        'totalAmount' => 0,
        'invoiceCount' => 0,
        'paidInvoices' => 0,
        'averageAmount' => 0,
    ];

    #[On('statusChanged')]
    public function refreshData(): void
    {
        $this->calculateStats();
    }

    #[On('familyMemberChanged')]
    public function refreshDataForFamilyMember(): void
    {
        $this->calculateStats();
    }

    public function mount()
    {
        $this->calculateStats();
    }

    public function calculateStats(): void
    {
        $user = auth()->user();

        $currentPeriodQuery = $this->getBaseQuery($user);
        $currentPeriodQuery = $this->filters->applyStatus($currentPeriodQuery);

        $currentTotal = $currentPeriodQuery->sum('amount') ?: 0;
        $currentCount = $currentPeriodQuery->count() ?: 0;
        $currentPaid = $currentPeriodQuery->where('payment_status', PaymentStatusEnum::Paid->value)->count() ?: 0;
        $currentAverage = $currentCount > 0 ? $currentTotal / $currentCount : 0;

        $this->totalStats = [
            'totalAmount' => $currentTotal,
            'invoiceCount' => $currentCount,
            'paidInvoices' => $currentPaid,
            'averageAmount' => $currentAverage,
        ];
    }

    private function getBaseQuery($user)
    {
        $now = now();
        $startDate = false ? $now->copy()->subDays(60) : $now->copy()->subDays(30);
        $endDate = false ? $now->copy()->subDays(30) : $now;

        if ($this->filters->family_member === 'all') {
            $family = $user->family();
            if ($family) {
                $query = Invoice::where('family_id', $family->id);
            } else {
                $query = $user->invoices();
            }
        } else {
            if ($this->filters->family_member == $user->id) {
                $query = $user->invoices();
            } else {
                $query = Invoice::where('user_id', $this->filters->family_member);
            }
        }

        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function render()
    {
        return view('livewire.pages.dashboard.stats');
    }
}
