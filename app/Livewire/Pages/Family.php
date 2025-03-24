<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\WithPagination;

class Family extends Component
{
    use WithPagination;

    public $sortField = 'name';

    public $sortDirection = 'asc';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function resetSort()
    {
        $this->sortField = 'name';
        $this->sortDirection = 'asc';
    }

    public function addAMember()
    {
        // À implémenter selon vos besoins
        // Peut-être rediriger vers un formulaire ou ouvrir une modal
    }

    public function render()
    {
        $family = auth()->user()->family();

        if (! $family) {
            $members = collect();
        } else {
            // Récupération de tous les membres de la famille (y compris l'utilisateur actuel)
            $members = $family->users()
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(6);
        }

        return view('livewire.pages.family', [
            'members' => $members ?? collect(),
            'family' => $family,
            'currentUser' => auth()->id(),
        ])->layout('layouts.app-sidebar');
    }
}
