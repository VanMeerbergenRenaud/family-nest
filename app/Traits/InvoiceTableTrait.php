<?php

namespace App\Traits;

use App\Enums\PaymentStatusEnum;
use App\Models\Invoice;
use App\Models\InvoiceFile;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

trait InvoiceTableTrait
{
    use ColumnPreferencesTrait;
    use InvoiceFileUrlTrait;
    use WithPagination;

    // Propriétés communes
    public array $selectedInvoiceIds = [];

    public array $invoiceIdsOnPage = [];

    public $selectedPaymentStatus = null;

    public bool $showInvoicePreviewModal = false;

    public bool $showDeleteFormModal = false;

    public $filePath;

    public $fileExtension;

    public $fileName;

    public $fileExists = false;

    public ?Invoice $invoice = null;

    protected $cachedArchivedInvoices = null;

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

    // Archives
    public function getArchivedInvoices()
    {
        if ($this->cachedArchivedInvoices === null) {
            $this->cachedArchivedInvoices = auth()->user()->invoices()
                ->where('is_archived', true)
                ->get();
        }

        return $this->cachedArchivedInvoices;
    }

    public function getArchivedInvoicesCount(): int
    {
        return $this->getArchivedInvoices()->count();
    }

    // Téléchargements
    public function downloadInvoice($invoiceId)
    {
        $invoiceFile = InvoiceFile::where('invoice_id', $invoiceId)
            ->where('is_primary', true)
            ->first();

        if (! $invoiceFile) {
            Toaster::error('Aucun fichier trouvé pour cette facture.');

            return false;
        }

        try {
            // Récupérer le chemin brut du fichier dans S3
            $s3FilePath = $invoiceFile->getRawOriginal('file_path');

            if (! Storage::disk('s3')->exists($s3FilePath)) {
                Toaster::error('Fichier introuvable ou mal enregistré : Veuillez modifier votre facture et importer à nouveau le fichier.');

                return false;
            }

            // Pour les gros fichiers, utilisez une redirection avec entêtes modifiés
            $client = Storage::disk('s3')->getClient();
            $bucket = config('filesystems.disks.s3.bucket');

            // Créer une commande avec des paramètres spécifiques pour le téléchargement
            $command = $client->getCommand('GetObject', [
                'Bucket' => $bucket,
                'Key' => $s3FilePath,
                'ResponseContentType' => $this->getContentType($invoiceFile->file_extension),
                'ResponseContentDisposition' => 'attachment; filename="'.$invoiceFile->file_name.'"',
            ]);

            // Générer l'URL signée avec ces paramètres
            $request = $client->createPresignedRequest($command, '+5 minutes');
            $presignedUrl = (string) $request->getUri();

            // Rediriger vers l'URL présignée qui forcera le téléchargement
            return redirect()->away($presignedUrl);
        } catch (\Exception) {
            Toaster::error('Erreur lors du téléchargement : Le fichier n\'a pas pu être téléchargé.');

            return false;
        }
    }

    // Actions
    public function showInvoiceModal($id): void
    {
        $invoice = auth()->user()->invoices()
            ->with('file')
            ->findOrFail($id);

        $this->invoice = $invoice;

        $fileInfo = $this->generateInvoiceFileUrl($invoice);

        $this->filePath = $fileInfo['url'];
        $this->fileExtension = $fileInfo['extension'];
        $this->fileExists = $fileInfo['exists'];
        $this->fileName = $invoice->file->file_name ?? null;

        if (property_exists($this, 'showFolderModal')) {
            $this->showFolderModal = false;
        }

        $this->showInvoicePreviewModal = true;
    }

    public function toggleFavorite($invoiceId): void
    {
        $invoice = auth()->user()->invoices()->findOrFail($invoiceId);
        $invoice->update(['is_favorite' => ! $invoice->is_favorite]);

        // Fermer le modal du dossier si ouvert
        if (property_exists($this, 'showFolderModal')) {
            $this->showFolderModal = false;
        }
    }

    public function copyInvoice($invoiceId): void
    {
        try {
            $originalInvoice = auth()->user()->invoices()
                ->with(['file', 'sharedUsers'])
                ->findOrFail($invoiceId);

            if (! auth()->user()->can('update', $originalInvoice)) {
                Toaster::error('Vous n\'avez pas la permission de copier cette facture.');

                return;
            }

            $newInvoice = $originalInvoice->replicate();
            $newInvoice->name = $originalInvoice->name.' (version copiée)';
            $newInvoice->is_favorite = false;
            $newInvoice->created_at = now();
            $newInvoice->updated_at = now();
            $newInvoice->save();

            // Copier le fichier associé
            if ($originalInvoice->file) {
                $originalFile = $originalInvoice->file;
                $newFile = $originalFile->replicate();
                $newFile->invoice_id = $newInvoice->id;
                $newFile->save();
            }

            // Copier les tags
            if ($originalInvoice->tags) {
                $newInvoice->tags = $originalInvoice->tags;
                $newInvoice->save();
            }

            $this->redirectRoute('invoices.edit', $newInvoice->id);
        } catch (\Exception) {
            Toaster::error('Erreur lors de la copie de la facture::Veuillez réessayer.');
        }
    }

    public function archiveInvoice($invoiceId): void
    {
        try {
            $user = auth()->user();
            $invoice = Invoice::findOrFail($invoiceId);

            // Vérifier les autorisations via la policy
            if (! $user->can('archive', $invoice)) {
                Toaster::error('Vous n\'avez pas la permission d\'archiver cette facture.');

                return;
            }

            $this->invoice = $invoice;
            $this->invoice->update([
                'is_archived' => true,
                'is_favorite' => false,
            ]);

            // Réinitialiser le cache des factures archivées
            $this->cachedArchivedInvoices = null;

            // Fermer le modal du dossier si ouvert
            if (property_exists($this, 'showFolderModal')) {
                $this->showFolderModal = false;
            }

            Toaster::success('Facture archivée avec succès !');
        } catch (\Exception) {
            Toaster::error('Erreur lors de l\'archivage::Veuillez réessayer.');
        }
    }

    public function archiveSelected(): void
    {
        if (empty($this->selectedInvoiceIds)) {
            Toaster::error('Aucune facture sélectionnée.');

            return;
        }

        $user = auth()->user();

        // Récupérer toutes les factures sélectionnées
        $invoices = Invoice::whereIn('id', $this->selectedInvoiceIds)
            ->where('is_archived', false)
            ->get();

        if ($invoices->isEmpty()) {
            Toaster::error('Aucune facture sélectionnée ou déjà archivée.');

            return;
        }

        // Vérifier les permissions pour chaque facture
        $archivableInvoiceIds = [];
        foreach ($invoices as $invoice) {
            if ($user->can('archive', $invoice)) {
                $archivableInvoiceIds[] = $invoice->id;
            }
        }

        if (empty($archivableInvoiceIds)) {
            Toaster::error('Vous n\'avez pas la permission d\'archiver ces factures.');

            return;
        }

        try {
            $count = Invoice::whereIn('id', $archivableInvoiceIds)
                ->update([
                    'is_archived' => true,
                    'is_favorite' => false,
                ]);

            // Réinitialiser le cache des factures archivées
            $this->cachedArchivedInvoices = null;

            if ($count > 1) {
                Toaster::success("$count factures archivées avec succès.");
            } elseif ($count == 1) {
                Toaster::success('La facture a été archivée avec succès.');
            } else {
                Toaster::error('Aucune facture n\'a pu être archivée.');
            }

            $this->selectedInvoiceIds = [];
            $this->selectedPaymentStatus = null;
        } catch (\Exception) {
            Toaster::error('Erreur lors de l\'archivage::Veuillez réessayer.');
        }
    }

    public function showDeleteForm($invoiceId): void
    {
        $user = auth()->user();
        $invoice = Invoice::findOrFail($invoiceId);

        // Vérifier l'autorisation via la policy
        if (! $user->can('delete', $invoice)) {
            Toaster::error('Vous n\'avez pas la permission de supprimer cette facture.');

            return;
        }

        $this->invoice = $invoice;
        $this->showDeleteFormModal = true;
    }

    public function deleteDefinitelyInvoice(): void
    {
        try {
            $user = auth()->user();

            if (! $user->can('delete', $this->invoice)) {
                Toaster::error('Vous n\'avez pas la permission de supprimer cette facture.');

                return;
            }

            $this->invoice->delete();
            $this->showDeleteFormModal = false;

            // Réinitialiser le cache si la facture était archivée
            if ($this->invoice->is_archived) {
                $this->cachedArchivedInvoices = null;
            }

            Toaster::success('Facture supprimée avec succès !');
        } catch (\Exception) {
            Toaster::error('Erreur lors de la suppression de la facture::Veuillez réessayer.');
        }
    }

    // Marquer comme payé
    public function markAsPaymentStatusSelected(): void
    {
        try {
            if (empty($this->selectedInvoiceIds)) {
                Toaster::error('Aucune facture sélectionnée.');

                return;
            }

            if ($this->selectedPaymentStatus === null) {
                Toaster::error('Veuillez sélectionner un statut de paiement.');

                return;
            }

            $invoices = Invoice::whereIn('id', $this->selectedInvoiceIds)
                ->where('is_archived', false)
                ->get();

            if ($invoices->isEmpty()) {
                Toaster::error('Aucune facture sélectionnée ou déjà archivée.');

                return;
            }

            // Vérifier les permissions
            foreach ($invoices as $invoice) {
                if (! auth()->user()->can('update', $invoice)) {
                    Toaster::error('Vous n\'avez pas la permission de modifier cette facture.');

                    return;
                }
            }

            $count = $invoices->count();
            foreach ($invoices as $invoice) {
                $invoice->payment_status = $this->selectedPaymentStatus;
                $invoice->save();
            }

            $statusEnum = PaymentStatusEnum::from($this->selectedPaymentStatus);
            $statusLabel = $statusEnum->label();

            if ($count > 1) {
                Toaster::success("$count factures marquées comme \"$statusLabel\" avec succès.");
            } else {
                Toaster::success("La facture a été marquée comme \"$statusLabel\" avec succès.");
            }

            $this->selectedInvoiceIds = [];
            $this->selectedPaymentStatus = null;
        } catch (\Exception) {
            Toaster::error('Erreur lors de la modification des factures sélectionnées');
        }
    }

    // Récupération de l'extension de fichier
    private function getContentType($extension): string
    {
        $contentTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'csv' => 'text/csv',
        ];

        return $contentTypes[strtolower($extension)] ?? 'application/octet-stream';
    }
}
