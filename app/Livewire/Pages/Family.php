<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\WithPagination;

class Family extends Component
{
    use WithPagination;

    public function render()
    {
        $members = auth()->user()->familyMembers()
            ->where('user_id', auth()->id())
            ->orderBy('is_primary', 'desc')
            ->orderBy('name')
            ->paginate(6);

        return view('livewire.pages.family', compact('members'))
            ->layout('layouts.app-sidebar');
    }
}
