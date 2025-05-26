<?php

namespace App\Services;

use App\Jobs\CompressPdfJob;
use App\Models\Invoice;
use App\Models\InvoiceFile;
use App\Traits\FormatFileSizeTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileStorageService
{
    use FormatFileSizeTrait;

    public function processInvoiceFile(Invoice $invoice, ?UploadedFile $uploadedFile, ?InvoiceFile $oldFile = null): ?InvoiceFile
    {
        if (! $uploadedFile) {
            return null;
        }

        // Récupère les informations du fichier (nom, extension, taille)
        $fileName = $uploadedFile->getClientOriginalName();
        $fileExtension = strtolower($uploadedFile->getClientOriginalExtension());
        $fileSize = $uploadedFile->getSize();

        // Enregistre le fichier sur S3 avec un chemin spécifique à l'utilisateur
        $userPath = 'invoices/user_'.auth()->id();
        $filePath = $uploadedFile->store($userPath, 's3');

        // Supprime l'ancien fichier si il a été modifié
        if ($oldFile && Storage::disk('s3')->exists($oldFile->getRawOriginal('file_path'))) {
            Storage::disk('s3')->delete($oldFile->getRawOriginal('file_path'));
        }

        // Prepare les données du fichier
        $fileData = [
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_extension' => $fileExtension,
            'file_size' => $fileSize,
            'is_primary' => true,
            'compression_status' => null,
        ];

        // Crée ou met à jour l'enregistrement du fichier InvoiceFile dans la db
        if ($oldFile) {
            $oldFile->update($fileData);
            $invoiceFile = $oldFile;
        } else {
            $fileData['invoice_id'] = $invoice->id;
            $invoiceFile = InvoiceFile::create($fileData);
        }

        // Si le fichier est un PDF (compression via l’API ILovePdf)
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

    public function getFileInfo(?UploadedFile $file, ?string $fileName = null, ?string $fileExtension = null, ?int $fileSize = null): ?array
    {
        if (! $file && ! $fileName) {
            return null;
        }

        $name = $fileName ?? $file->getClientOriginalName();
        $extension = $fileExtension ?? strtolower($file->getClientOriginalExtension());
        $size = $fileSize ?? $file->getSize();

        // Renvoie un tableau contenant (nom, extension, taille et type du fichier)
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

    // TODO : Delete the invoice file from s3
}
