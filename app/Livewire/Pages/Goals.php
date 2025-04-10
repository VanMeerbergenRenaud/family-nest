<?php

namespace App\Livewire\Pages;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Objectifs')]
class Goals extends Component
{
    public bool $showGoalExempleModal;

    public function showGoalExemple(): void
    {
        $this->showGoalExempleModal = true;
    }

    public function render()
    {
        return view('livewire.pages.goals')
            ->layout('layouts.app-sidebar');
    }
}
