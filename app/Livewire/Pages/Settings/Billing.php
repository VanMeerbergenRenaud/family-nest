<?php

namespace App\Livewire\Pages\Settings;

use Livewire\Component;

class Billing extends Component
{
    public bool $isAnnual = false;
    public string $billingCycle = 'monthly';
    public string $selectedPlan = 'Essentiel';

    protected $listeners = [
        'selectPlan' => 'selectPlan',
    ];

    public function mount()
    {
        $this->isAnnual = $this->billingCycle === 'annual';
    }

    public function updatedBillingCycle($value): void
    {
        $this->isAnnual = $value === 'annual';
    }

    public function setBilling($cycle): void
    {
        $this->billingCycle = $cycle;
        $this->isAnnual = $cycle === 'annual';
    }

    public function selectPlan($planName): void
    {
        $this->selectedPlan = $planName;
    }

    public function render()
    {
        return view('livewire.pages.settings.billing')
            ->layout('layouts.app-sidebar');
    }
}
