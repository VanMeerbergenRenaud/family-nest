<?php

namespace App\Livewire\Pages\Dashboard;

use App\Traits\ColumnPreferencesTrait;
use App\Traits\HumanDateTrait;
use App\Traits\InvoiceTableTrait;
use App\Traits\SearchableTrait;
use App\Traits\SortableTrait;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class Table extends Component
{
    use ColumnPreferencesTrait,
        HumanDateTrait,
        InvoiceTableTrait,
        SearchableTrait,
        SortableTrait;

    #[Reactive]
    public Filters $filters;

    public function mount()
    {
        $this->mountSortableTrait();
        $this->mountInvoiceTableTrait();
    }

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
