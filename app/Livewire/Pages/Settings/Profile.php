<?php

namespace App\Livewire\Pages\Settings;

use Livewire\Component;

class Profile extends Component
{
    public function render()
    {
        return view('livewire.pages.settings.profile')
            ->layout('layouts.app-sidebar');
    }
}
