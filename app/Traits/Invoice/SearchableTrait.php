<?php

namespace App\Traits\Invoice;

use Livewire\Attributes\Url;

trait SearchableTrait
{
    #[Url]
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    protected function applySearch($query)
    {
        return $this->search === ''
                 ? $query
                 : $query->where(function ($query) {
                     $searchTerm = strtolower($this->search);
                     $query->whereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"])
                         ->orWhereRaw('LOWER(reference) LIKE ?', ["%{$searchTerm}%"]);
                 });
    }
}
