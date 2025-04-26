<?php

namespace App\Livewire\Pages\Dashboard;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class Chart extends Component
{
    public User $user;

    #[Reactive]
    public Filters $filters;

    public $dataset = [];

    public function mount()
    {
        $this->user = auth()->user();
        $this->fillDataset();
    }

    #[On('statusChanged')]
    public function refreshData(): void
    {
        $this->fillDataset();
    }

    #[On('familyMemberChanged')]
    public function refreshDataForFamilyMember(): void
    {
        $this->fillDataset();
    }

    public function fillDataset(): void
    {
        if ($this->filters->family_member === 'all') {
            $family = $this->user->family();
            if ($family) {
                $query = \App\Models\Invoice::where('family_id', $family->id);
            } else {
                $query = $this->user->invoices();
            }
        } else {
            if ($this->filters->family_member == $this->user->id) {
                $query = $this->user->invoices();
            } else {
                $query = \App\Models\Invoice::where('user_id', $this->filters->family_member);
            }
        }

        $query = $this->filters->applyStatus($query);

        $results = $query->select('type', DB::raw('SUM(amount) as total'))
            ->groupBy('type')
            ->orderBy('total', 'desc')
            ->get();

        $this->dataset = [
            'values' => $results->pluck('total')->toArray(),
            'labels' => $results->pluck('type')->toArray(),
        ];
    }

    public function hydrate()
    {
        $this->fillDataset();
    }

    public function render()
    {
        return view('livewire.pages.dashboard.chart');
    }
}
