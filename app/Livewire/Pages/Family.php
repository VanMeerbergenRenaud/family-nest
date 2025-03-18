<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class Family extends Component
{
    public function render()
    {
        return view('livewire.pages.family')
            ->layout('layouts.app-sidebar');
    }
}
