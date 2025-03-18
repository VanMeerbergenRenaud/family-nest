<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class Themes extends Component
{
    public function render()
    {
        return view('livewire.pages.themes')
            ->layout('layouts.app-sidebar');
    }
}
