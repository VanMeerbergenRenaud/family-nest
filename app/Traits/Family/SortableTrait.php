<?php

namespace App\Traits\Family;

use Livewire\Attributes\Url;

trait SortableTrait
{
    #[Url]
    public string $sortCol = 'name';

    #[Url]
    public bool $sortAsc = true;

    public function sortBy(string $column): void
    {
        if ($this->sortCol === $column) {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortCol = $column;
            $this->sortAsc = true;
        }
    }

    protected function applySorting($query)
    {
        if (! $this->sortCol) {
            return $query;
        }

        $validColumns = [
            'name',
            'permission',
            'relation',
        ];

        if (in_array($this->sortCol, $validColumns)) {
            $query->orderBy($this->sortCol, $this->sortAsc ? 'asc' : 'desc');
        }

        return $query;
    }
}
