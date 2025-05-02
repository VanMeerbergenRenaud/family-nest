<?php

namespace App\Livewire\Pages\Invoices\Index;

use App\Livewire\Pages\Dashboard\Filters;
use App\Traits\InvoiceShareCalculationTrait;
use App\Traits\InvoiceStateCheckTrait;
use App\Traits\InvoiceTableTrait;
use App\Traits\SearchableTrait;
use App\Traits\SortableTrait;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Factures')]
class Page extends Component
{
    use InvoiceFolderTrait;
    use InvoiceShareCalculationTrait;
    use InvoiceStateCheckTrait;
    use InvoiceTableTrait;
    use SearchableTrait;
    use SortableTrait;
    use WithPagination;

    public $is_archived = false;

    public bool $showSidebarInvoiceDetails = false;

    public $family_members = [];

    public Filters $filters;

    public function mount()
    {
        if (! $this->hasFamily()) {
            return;
        }

        $this->mountInvoiceTableTrait();
        $this->mountSortableTrait();

        $user = auth()->user();
        $this->filters->init($user);
    }

    public function showSidebarInvoice($id): void
    {
        $this->invoice = auth()->user()->invoices()
            ->with(['file', 'sharedUsers'])
            ->findOrFail($id);

        $this->loadFamilyMembers();
        $this->showSidebarInvoiceDetails = true;
    }

    public function loadFamilyMembers(): void
    {
        $family = auth()->user()->family();
        if ($family) {
            $this->family_members = $family->users()->get();
        } else {
            $this->family_members = collect([auth()->user()]);
        }
    }

    public function toggleSidebar(): void
    {
        $this->showSidebarInvoiceDetails = ! $this->showSidebarInvoiceDetails;
    }

    public function render()
    {
        $query = auth()->user()->invoices()
            ->with(['file', 'sharedUsers'])
            ->where('is_archived', false);

        $query->orderBy($this->sortField, $this->sortDirection);

        $query = $this->applySearch($query);

        $invoices = $query->paginate(8);

        $recentInvoices = auth()->user()->invoices()
            ->with(['file', 'sharedUsers'])
            ->where('is_archived', false)
            ->orderBy('updated_at', 'desc')
            ->limit(8)
            ->get();

        $archivedInvoices = auth()->user()->invoices()
            ->with(['file', 'sharedUsers'])
            ->where('is_archived', true)
            ->get();

        $folderStats = $this->getFolderStats();

        $this->invoiceIdsOnPage = $invoices->map(fn ($invoice) => (string) $invoice->id)->toArray();

        return view('livewire.pages.invoices.index', [
            'invoices' => $invoices,
            'recentInvoices' => $recentInvoices,
            'folderStats' => $folderStats,
            'archivedInvoices' => $archivedInvoices,
        ])->layout('layouts.app-sidebar');
    }
}
