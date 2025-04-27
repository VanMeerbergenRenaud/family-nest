<?php

namespace App\Livewire\Pages\Dashboard;

use App\Models\Family;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Tableau de bord')]
class Index extends Component
{
    public Family $family;
    public Filters $filters;

    public function mount()
    {
        $user = auth()->user();
        $this->family = $user->family();
        $this->filters->init($user);
    }

    public function resetFilters(): void
    {
        $this->filters->resetAllFilters();
    }

    public function render()
    {
        return view('livewire.pages.dashboard.index')
            ->layout('layouts.app-sidebar');
    }
}
