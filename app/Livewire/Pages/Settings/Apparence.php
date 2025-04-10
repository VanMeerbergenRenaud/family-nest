<?php

namespace App\Livewire\Pages\Settings;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Réglages d\'apparence')]
class Apparence extends Component
{
    public function render()
    {
        return view('livewire.pages.settings.apparence')
            ->layout('layouts.app-sidebar');
    }
}
