<?php

namespace App\Livewire\Pages\Settings;

use Livewire\Component;

class Apparence extends Component
{
    public function render()
    {
        return view('livewire.pages.settings.apparence')
            ->layout('layouts.app-sidebar');
    }
}
