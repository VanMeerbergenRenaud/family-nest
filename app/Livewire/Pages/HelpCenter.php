<?php

namespace App\Livewire\Pages;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Centre d\'aide')]
class HelpCenter extends Component
{
    public function render()
    {
        return view('livewire.pages.help-center')
            ->layout('layouts.app-sidebar');
    }
}
