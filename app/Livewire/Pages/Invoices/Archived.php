<?php

namespace App\Livewire\Pages\Invoices;

use App\Livewire\Forms\InvoiceForm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

#[Title('Archives')]
class Archived extends Component
{
    use WithPagination;

    public InvoiceForm $form;

    public bool $showArchiveExempleModal = false;

    public bool $showDeleteFormModal = false;

    public bool $showDeleteAllFormModal = false;

    public function showArchiveExemple(): void
    {
        $this->showArchiveExempleModal = true;
    }

    public function restoreInvoice($invoiceId): void
    {
        $invoice = auth()->user()->invoices()
            ->where('id', $invoiceId)
            ->where('is_archived', true)
            ->firstOrFail();

        $this->form->setFromInvoice($invoice);

        if ($this->form->restore()) {
            Toaster::success('Facture restaurée avec succès !');
        } else {
            Toaster::error('Erreur lors de la restauration de la facture.');
        }
    }

    public function showDeleteInvoiceForm($id): void
    {
        $invoice = auth()->user()->invoices()->findOrFail($id);
        $this->form->setFromInvoice($invoice);
        $this->showDeleteFormModal = true;
    }

    public function deleteDefinitelyInvoice(): void
    {
        if ($this->form->delete()) {
            $this->showDeleteFormModal = false;
            Toaster::success('Facture supprimée définitivement !');
        } else {
            Toaster::error('Erreur lors de la suppression de la facture.');
        }
    }

    public function showDeleteAllInvoicesForm(): void
    {
        $this->showDeleteAllFormModal = true;
    }

    public function deleteDefinitelyAllInvoice(): void
    {
        try {
            DB::beginTransaction();

            $archivedInvoices = auth()->user()->invoices()
                ->where('is_archived', true)
                ->get();

            $count = $archivedInvoices->count();

            if ($count === 0) {
                Toaster::info('Aucune facture à supprimer.');
                $this->showDeleteAllFormModal = false;

                return;
            }

            foreach ($archivedInvoices as $invoice) {
                $this->form->setFromInvoice($invoice);
                $this->form->delete();
            }

            DB::commit();
            $this->showDeleteAllFormModal = false;
            Toaster::success("Corbeille vidée avec succès ! $count factures ont été supprimées définitivement.");
        } catch (\Exception $e) {
            DB::rollBack();
            Toaster::error('Une erreur est survenue lors de la suppression des factures.');
            Log::error('Erreur lors de la suppression des factures archivées: '.$e->getMessage());
        }
    }

    public function render()
    {
        $archivedInvoices = auth()->user()->invoices()
            ->with('file')
            ->where('is_archived', true)
            ->orderBy('created_at', 'desc')
            ->paginate(7);

        return view('livewire.pages.invoices.archived', [
            'archivedInvoices' => $archivedInvoices,
        ])->layout('layouts.app-sidebar');
    }
}
