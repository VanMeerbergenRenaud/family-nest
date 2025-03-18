<?php

namespace App\Livewire\Pages\Settings;

use Livewire\Component;

class Billing extends Component
{
    public function render()
    {
        return view('livewire.pages.settings.billing')
            ->layout('layouts.app-sidebar');
    }
}
