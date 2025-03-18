<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class Calendar extends Component
{
    public function render()
    {
        return view('livewire.pages.calendar')
            ->layout('layouts.app-sidebar');
    }
}
