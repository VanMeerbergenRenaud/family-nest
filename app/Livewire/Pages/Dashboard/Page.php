<?php

namespace App\Livewire\Pages\Dashboard;

use App\Models\Family;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Tableau de bord')]
class Page extends Component
{
    public Family $family;

    public Filters $filters;

    public function mount()
    {
        $this->family = auth()->user()->family();
        $this->filters->init(auth()->user());
    }

    public function render()
    {
        return view('livewire.pages.dashboard.page')
            ->layout('layouts.app-sidebar');
    }
}
