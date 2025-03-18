<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class Goals extends Component
{
    public function render()
    {
        return view('livewire.pages.goals')
            ->layout('layouts.app-sidebar');
    }
}
