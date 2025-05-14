<?php

namespace App\Livewire\Pages\Invoices;

use App\Livewire\Pages\Dashboard\Filters;
use App\Traits\HumanDateTrait;
use App\Traits\Invoice\ActionsTrait;
use App\Traits\Invoice\BulkActionsTrait;
use App\Traits\Invoice\ColumnPreferencesTrait;
use App\Traits\Invoice\FileUrlTrait;
use App\Traits\Invoice\SearchableTrait;
use App\Traits\Invoice\SortableTrait;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Composant dynamique utilisé à la fois pour le dashboard et la page des factures
 */
class InvoiceTable extends Component
{
    use ActionsTrait,
        BulkActionsTrait,
        ColumnPreferencesTrait,
        ColumnPreferencesTrait,
        FileUrlTrait,
        HumanDateTrait,
        SearchableTrait,
        SortableTrait,
        WithPagination;

    #[Reactive]
    public Filters $filters;

    public int $perPage = 8;

    public $perPageOptions = [8, 15, 25, 50, 100];

    public bool $withFilters = true;

    public function mount()
    {
        if (! isset($this->filters)) {
            $this->filters->init(auth()->user());
        }

        $this->detectDevice();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
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
