<?php

namespace App\Livewire\Pages;

use Livewire\Component;

class HelpCenter extends Component
{
    public function render()
    {
        return view('livewire.pages.help-center')
            ->layout('layouts.app-sidebar');
    }
}
