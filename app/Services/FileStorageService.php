<?php

namespace App\Services;

use App\Jobs\CompressPdfJob;
use App\Models\Invoice;
use App\Models\InvoiceFile;
use App\Traits\FormatFileSizeTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileStorageService
{
    use FormatFileSizeTrait;

    /**
     * Process uploaded invoice file
     */
    public function processInvoiceFile(Invoice $invoice, ?UploadedFile $uploadedFile, ?InvoiceFile $oldFile = null): ?InvoiceFile
    {
        if (! $uploadedFile) {
            return null;
        }

        // Get file info
        $fileName = $uploadedFile->getClientOriginalName();
        $fileExtension = strtolower($uploadedFile->getClientOriginalExtension());
        $fileSize = $uploadedFile->getSize();

        // Store file on S3
        $userPath = 'invoices/user_'.auth()->id();
        $filePath = $uploadedFile->store($userPath, 's3');

        // Delete old file if exists
        if ($oldFile && Storage::disk('s3')->exists($oldFile->getRawOriginal('file_path'))) {
            Storage::disk('s3')->delete($oldFile->getRawOriginal('file_path'));
        }

        // Prepare file data
        $fileData = [
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_extension' => $fileExtension,
            'file_size' => $fileSize,
            'is_primary' => true,
            'compression_status' => null,
        ];

        // Update or create file record
        if ($oldFile) {
            $oldFile->update($fileData);
            $invoiceFile = $oldFile;
        } else {
            $fileData['invoice_id'] = $invoice->id;
            $invoiceFile = InvoiceFile::create($fileData);
        }

        // Queue PDF compression if needed
        if ($fileExtension === 'pdf') {
            $invoiceFile->update([
                'compression_status' => 'pending',
                'original_size' => $fileSize,
            ]);

            CompressPdfJob::dispatch(
                $invoiceFile->id,
                $filePath,
                $fileSize
            );
        }

        return $invoiceFile;
    }

    /**
     * Delete invoice file
     */
    public function deleteInvoiceFile(InvoiceFile $file): bool
    {
        try {
            // Delete from S3
            if ($file->getRawOriginal('file_path') && Storage::disk('s3')->exists($file->getRawOriginal('file_path'))) {
                Storage::disk('s3')->delete($file->getRawOriginal('file_path'));
            }

            // Delete database record
            $file->delete();

            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting file: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Get file information
     */
    public function getFileInfo(?UploadedFile $file, ?string $fileName = null, ?string $fileExtension = null, ?int $fileSize = null): ?array
    {
        if (! $file && ! $fileName) {
            return null;
        }

        $name = $fileName ?? $file->getClientOriginalName();
        $extension = $fileExtension ?? strtolower($file->getClientOriginalExtension());
        $size = $fileSize ?? $file->getSize();

        return [
            'name' => $name,
            'extension' => $extension,
            'size' => round($size / 1024, 2), // KB
            'sizeFormatted' => $this->formatFileSize($size),
            'isImage' => in_array($extension, ['jpg', 'jpeg', 'png']),
            'isPdf' => $extension === 'pdf',
            'isDocx' => $extension === 'docx',
            'isCsv' => $extension === 'csv',
        ];
    }
}
