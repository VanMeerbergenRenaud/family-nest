<?php

namespace App\Traits\Invoice;

use Livewire\Attributes\Url;

trait SortableTrait
{
    #[Url]
    public string $sortCol = 'name';

    #[Url]
    public bool $sortAsc = false;

    public $activeFilter = null;

    public array $availableFilters = [
        'name_asc' => 'Nom (A → Z)',
        'name_desc' => 'Nom (Z → A)',
        'issued_date_asc' => 'Date d\'émission (ancienne)',
        'issued_date_desc' => 'Date d\'émission (récente)',
        'payment_due_date_asc' => 'Date d\'échéance (ancienne)',
        'payment_due_date_desc' => 'Date d\'échéance (récente)',
        'amount_asc' => 'Montant (croissant)',
        'amount_desc' => 'Montant (décroissant)',
    ];

    public function sortBy(string $column): void
    {
        $this->activeFilter = null;

        if ($this->sortCol === $column) {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortCol = $column;
            $this->sortAsc = true;
        }

        $this->resetPage();
    }

    public function applyFilter($filter): void
    {
        if (empty($filter)) {
            $this->resetSort();

            return;
        }

        $this->activeFilter = $filter;

        $parts = explode('_', $filter);
        $direction = array_pop($parts);
        $field = implode('_', $parts);

        $this->sortCol = $field;
        $this->sortAsc = $direction === 'asc';

        $this->resetPage();
    }

    public function resetSort(): void
    {
        $this->sortCol = 'name';
        $this->sortAsc = false;
        $this->activeFilter = null;

        $this->resetPage();
    }

    protected function applySorting($query)
    {
        if (! $this->sortCol) {
            return $query;
        }

        $validColumns = [
            'name',
            'type',
            'reference',
            'category',
            'issuer_name',
            'amount',
            'payment_status',
            'issued_date',
            'payment_due_date',
        ];

        if (in_array($this->sortCol, $validColumns)) {
            $query->orderBy($this->sortCol, $this->sortAsc ? 'asc' : 'desc');
        }

        return $query;
    }
}
