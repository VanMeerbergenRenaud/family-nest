<?php

namespace App\Livewire\Pages\Invoices;

use App\Livewire\Pages\Dashboard\Filters;
use App\Traits\ColumnPreferencesTrait;
use App\Traits\HumanDateTrait;
use App\Traits\InvoiceTableTrait;
use App\Traits\SearchableTrait;
use App\Traits\SortableTrait;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Component;

/**
 * Composant réutilisable pour les tables d'invoices
 * Peut être utilisé à la fois par le dashboard et la page invoices
 */
class InvoiceTable extends Component
{
    use ColumnPreferencesTrait,
        HumanDateTrait,
        InvoiceTableTrait,
        SearchableTrait,
        SortableTrait;

    #[Reactive]
    public Filters $filters;

    public int $perPage = 9;

    public bool $withFilters = true;

    public function mount()
    {
        $this->mountSortableTrait();
        $this->mountInvoiceTableTrait();

        if (! isset($this->filters)) {
            $user = auth()->user();
            $this->filters->init($user);
        }
    }

    public function updated($property): void
    {
        if (str_starts_with($property, 'filters.') || $property === 'search') {
            $this->resetPage();
            $this->selectedInvoiceIds = [];
        }
    }

    #[Computed]
    public function invoices()
    {
        $query = $this->withFilters
            ? $this->filters->apply($this->filters->getBaseQuery())
            : auth()->user()->invoices()->where('is_archived', false);

        $query = $this->applySearch($query);
        $query = $this->applySorting($query);

        return $query->paginate($this->perPage);
    }

    public function render()
    {
        $invoices = $this->invoices;

        $this->invoiceIdsOnPage = $invoices->map(fn ($invoice) => (string) $invoice->id)->toArray();

        return view('livewire.pages.invoices.invoice-table', compact('invoices'));
    }
}
