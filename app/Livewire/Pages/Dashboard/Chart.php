<?php

namespace App\Livewire\Pages\Dashboard;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class Chart extends Component
{
    public User $user;

    #[Reactive]
    public Filters $filters;

    public array $dataset = [];

    public function mount()
    {
        $this->user = auth()->user();
        $this->refreshData();
    }

    public function refreshData(): void
    {
        $query = $this->filters->getBaseQuery();

        // Vérifier si des factures existent avant d'appliquer les filtres
        $hasInvoices = $query->count() > 0;

        if (! $hasInvoices) {
            $this->dataset = [
                'values' => [],
                'labels' => [],
            ];

            return;
        }

        $query = $this->filters->apply($query);

        $results = $query->select('type', DB::raw('SUM(amount) as total'))
            ->groupBy('type')
            ->orderBy('total', 'desc')
            ->get();

        // Si les résultats sont vides après application des filtres
        if ($results->isEmpty()) {
            $this->dataset = [
                'values' => [],
                'labels' => [],
            ];

            return;
        }

        $this->dataset = [
            'values' => $results->pluck('total')->toArray(),
            'labels' => $results->pluck('type')->toArray(),
        ];
    }

    #[Computed]
    public function shouldRefresh(): array
    {
        return [
            'status' => $this->filters->status,
            'family_member' => $this->filters->family_member,
            'range' => $this->filters->range,
            'start' => $this->filters->start,
            'end' => $this->filters->end,
        ];
    }

    public function render()
    {
        if ($this->shouldRefresh) {
            $this->refreshData();
        }

        return view('livewire.pages.dashboard.chart');
    }
}
