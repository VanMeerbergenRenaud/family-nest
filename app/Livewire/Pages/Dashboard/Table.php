<?php

namespace App\Livewire\Pages\Dashboard;

use App\Models\Invoice;
use App\Traits\ColumnPreferencesTrait;
use App\Traits\HumanDateTrait;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use ColumnPreferencesTrait, HumanDateTrait, Searchable, Sortable, WithPagination;

    #[Reactive]
    public Filters $filters;

    public array $selectedInvoiceIds = [];

    // Cette propriété sera réactive grâce à la méthode computed
    public array $invoiceIdsOnPage = [];

    // Surveillance des changements de filtres pour reset la pagination
    public function updated($property)
    {
        if (str_starts_with($property, 'filters.') || $property === 'search') {
            $this->resetPage();
            $this->selectedInvoiceIds = [];
        }
    }

    #[Computed]
    public function invoices()
    {
        $query = $this->filters->getBaseQuery();
        $query = $this->filters->apply($query);
        $query = $this->applySearch($query);
        $query = $this->applySorting($query);

        return $query->paginate(10);
    }

    public function render()
    {
        $invoices = $this->invoices;

        // Mettre à jour les IDs sur la page actuelle
        $this->invoiceIdsOnPage = $invoices->map(fn ($invoice) => (string) $invoice->id)->toArray();

        return view('livewire.pages.dashboard.table', compact('invoices'));
    }
}
