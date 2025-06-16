<?php

namespace App\Livewire\Pages\Settings;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Réglages de stockage')]
class Storage extends Component
{
    public function render()
    {
        return view('livewire.pages.settings.storage')
            ->layout('layouts.app-sidebar');
    }
}
