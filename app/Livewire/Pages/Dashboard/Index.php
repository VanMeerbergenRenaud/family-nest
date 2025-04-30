<?php

namespace App\Livewire\Pages\Dashboard;

use App\Models\Family;
use App\Models\Invoice;
use App\Traits\InvoiceStateCheckTrait;
use App\Traits\InvoiceTableTrait;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Tableau de bord')]
class Index extends Component
{
    use InvoiceStateCheckTrait;
    use InvoiceTableTrait;

    public ?Family $family = null;

    public Filters $filters;

    public bool $showInvoiceExempleModal = false;

    public function mount()
    {
        $user = auth()->user();

        if ($this->hasFamily()) {
            $this->family = $this->getUserFamily();
            $this->filters->init($user);
            $this->filters->family_member = 'all';
        }

        $this->mountInvoiceTableTrait();
    }

    public function getInvoicesQuery(): Invoice
    {
        if (! $this->hasFamily()) {
            $this->redirectRoute('family');
        }

        $dashboardState = $this->getDashboardState();
        $query = Invoice::where('is_archived', false);

        switch ($dashboardState) {
            case 'no_invoices':
            case 'only_archived_invoices':
                // Pas de factures actives, retourner une collection vide
                return Invoice::where('id', 0);

            case 'has_active_invoices':
                if ($this->filters->family_member === 'all') {
                    $familyMemberIds = $this->getFamilyMemberIds();
                    $query->whereIn('user_id', $familyMemberIds);
                } else {
                    $query->where('user_id', $this->filters->family_member);
                }
                break;
        }

        return $query;
    }

    public function showInvoiceExemple(): void
    {
        $this->showInvoiceExempleModal = true;
    }

    public function resetFilters(): void
    {
        if ($this->hasFamily()) {
            $this->filters->resetAllFilters();
            $this->filters->family_member = 'all';
        }
    }

    public function render()
    {
        return view('livewire.pages.dashboard.index')
            ->layout('layouts.app-sidebar');
    }
}
