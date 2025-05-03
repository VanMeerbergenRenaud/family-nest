<?php

namespace App\Traits\Invoice;

use App\Models\Invoice;
use App\Models\InvoiceFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Masmerise\Toaster\Toaster;

trait FileUrlTrait
{
    /**
     * Génère une URL temporaire pour le fichier principal d'une facture
     *
     * @param  int|Invoice  $invoice  La facture ou son ID
     * @param  bool  $showError  Afficher les erreurs à l'utilisateur
     * @return array Tableau contenant l'URL, l'extension et un statut
     */
    public function generateInvoiceFileUrl(Invoice|int $invoice, bool $showError = true): array
    {
        // Si l'argument est un ID, charger la facture
        if (is_numeric($invoice)) {
            $invoice = Invoice::with('file')->find($invoice);
            if (! $invoice) {
                if ($showError) {
                    Toaster::error('Facture introuvable.');
                }

                return ['url' => null, 'extension' => null, 'exists' => false];
            }
        }

        // Vérifier si la facture a un fichier associé
        if (! $invoice->file) {
            if ($showError) {
                Toaster::info('Aucun fichier associé à cette facture.');
            }

            return ['url' => null, 'extension' => null, 'exists' => false];
        }

        try {
            // Récupérer le chemin brut du fichier dans S3
            $s3FilePath = $invoice->file->getRawOriginal('file_path');
            $fileExtension = $invoice->file->file_extension;

            // Vérifier que le fichier existe sur S3
            if (! Storage::disk('s3')->exists($s3FilePath)) {
                if ($showError) {
                    Toaster::error('Fichier introuvable : Le fichier n\'existe plus sur le serveur.');
                }
                Log::warning('Fichier S3 introuvable: '.$s3FilePath);

                return ['url' => null, 'extension' => $fileExtension, 'exists' => false];
            }

            // Générer une URL temporaire simple
            $fileUrl = Storage::disk('s3')->temporaryUrl(
                $s3FilePath,
                now()->addMinutes(10)
            );

            return [
                'url' => $fileUrl,
                'extension' => $fileExtension,
                'exists' => true,
                'name' => $invoice->file->file_name,
            ];

        } catch (\Exception $e) {
            if ($showError) {
                Toaster::error('Erreur lors de l\'affichage : Le fichier n\'a pas pu être récupéré.');
            }
            Log::error('Erreur URL temporaire S3: '.$e->getMessage());

            return [
                'url' => null,
                'extension' => $invoice->file->file_extension ?? null,
                'exists' => false,
            ];
        }
    }

    /**
     * Génère une URL temporaire pour un fichier de facture spécifique
     *
     * @param  int|InvoiceFile  $invoiceFile  Le fichier ou son ID
     * @param  bool  $showError  Afficher les erreurs à l'utilisateur
     * @return array Tableau contenant l'URL, l'extension et un statut
     */
    public function generateInvoiceFileUrlFromFile(InvoiceFile|int $invoiceFile, bool $showError = true): array
    {
        // Si l'argument est un ID, charger le fichier
        if (is_numeric($invoiceFile)) {
            $invoiceFile = InvoiceFile::find($invoiceFile);
            if (! $invoiceFile) {
                if ($showError) {
                    Toaster::error('Fichier introuvable.');
                }

                return ['url' => null, 'extension' => null, 'exists' => false];
            }
        }

        try {
            // Récupérer le chemin brut du fichier dans S3
            $s3FilePath = $invoiceFile->getRawOriginal('file_path');
            $fileExtension = $invoiceFile->file_extension;

            // Vérifier que le fichier existe sur S3
            if (! Storage::disk('s3')->exists($s3FilePath)) {
                if ($showError) {
                    Toaster::error('Fichier introuvable : Le fichier n\'existe plus sur le serveur.');
                }
                Log::warning('Fichier S3 introuvable: '.$s3FilePath);

                return ['url' => null, 'extension' => $fileExtension, 'exists' => false];
            }

            // Générer une URL temporaire simple
            $fileUrl = Storage::disk('s3')->temporaryUrl(
                $s3FilePath,
                now()->addMinutes(10)
            );

            return [
                'url' => $fileUrl,
                'extension' => $fileExtension,
                'exists' => true,
                'name' => $invoiceFile->file_name,
            ];

        } catch (\Exception $e) {
            if ($showError) {
                Toaster::error('Erreur lors de l\'affichage::Le fichier n\'a pas pu être récupéré.');
            }
            Log::error('Erreur URL temporaire S3: '.$e->getMessage());

            return [
                'url' => null,
                'extension' => $invoiceFile->file_extension ?? null,
                'exists' => false,
            ];
        }
    }
}
