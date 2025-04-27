<?php

namespace App\Livewire\Pages\Dashboard;

use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

trait Sortable
{
    #[Url]
    public string $sortCol = 'name';

    #[Url]
    public bool $sortAsc = false;

    public function sortBy(string $column): void
    {
        $this->sortAsc = $this->sortCol === $column && !$this->sortAsc;
        $this->sortCol = $column;
    }

    protected function applySorting($query)
    {
        if (!$this->sortCol) {
            return $query;
        }

        $validColumns = [
            'name', 'type', 'category', 'issuer_name',
            'amount', 'payment_status', 'issued_date', 'payment_due_date'
        ];

        if (in_array($this->sortCol, $validColumns)) {
            $query->orderBy($this->sortCol, $this->sortAsc ? 'asc' : 'desc');
        }

        return $query;
    }
}
