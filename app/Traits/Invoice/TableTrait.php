<?php

namespace App\Traits\Invoice;

use Livewire\WithPagination;

trait TableTrait
{
    use ActionsTrait;
    use BulkActionsTrait;
    use ColumnPreferencesTrait;
    use FileUrlTrait;
    use WithPagination;

    public function mountInvoiceTableTrait(): void
    {
        $this->initializeColumnPreferences();
    }

    public function updatedInvoiceTableTrait($property): void
    {
        if (str_starts_with($property, 'filters.') || $property === 'search') {
            $this->resetPage();
            $this->selectedInvoiceIds = [];
        }
    }
}
