<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class Dashboard extends Component
{
    public $showModal = false;

    public $isSidebarOpen = false;

    public function toggleSidebar()
    {
        $this->isSidebarOpen = !$this->isSidebarOpen;
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function render()
    {
        return view('livewire.pages.dashboard')->layout('layouts.app');
    }
}
