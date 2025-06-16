<?php

namespace App\Livewire\Pages\Settings;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Réglages des notifications')]
class Notification extends Component
{
    public function render()
    {
        return view('livewire.pages.settings.notification')
            ->layout('layouts.app-sidebar');
    }
}
