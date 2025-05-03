<?php

namespace App\Livewire\Pages\Invoices;

use App\Livewire\Forms\InvoiceForm;
use App\Models\Invoice;
use App\Models\User;
use App\Traits\Invoice\StateCheckTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;
use ZipArchive;

#[Title('Archives')]
class Archived extends Component
{
    use StateCheckTrait, WithPagination;

    public InvoiceForm $form;

    public string $filterType = 'all';

    public bool $showDeleteFormModal = false;

    public bool $showDeleteAllFormModal = false;

    public bool $showDownloadSelectionModal = false;

    public string $selectedMemberId = 'all';

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
            $familyMemberIds = auth()->user()->family()
                ->users()
                ->pluck('user_id')
                ->toArray();

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
            Toaster::error("Vous n'avez pas la permission de $actionName cette facture.");

            return false;
        }

        return true;
    }

    public function isFilterEmpty(): bool
    {
        return $this->getFilteredInvoicesQuery()->count() === 0;
    }

    public function downloadArchivedFiles(): void
    {
        if ($this->isFilterEmpty()) {
            Toaster::info('Aucune facture archivée à télécharger.');

            return;
        }

        $this->selectedMemberId = 'all';
        $this->showDownloadSelectionModal = true;
    }

    public function updateSelectedMemberId($memberId): void
    {
        $this->selectedMemberId = $memberId;
        Log::info('Membre sélectionné pour téléchargement', ['member_id' => $memberId]);
    }

    public function downloadSelectedArchives()
    {
        $this->showDownloadSelectionModal = false;

        Toaster::info('Préparation de votre téléchargement...');

        try {
            // 1. Récupération des factures selon les filtres
            $query = $this->getFilteredInvoicesQuery();

            $userName = auth()->user()->name;
            $familyName = auth()->user()->family()->name;

            // Appliquer le filtre de membre si nécessaire
            if ($this->selectedMemberId !== 'all') {
                $query->where('user_id', $this->selectedMemberId);
                $memberName = User::find($this->selectedMemberId)->name ?? 'Utilisateur';
                $zipName = 'FamilyNest_Archives_'.str_replace(' ', '_', $memberName);
            } else {
                $zipName = $this->filterType === 'personal'
                    ? 'FamilyNest_Archives_'.$userName
                    : 'FamilyNest_Archives_'.$familyName;
            }

            // Charger les factures avec leurs fichiers
            $invoices = $query->with(['file', 'user'])->get();

            if ($invoices->isEmpty()) {
                Toaster::warning('Aucune facture disponible à télécharger.');

                return;
            }

            // 2. Préparation du ZIP
            $tempDir = storage_path('app/temp/'.uniqid());
            if (! File::isDirectory($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }

            $zipPath = "$tempDir/$zipName";
            $zip = new ZipArchive;

            if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
                throw new \Exception('Impossible de créer le fichier ZIP');
            }

            // 3. Organisation par année et ajout des fichiers
            $successes = 0;
            $errors = 0;

            // Grouper par année
            $invoicesByYear = $invoices->groupBy(function ($invoice) {
                return $invoice->issued_date
                    ? date('Y', strtotime($invoice->issued_date))
                    : 'Archives_non_datées';
            });

            foreach ($invoicesByYear as $year => $yearInvoices) {
                // Créer un dossier pour l'année
                $zip->addEmptyDir($year);

                foreach ($yearInvoices as $invoice) {
                    // Vérifier si le fichier existe
                    if (! $invoice->file || ! Storage::disk('s3')->exists($invoice->file->file_path)) {
                        $errors++;

                        continue;
                    }

                    try {
                        // Définir des noms de fichiers sécurisés
                        $fileName = preg_replace('/[^a-zA-Z0-9_\-.]/', '_', $invoice->name ?? 'Facture');
                        $fileName .= "_$invoice->id.{$invoice->file->file_extension}";

                        // Chemin temporaire pour le fichier
                        $tempFile = "$tempDir/temp_$invoice->id.{$invoice->file->file_extension}";

                        // Télécharger depuis S3
                        Storage::disk('local')->put(
                            str_replace(storage_path('app'), '', $tempFile),
                            Storage::disk('s3')->get($invoice->file->file_path)
                        );

                        // Ajouter au ZIP
                        $zip->addFile($tempFile, "$year/$fileName");
                        $successes++;

                    } catch (\Exception $e) {
                        Log::error('Erreur avec le fichier: '.$e->getMessage());
                        $errors++;
                    }
                }
            }

            $zip->close();

            // 4. Nettoyage et téléchargement
            foreach (File::glob("$tempDir/temp_*") as $tempFile) {
                File::delete($tempFile);
            }

            if ($successes === 0) {
                File::delete($zipPath);
                Toaster::error("Aucun fichier n'a pu être téléchargé.");

                return;
            }

            if ($errors > 0) {
                Toaster::warning("$successes fichiers ajoutés au ZIP. $errors fichiers n'ont pas pu être inclus.");
            } else {
                Toaster::success("$successes fichiers ajoutés au ZIP avec succès !");
            }

            // 5. Déclencher le téléchargement
            return response()->download($zipPath, $zipName, [
                'Content-Type' => 'application/zip',
            ])->deleteFileAfterSend();

        } catch (\Exception $e) {
            Toaster::error('Erreur lors de la création du ZIP.');
            Log::error('Erreur ZIP: '.$e->getMessage());

            return null;
        }
    }

    public function render()
    {
        $archivedInvoices = $this->getFilteredInvoicesQuery()
            ->with(['file'])
            ->get();

        $invoicesByYear = $archivedInvoices->groupBy(function ($invoice) {
            return $invoice->issued_date
                ? date('Y', strtotime($invoice->issued_date))
                : 'Non daté';
        })->sortKeysDesc();

        $familyMembers = auth()->user()->family()->users()->get();

        return view('livewire.pages.invoices.archived', [
            'archivedInvoices' => $archivedInvoices,
            'invoicesByYear' => $invoicesByYear,
            'currentYear' => now()->format('Y'),
            'familyMembers' => $familyMembers,
        ])->layout('layouts.app-sidebar');
    }
}
