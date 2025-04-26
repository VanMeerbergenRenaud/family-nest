<?php

namespace App\Livewire\Pages\Dashboard;

use Livewire\Attributes\Url;

trait Sortable
{
    #[Url]
    public $sortCol = 'name';

    #[Url]
    public $sortAsc = false;

    public function sortBy($column): void
    {
        if ($this->sortCol === $column) {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortCol = $column;
            $this->sortAsc = false;
        }
    }

    protected function applySorting($query)
    {
        if ($this->sortCol) {
            $column = match ($this->sortCol) {
                'name' => 'name',
                'type' => 'type',
                'payment_status' => 'payment_status',
                'payment_due_date' => 'payment_due_date',
                'amount' => 'amount',
            };

            $query->orderBy($column, $this->sortAsc ? 'asc' : 'desc');
        }

        return $query;
    }
}
