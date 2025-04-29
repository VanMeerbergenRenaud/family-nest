<?php

namespace App\Livewire\Pages\Dashboard;

use App\Models\Family;
use App\Traits\InvoiceTableTrait;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Tableau de bord')]
class Index extends Component
{
    use InvoiceTableTrait;

    public Family $family;

    public Filters $filters;

    public function mount()
    {
        $user = auth()->user();
        $this->family = $user->family();
        $this->filters->init($user);
        $this->mountInvoiceTableTrait();
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
