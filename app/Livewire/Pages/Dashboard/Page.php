<?php

namespace App\Livewire\Pages\Dashboard;

use App\Models\Family;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Tableau de bord')]
class Page extends Component
{
    public Family $family;

    public Filters $filters;

    // Variable pour forcer le rafraîchissement des composants
    public $refreshCounter = 0;

    public function mount()
    {
        $this->family = auth()->user()->family();
        $this->filters->init(auth()->user());
    }

    public function resetFilters(): void
    {
        $this->filters->resetAllFilters();
        $this->refreshCounter++;
    }

    #[On('refresh-dashboard-components')]
    public function refreshComponents(): void
    {
        $this->refreshCounter++;
    }

    public function render()
    {
        return view('livewire.pages.dashboard.page')
            ->layout('layouts.app-sidebar');
    }
}
