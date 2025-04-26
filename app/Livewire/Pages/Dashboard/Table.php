<?php

namespace App\Livewire\Pages\Dashboard;

use App\Models\Invoice;
use App\Traits\HumanDateTrait;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use Searchable, Sortable, WithPagination, HumanDateTrait;

    #[Reactive]
    public Filters $filters;

    public $selectedInvoiceIds = [];

    public $invoiceIdsOnPage = [];

    #[On('statusChanged')]
    public function resetPageAndRefresh(): void
    {
        $this->resetPage();
        $this->selectedInvoiceIds = [];
    }

    #[On('familyMemberChanged')]
    public function resetPageAndRefreshForFamilyMember(): void
    {
        $this->resetPage();
        $this->selectedInvoiceIds = [];
    }

    #[On('rangeChanged')]
    public function resetPageAndRefreshForRange(): void
    {
        $this->resetPage();
        $this->selectedInvoiceIds = [];
    }

    public function render()
    {
        $user = auth()->user();

        if ($this->filters->family_member === 'all') {
            $family = $user->family();
            if ($family) {
                $query = Invoice::where('family_id', $family->id);
            } else {
                $query = $user->invoices();
            }
        } else {
            if ($this->filters->family_member == $user->id) {
                $query = $user->invoices();
            } else {
                $query = Invoice::where('user_id', $this->filters->family_member);
            }
        }

        $query = $this->filters->applyStatus($query);
        $query = $this->applySearch($query);
        $query = $this->filters->applyRange($query);
        $query = $this->applySorting($query);

        $invoices = $query->paginate(10);

        $this->invoiceIdsOnPage = $invoices->map(
            fn ($invoice) => (string) $invoice->id
        )->toArray();

        return view('livewire.pages.dashboard.table', compact('invoices'));
    }
}
