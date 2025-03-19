<?php

namespace App\Jobs;

use App\Models\InvoiceFile;
use App\Services\PdfCompressorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CompressPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $invoiceFileId;

    protected $originalPath;

    protected $originalSize;

    // Nombre de tentatives avant d'abandonner
    public int $tries = 3;

    // Create a new job instance.
    public function __construct($invoiceFileId, $originalPath, $originalSize)
    {
        $this->invoiceFileId = $invoiceFileId;
        $this->originalPath = $originalPath;
        $this->originalSize = $originalSize;
    }

    // Execute the job.
    public function handle(): void
    {
        try {
            // Récupérer le fichier de facture
            $invoiceFile = InvoiceFile::find($this->invoiceFileId);

            if (! $invoiceFile) {
                return;
            }

            // Chemin complet du fichier original
            $originalFullPath = storage_path('app/public/'.$this->originalPath);

            if (! file_exists($originalFullPath)) {
                // Mettre à jour le statut en cas d'échec
                $invoiceFile->update([
                    'compression_status' => 'failed',
                    'original_size' => $this->originalSize,
                    'compression_rate' => 0,
                ]);

                return;
            }

            // Créer un dossier temporaire pour la sortie
            $tempOutputDir = storage_path('app/public/temp/compressed');

            if (! is_dir($tempOutputDir)) {
                mkdir($tempOutputDir, 0755, true);
            }

            // Compresser le PDF
            Log::info('CompressPdfJob: Début de la compression', [
                'service' => 'PdfCompressorService',
            ]);

            $pdfCompressor = app(PdfCompressorService::class);

            $compressedPath = $pdfCompressor->compressPdf(
                $originalFullPath,
                $tempOutputDir
            );

            if (! $compressedPath || ! file_exists($compressedPath)) {
                // Mettre à jour le statut en cas d'échec
                $invoiceFile->update([
                    'compression_status' => 'failed',
                    'original_size' => $this->originalSize,
                    'compression_rate' => 0,
                ]);

                return;
            }

            Log::info('CompressPdfJob: Compression réussie', [
                'compressed_path' => $compressedPath,
                'compressed_size' => filesize($compressedPath),
            ]);

            // Calculer le nouveau chemin de stockage permanent
            $newFileName = 'compressed_'.basename($this->originalPath);
            $newFilePath = 'invoices/'.$newFileName;

            // Déplacer le fichier compressé vers le stockage permanent
            Storage::disk('public')->put(
                $newFilePath,
                file_get_contents($compressedPath)
            );

            // Obtenir la nouvelle taille du fichier
            $newSize = Storage::disk('public')->size($newFilePath);
            $compressionRate = round(($this->originalSize - $newSize) / $this->originalSize * 100, 1);

            // Mettre à jour le fichier de facture avec le nouveau fichier
            $invoiceFile->update([
                'file_path' => $newFilePath,
                'file_size' => $newSize,
                'compression_status' => 'completed',
                'original_size' => $this->originalSize,
                'compression_rate' => $compressionRate,
            ]);

            // Nettoyer - supprimer le fichier original si différent du compressé
            if ($this->originalPath !== $newFilePath) {
                Storage::disk('public')->delete($this->originalPath);
            }

            // Nettoyer le fichier temporaire
            if (file_exists($compressedPath)) {
                unlink($compressedPath);
            }
        } catch (\Exception $e) {
            Log::error('CompressPdfJob: Erreur lors de la compression du PDF: '.$e->getMessage(), [
                'invoice_file_id' => $this->invoiceFileId,
                'exception_class' => get_class($e),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            // En cas d'exception, mettre également à jour le statut
            try {
                InvoiceFile::where('id', $this->invoiceFileId)->update([
                    'compression_status' => 'failed',
                    'original_size' => $this->originalSize,
                    'compression_rate' => 0,
                ]);
            } catch (\Exception $updateEx) {
                Log::error('CompressPdfJob: Impossible de mettre à jour le statut après erreur: '.$updateEx->getMessage());
            }
        }
    }
}
