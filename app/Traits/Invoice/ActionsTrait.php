<?php

namespace App\Traits\Invoice;

use App\Models\Invoice;
use App\Models\InvoiceFile;
use Illuminate\Support\Facades\Storage;
use Masmerise\Toaster\Toaster;

trait ActionsTrait
{
    public ?Invoice $invoice = null;

    public $filePath = '';

    public $fileExtension = '';

    public $fileName = '';

    public $fileExists = false;

    public bool $showInvoicePreviewModal = false;

    public bool $showDeleteFormModal = false;

    public function showInvoiceModal($id): void
    {
        $invoice = auth()->user()->accessibleInvoices()
            ->with('file')
            ->findOrFail($id);

        $this->invoice = $invoice;

        $fileInfo = $this->generateInvoiceFileUrl($invoice);

        $this->filePath = $fileInfo['url'];
        $this->fileExtension = $fileInfo['extension'];
        $this->fileExists = $fileInfo['exists'] ?? false;
        $this->fileName = $fileInfo['name'] ?? $invoice->file->file_name ?? $invoice->name;

        // Fermer tout autre modal si ouvert
        if (property_exists($this, 'showFolderModal')) {
            $this->showFolderModal = false;
        }

        $this->showInvoicePreviewModal = true;
    }

    public function restoreInvoice($invoiceId): void
    {
        $invoice = Invoice::findOrFail($invoiceId);

        $this->invoice = $invoice;

        if (! auth()->user()->can('update', $invoice)) {
            return;
        }

        $this->invoice->update([
            'is_archived' => false,
            'is_favorite' => false,
        ]);

        $this->dispatch('invoice-restore', $invoiceId);
        Toaster::success('Facture restaurée avec succès !');
    }

    public function copyInvoice($invoiceId): void
    {
        try {
            $user = auth()->user();

            $originalInvoice = $user->accessibleInvoices()
                ->with(['sharedUsers'])
                ->findOrFail($invoiceId);

            if (! $user->can('update', $originalInvoice)) {
                Toaster::error('Vous n\'avez pas la permission de copier cette facture.');

                return;
            }

            $newInvoice = $originalInvoice->replicate();

            $newInvoice->name = $originalInvoice->name.' (version copiée)';
            $newInvoice->user_id = $user->id; // making sure the new invoice is owned by the current user

            if ($originalInvoice->tags) {
                $newInvoice->tags = $originalInvoice->tags;
            }

            $newInvoice->is_favorite = false;
            $newInvoice->created_at = now();
            $newInvoice->updated_at = now();

            $newInvoice->save();

            $this->redirectRoute('invoices.edit', $newInvoice->id);
        } catch (\Exception $e) {
            Toaster::error('Erreur lors de la copie de la facture: '.$e->getMessage());
        }
    }

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

            if (property_exists($this, 'showFolderModal')) {
                $this->showFolderModal = false;
            }

            // Rediriger vers l'URL présignée qui forcera le téléchargement
            return redirect()->away($presignedUrl);
        } catch (\Exception $e) {
            Toaster::error('Erreur lors du téléchargement: '.$e->getMessage());

            return false;
        }
    }

    public function toggleFavorite($invoiceId): void
    {
        $invoice = auth()->user()->invoices()->findOrFail($invoiceId);
        $invoice->update(['is_favorite' => ! $invoice->is_favorite]);

        // Fermer la modale du dossier s'il est ouvert
        if (property_exists($this, 'showFolderModal')) {
            $this->showFolderModal = false;
        }

        $this->dispatch('invoice-favorite', $invoiceId);
    }

    public function archiveInvoice($invoiceId): void
    {
        try {
            $invoice = Invoice::findOrFail($invoiceId);

            if (! auth()->user()->can('delete', $invoice)) {
                Toaster::error('Vous n\'avez pas la permission d\'archiver cette facture.');

                return;
            }

            $this->invoice = $invoice;
            $this->invoice->update([
                'is_archived' => true,
                'is_favorite' => false,
            ]);

            if (property_exists($this, 'showFolderModal')) {
                $this->showFolderModal = false;
            }

            $this->dispatch('invoice-archived', $invoiceId);
            Toaster::success('Facture archivée avec succès !');
        } catch (\Exception $e) {
            Toaster::error('Erreur lors de l\'archivage: '.$e->getMessage());
        }
    }

    public function showDeleteForm($invoiceId): void
    {
        $invoice = Invoice::findOrFail($invoiceId);

        if (! auth()->user()->can('delete', $invoice)) {
            Toaster::error('Vous n\'avez pas la permission de supprimer cette facture.');

            return;
        }

        $this->invoice = $invoice;
        $this->showDeleteFormModal = true;
    }

    public function deleteDefinitelyInvoice(): void
    {
        try {
            if (! auth()->user()->can('delete', $this->invoice)) {
                Toaster::error('Vous n\'avez pas la permission de supprimer cette facture.');

                return;
            }

            $this->invoice->delete();
            $this->showDeleteFormModal = false;

            if (property_exists($this, 'showFolderModal')) {
                $this->showFolderModal = false;
            }

            if ($this instanceof \App\Livewire\Pages\Invoices\Show) {
                $this->redirectRoute('dashboard');
            } else {
                $this->dispatch('invoice-deleted', $this->invoice->id);
                Toaster::success('Facture supprimée définitivement !');
            }
        } catch (\Exception $e) {
            Toaster::error('Erreur lors de la suppression: '.$e->getMessage());
        }
    }

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
