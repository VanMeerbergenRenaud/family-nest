<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class Dashboard extends Component
{
    public $user;

    public bool $showDashboardExempleModal;

    public function mount()
    {
        $this->user = auth()->user();
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
