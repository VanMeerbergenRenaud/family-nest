<?php

namespace App\Livewire\Pages\Invoices;

use App\Livewire\Forms\InvoiceForm;
use App\Models\Invoice;
use App\Traits\InvoiceStateCheckTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

#[Title('Archives')]
class Archived extends Component
{
    use InvoiceStateCheckTrait, WithPagination;

    public InvoiceForm $form;

    public string $filterType = 'all';

    public bool $showDeleteFormModal = false;

    public bool $showDeleteAllFormModal = false;

    protected $queryString = ['filterType'];

    public function setFilterType(string $type): void
    {
        $this->filterType = $type;
        $this->resetPage();
    }

    public function restoreInvoice($invoiceId): void
    {
        $invoice = Invoice::findOrFail($invoiceId);

        if (! $this->authorizeAction('update', $invoice, 'restaurer')) {
            return;
        }

        $this->form->setFromInvoice($invoice);
        $this->form->restore()
            ? Toaster::success('Facture restaurée avec succès !')
            : Toaster::error('Erreur lors de la restauration de la facture.');
    }

    public function showDeleteInvoiceForm($id): void
    {
        $invoice = Invoice::findOrFail($id);

        if (! $this->authorizeAction('delete', $invoice, 'supprimer')) {
            return;
        }

        $this->form->setFromInvoice($invoice);
        $this->showDeleteFormModal = true;
    }

    public function deleteDefinitelyInvoice(): void
    {
        if ($this->form->delete()) {
            $this->showDeleteFormModal = false;
            Toaster::success('Facture supprimée définitivement !');
            $this->redirectRoute('invoices.archived');
        } else {
            Toaster::error('Erreur lors de la suppression de la facture.');
        }
    }

    public function showDeleteAllInvoicesForm(): void
    {
        // Vérifier s'il y a des factures à supprimer avant d'afficher la modal
        if ($this->isFilterEmpty()) {
            Toaster::info('Il n\'y a aucune facture à supprimer.');

            return;
        }

        $this->showDeleteAllFormModal = true;
    }

    public function deleteDefinitelyAllInvoice(): void
    {
        try {
            DB::beginTransaction();

            $archivedInvoices = $this->getFilteredInvoicesQuery()->get();
            $count = $this->batchDeleteInvoices($archivedInvoices);

            if ($count === 0) {
                Toaster::info('Aucune facture à supprimer: Il se peut que vous n\'ayez pas les permissions nécessaires.');
            } else {
                Toaster::success("Corbeille vidée avec succès ! $count factures ont été supprimées définitivement.");
            }

            $this->showDeleteAllFormModal = false;

            DB::commit();

            $this->redirectRoute('invoices.archived');
        } catch (\Exception $e) {
            DB::rollBack();
            Toaster::error('Une erreur est survenue lors de la suppression des factures.');
            Log::error('Erreur lors de la suppression des factures archivées: '.$e->getMessage());
        }
    }

    protected function getFilteredInvoicesQuery()
    {
        $query = Invoice::where('is_archived', true);

        if ($this->filterType === 'personal') {
            $query->where('user_id', auth()->id());
        } elseif ($this->hasFamily()) {
            $familyMemberIds = auth()->user()->family()->users()->pluck('user_id')->toArray();
            $query->whereIn('user_id', $familyMemberIds);
        }

        return $query;
    }

    protected function batchDeleteInvoices($invoices): int
    {
        $count = 0;
        foreach ($invoices as $invoice) {
            if (auth()->user()->can('delete', $invoice)) {
                $this->form->setFromInvoice($invoice);
                if ($this->form->delete()) {
                    $count++;
                }
            }
        }

        return $count;
    }

    protected function authorizeAction(string $action, Invoice $invoice, string $actionName): bool
    {
        if (! auth()->user()->can($action, $invoice)) {
            Toaster::error("Vous n'avez pas la permission de {$actionName} cette facture.");

            return false;
        }

        return true;
    }

    public function isFilterEmpty(): bool
    {
        return $this->getFilteredInvoicesQuery()->count() === 0;
    }

    public function render()
    {
        $archivedInvoices = $this->getFilteredInvoicesQuery()
            ->with(['file', 'user'])
            ->get();

        $invoicesByYear = $archivedInvoices->groupBy(function ($invoice) {
            return $invoice->issued_date ? date('Y', strtotime($invoice->issued_date)) : 'Non daté';
        })->sortKeysDesc();

        return view('livewire.pages.invoices.archived', [
            'invoicesByYear' => $invoicesByYear,
            'currentYear' => now()->format('Y'),
            'isFilterEmpty' => $archivedInvoices->isEmpty(),
        ])->layout('layouts.app-sidebar');
    }
}
