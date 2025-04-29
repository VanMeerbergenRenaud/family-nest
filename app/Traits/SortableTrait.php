<?php

namespace App\Traits;

use Livewire\Attributes\Url;

trait SortableTrait
{
    #[Url]
    public string $sortCol = 'name';

    #[Url]
    public bool $sortAsc = false;

    public string $sortField = 'name';

    public string $sortDirection = 'desc';

    public $activeFilter = null;

    public $availableFilters = [
        'name_asc' => 'Par ordre alphabétique (A-Z)',
        'name_desc' => 'Par ordre alphabétique (Z-A)',
        'issued_date_asc' => 'Date d\'ajout (plus ancien)',
        'issued_date_desc' => 'Date d\'ajout (plus récent)',
        'payment_due_date_asc' => 'Date de paiement (plus ancien)',
        'payment_due_date_desc' => 'Date de paiement (plus récent)',
        'amount_asc' => 'Montant (du moins cher au plus cher)',
        'amount_desc' => 'Montant (du plus cher au moins cher)',
    ];

    public function mountSortableTrait(): void
    {
        $this->sortField = $this->sortCol;
        $this->sortDirection = $this->sortAsc ? 'asc' : 'desc';
    }

    public function updatedSortCol(): void
    {
        $this->sortField = $this->sortCol;
    }

    public function updatedSortAsc(): void
    {
        $this->sortDirection = $this->sortAsc ? 'asc' : 'desc';
    }

    public function updatedSortField(): void
    {
        $this->sortCol = $this->sortField;
    }

    public function updatedSortDirection(): void
    {
        $this->sortAsc = $this->sortDirection === 'asc';
    }

    public function sortBy(string $column): void
    {
        $this->activeFilter = null;

        if ($this->sortCol === $column) {
            $this->sortAsc = ! $this->sortAsc;
            $this->sortDirection = $this->sortAsc ? 'asc' : 'desc';
        } else {
            $this->sortCol = $column;
            $this->sortField = $column;
            $this->sortAsc = true;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function applyFilter($filter): void
    {
        if (empty($filter)) {
            $this->activeFilter = null;
            $this->resetSort();

            return;
        }

        $this->activeFilter = $filter;

        $parts = explode('_', $filter);
        $direction = array_pop($parts);
        $field = implode('_', $parts);

        $this->sortField = $field;
        $this->sortCol = $field;
        $this->sortDirection = $direction;
        $this->sortAsc = $direction === 'asc';

        $this->resetPage();
    }

    public function resetSort(): void
    {
        $this->sortCol = 'name';
        $this->sortField = 'name';
        $this->sortAsc = false;
        $this->sortDirection = 'desc';
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
