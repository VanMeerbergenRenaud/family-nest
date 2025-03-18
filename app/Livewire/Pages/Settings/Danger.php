<?php

namespace App\Livewire\Pages\Settings;

use Livewire\Component;

class Danger extends Component
{
    public function render()
    {
        return view('livewire.pages.settings.danger')
            ->layout('layouts.app-sidebar');
    }
}
