<?php

namespace App\Livewire\Pages\Settings;

use Livewire\Component;

class Storage extends Component
{
    public function render()
    {
        return view('livewire.pages.settings.storage')
            ->layout('layouts.app-sidebar');
    }
}
