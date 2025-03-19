<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Ilovepdf\Ilovepdf;

class PdfCompressorService
{
    protected Ilovepdf $ilovepdf;

    public function __construct()
    {
        $publicKey = config('services.ilovepdf.public_key');
        $secretKey = config('services.ilovepdf.secret_key');

        $this->ilovepdf = new Ilovepdf($publicKey, $secretKey);
    }

    /**
     * Compresse un fichier PDF et le télécharge dans le répertoire spécifié.
     *
     * @param  string  $filePath  Chemin du fichier PDF à compresser.
     * @param  string  $outputPath  Chemin du répertoire où le fichier compressé sera téléchargé.
     * @return string|null Chemin du fichier PDF compressé ou null en cas d'erreur.
     */
    public function compressPdf(string $filePath, string $outputPath): ?string
    {
        try {
            // Récupère le nom du fichier original
            $originalFileName = basename($filePath);

            // Crée une nouvelle tâche de compression
            $myTask = $this->ilovepdf->newTask('compress');

            // Ajoute le fichier à la tâche
            $file = $myTask->addFile($filePath);

            Log::info('PdfCompressorService: Fichier ajouté', [
                'server_filename' => $file->server_filename ?? 'Fichier non trouvé',
            ]);

            // Définit le niveau de compression
            $myTask->setCompressionLevel('recommended');

            // Exécute la tâche de compression
            $myTask->execute();

            // Télécharge le fichier compressé
            $myTask->download($outputPath);

            // Chemin du fichier compressé
            $compressedFilePath = $outputPath.'/'.$originalFileName;

            // Vérifie si le fichier compressé existe
            if (file_exists($compressedFilePath)) {
                return $compressedFilePath;
            }

            // Recherche d'autres fichiers PDF dans le répertoire de sortie
            $pdfFiles = glob($outputPath.'/*.pdf');

            // Retourne le premier fichier PDF trouvé
            if (! empty($pdfFiles)) {
                return $pdfFiles[0];
            }

            return null;

        } catch (\Exception $e) {
            Log::error('PdfCompressorService: Erreur lors de la compression du PDF: '.$e->getMessage(), [
                'exception_class' => get_class($e),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }
}
