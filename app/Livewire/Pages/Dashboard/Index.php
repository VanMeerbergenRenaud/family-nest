<?php

namespace App\Livewire\Pages\Dashboard;

use App\Models\Family;
use App\Models\Invoice;
use App\Traits\Invoice\ActionsTrait;
use App\Traits\Invoice\BulkActionsTrait;
use App\Traits\Invoice\ColumnPreferencesTrait;
use App\Traits\Invoice\FileUrlTrait;
use App\Traits\Invoice\StateCheckTrait;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Tableau de bord')]
class Index extends Component
{
    use ActionsTrait;
    use BulkActionsTrait;
    use ColumnPreferencesTrait;
    use FileUrlTrait;
    use StateCheckTrait;
    use WithPagination;

    public ?Family $family = null;

    public Filters $filters;

    public function mount()
    {
        $user = auth()->user();

        if ($this->hasFamily()) {
            $this->family = $this->getUserFamily();
            $this->filters->init($user);
            $this->filters->family_member = 'all';
        }
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
