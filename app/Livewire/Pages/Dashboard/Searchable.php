<?php

namespace App\Livewire\Pages\Dashboard;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    public string $search = '';

    public function updatedSearchable(string $property): void
    {
        if ($property === 'search') {
            $this->resetPage();
        }
    }

    protected function applySearch($query)
    {
        return $this->search === ''
            ? $query
            : $query->where(function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('reference', 'like', "%{$this->search}%");
            });
    }
}
