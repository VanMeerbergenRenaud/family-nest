<?php

namespace App\Livewire\Pages;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Politique de confidentialité')]
class PrivacyPolicy extends Component
{
    public function render()
    {
        return view('livewire.pages.privacy-policy')
            ->layout('layouts.guest');
    }
}
