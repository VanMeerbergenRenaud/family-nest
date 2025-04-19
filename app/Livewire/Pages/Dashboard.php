<?php

namespace App\Livewire\Pages;

use App\Models\Invoice;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Tableau de bord')]
class Dashboard extends Component
{
    public $user;

    public $family;

    public $allInvoicesOfFamily = [];

    public $allInvoicesOfUser = [];

    public $allInvoicesOfOtherUsers = [];

    public bool $showDashboardExempleModal = false;

    public function mount()
    {
        $this->user = auth()->user();
        $this->family = $this->user->family();

        if ($this->family) {
            $this->allInvoicesOfFamily = Invoice::where('family_id', $this->family->id)
                ->get();
        }

        $this->allInvoicesOfUser = Invoice::where('family_id', $this->family->id)
            ->where('user_id', $this->user->id)
            ->get();

        $this->allInvoicesOfOtherUsers = Invoice::where('family_id', $this->family->id)
            ->where('user_id', '!=', $this->user->id)
            ->get();
    }

    public function showDashboardExemple(): void
    {
        $this->showDashboardExempleModal = true;
    }

    public function render()
    {
        return view('livewire.pages.dashboard')
            ->layout('layouts.app-sidebar');
    }
}
