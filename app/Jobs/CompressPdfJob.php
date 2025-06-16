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

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct($invoiceFileId, $originalPath, $originalSize)
    {
        $this->invoiceFileId = $invoiceFileId;
        $this->originalPath = $originalPath;
        $this->originalSize = $originalSize;
    }

    public function handle(): void
    {
        $tempDir = null;
        $tempFilePath = null;
        $compressedPath = null;

        try {
            // Get invoice file
            $invoiceFile = InvoiceFile::find($this->invoiceFileId);
            if (! $invoiceFile || ! Storage::disk('s3')->exists($this->originalPath)) {
                $this->markAsFailed($invoiceFile);

                return;
            }

            // Create temp directories
            $uniqueId = uniqid();
            $tempDir = storage_path('app/temp/pdf_compression/'.$uniqueId);
            $tempOutputDir = $tempDir.'/output';
            mkdir($tempDir, 0755, true);
            mkdir($tempOutputDir, 0755, true);

            // Download file from S3
            $tempFilePath = $tempDir.'/'.basename($this->originalPath);
            file_put_contents($tempFilePath, Storage::disk('s3')->get($this->originalPath));

            // Compress PDF
            $pdfCompressor = app(PdfCompressorService::class);
            $compressedPath = $pdfCompressor->compressPdf($tempFilePath, $tempOutputDir);

            if (! $compressedPath || ! file_exists($compressedPath)) {
                throw new \Exception('Compression failed');
            }

            // Check if compression is worth it
            $compressedSize = filesize($compressedPath);
            if ($compressedSize >= $this->originalSize) {
                $invoiceFile->update([
                    'compression_status' => 'skipped',
                    'compression_rate' => 0,
                ]);

                return;
            }

            // Upload compressed file to S3
            $newS3Path = dirname($this->originalPath).'/compressed_'.basename($this->originalPath);
            Storage::disk('s3')->put($newS3Path, file_get_contents($compressedPath));

            // Update invoice file record
            $compressionRate = round(($this->originalSize - $compressedSize) / $this->originalSize * 100, 1);
            $invoiceFile->update([
                'file_path' => $newS3Path,
                'file_size' => $compressedSize,
                'compression_status' => 'completed',
                'compression_rate' => $compressionRate,
            ]);

            // Delete original file
            if ($this->originalPath !== $newS3Path) {
                Storage::disk('s3')->delete($this->originalPath);
            }

        } catch (\Exception $e) {
            Log::error('PDF compression error: '.$e->getMessage());
            $this->markAsFailed(InvoiceFile::find($this->invoiceFileId));
        } finally {
            $this->cleanupTempFiles($tempFilePath, $compressedPath, $tempDir);
        }
    }

    private function markAsFailed(?InvoiceFile $invoiceFile): void
    {
        if ($invoiceFile) {
            $invoiceFile->update([
                'compression_status' => 'failed',
                'compression_rate' => 0,
            ]);
        }
    }

    private function cleanupTempFiles(?string $tempFilePath, ?string $compressedPath, ?string $tempDir): void
    {
        // Delete temp file
        if ($tempFilePath && file_exists($tempFilePath)) {
            unlink($tempFilePath);
        }

        // Delete compressed file
        if ($compressedPath && file_exists($compressedPath)) {
            unlink($compressedPath);
        }

        // Recursively delete temp directory
        if ($tempDir && is_dir($tempDir)) {
            $this->deleteDirectory($tempDir);
        }
    }

    private function deleteDirectory(string $dir): bool
    {
        if (! is_dir($dir)) {
            return false;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir.'/'.$file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        return rmdir($dir);
    }
}
