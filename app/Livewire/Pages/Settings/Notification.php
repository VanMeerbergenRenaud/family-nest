<?php

namespace App\Livewire\Pages\Settings;

use Livewire\Component;

class Notification extends Component
{
    public function render()
    {
        return view('livewire.pages.settings.notification')
            ->layout('layouts.app-sidebar');
    }
}
