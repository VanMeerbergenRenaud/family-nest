<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class Themes extends Component
{
    public bool $showThemeExempleModal;

    public function showThemeExemple(): void
    {
        $this->showThemeExempleModal = true;
    }

    public function render()
    {
        return view('livewire.pages.themes')
            ->layout('layouts.app-sidebar');
    }
}
