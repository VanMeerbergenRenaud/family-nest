<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Ilovepdf\Ilovepdf;

class PdfCompressorService
{
    protected Ilovepdf $ilovepdf;

    public function __construct()
    {
        $this->ilovepdf = new Ilovepdf(
            config('services.ilovepdf.public_key'),
            config('services.ilovepdf.secret_key')
        );
    }

    /**
     * Compress PDF file using iLovePDF API
     */
    public function compressPdf(string $filePath, string $outputPath): ?string
    {
        try {
            if (! file_exists($filePath) || ! is_dir($outputPath)) {
                return null;
            }

            $originalFileName = basename($filePath);
            $myTask = $this->ilovepdf->newTask('compress');
            $myTask->setCompressionLevel('extreme');
            $myTask->addFile($filePath);
            $myTask->execute();
            $myTask->download($outputPath);

            // Check compressed file
            $compressedFilePath = $outputPath.'/'.$originalFileName;
            if (file_exists($compressedFilePath)) {
                return $compressedFilePath;
            }

            // Try to find any PDF file in output directory
            $pdfFiles = glob($outputPath.'/*.pdf');

            return ! empty($pdfFiles) ? $pdfFiles[0] : null;

        } catch (\Exception $e) {
            Log::error('PDF compression failed: '.$e->getMessage());

            return null;
        }
    }
}
