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
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('reference', 'like', "%{$this->search}%");
            });
    }
}
