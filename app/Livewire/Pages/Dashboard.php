<?php

namespace App\Livewire\Pages;

use App\Models\Invoice;
use Livewire\Component;

class Dashboard extends Component
{
    public $user;

    public $family;

    public $allInvoicesOfUser = [];

    public $allInvoicesOfFamily = [];

    public bool $showDashboardExempleModal = false;

    public function mount()
    {
        $this->user = auth()->user();
        $this->family = $this->user->family();

        // Get all the user's invoices
        $this->allInvoicesOfUser = auth()->user()->invoices()
            ->where('is_archived', false)
            ->orderBy('amount', 'desc')
            ->get();

        if ($this->family) {
            $familyMemberIds = $this->family->users->pluck('id')->toArray();

            // Get all the invoices of the family
            $this->allInvoicesOfFamily = Invoice::where('family_id', $this->family->id)
                ->orWhereIn('user_id', $familyMemberIds)
                ->where('is_archived', false)
                ->orderBy('amount', 'desc')
                ->get();
        }
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
