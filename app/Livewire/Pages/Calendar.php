<?php

namespace App\Livewire\Pages;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Calendrier')]
class Calendar extends Component
{
    public bool $showCalendarExempleModal;

    public function showCalendarExemple(): void
    {
        $this->showCalendarExempleModal = true;
    }

    public function render()
    {
        return view('livewire.pages.calendar')
            ->layout('layouts.app-sidebar');
    }
}
