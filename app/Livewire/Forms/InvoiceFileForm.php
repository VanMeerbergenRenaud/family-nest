<?php

namespace App\Livewire\Forms;

use App\Models\InvoiceFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Form;

class InvoiceFileForm extends Form
{
    public InvoiceFile $invoiceFile;

    #[Validate]
    public $uploadedFile;

    public $existingFilePath = null;

    public $fileName = null;

    public $filePath = null;

    public $fileExtension = null;

    public $fileSize = null;

    /**
     * Règles de validation
     */
    public function rules()
    {
        return [
            'uploadedFile' => $this->existingFilePath
                ? 'nullable|file|mimes:pdf,docx,jpeg,png,jpg|max:10240'
                : 'required|file|mimes:pdf,docx,jpeg,png,jpg|max:10240',
        ];
    }

    /**
     * Messages d'erreur personnalisés
     */
    public function messages()
    {
        return [
            'uploadedFile.required' => 'Veuillez sélectionner un fichier.',
            'uploadedFile.file' => 'Le fichier doit être un fichier valide.',
            'uploadedFile.mimes' => 'Le fichier doit être au format PDF, Word, JPEG, JPG ou PNG.',
            'uploadedFile.max' => 'Le fichier ne doit pas dépasser 10 Mo.',
        ];
    }

    /**
     * Traiter le fichier uploadé et récupérer ses informations
     */
    public function processUploadedFile()
    {
        if (! $this->uploadedFile) {
            return false;
        }

        $this->fileName = $this->uploadedFile->getClientOriginalName();
        $this->fileExtension = strtolower($this->uploadedFile->getClientOriginalExtension());
        $this->fileSize = $this->uploadedFile->getSize();

        return true;
    }

    /**
     * Enregistrer le fichier lié à une facture
     */
    public function storeFile($invoiceId)
    {
        $this->validate();

        try {
            // Vérifier que le fichier existe
            if (! $this->uploadedFile) {
                throw new \Exception('Aucun fichier fourni');
            }

            // Traiter le fichier
            $this->processUploadedFile();

            // Stocker le fichier et récupérer son chemin
            $this->filePath = $this->uploadedFile->store('invoices', 'public');

            // Vérifier que le stockage a réussi
            if (! $this->filePath) {
                throw new \Exception('Échec du stockage du fichier');
            }

            // Créer l'enregistrement dans la base de données
            $invoiceFile = InvoiceFile::create([
                'invoice_id' => $invoiceId,
                'file_path' => $this->filePath,
                'file_name' => $this->fileName,
                'file_extension' => $this->fileExtension,
                'file_size' => $this->fileSize,
            ]);

            return $invoiceFile;

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'upload du fichier: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Mettre à jour le fichier lié à une facture
     */
    public function updateFile($invoiceId)
    {
        if (! $this->uploadedFile && ! $this->existingFilePath) {
            return false;
        }

        try {
            // Si un nouveau fichier est fourni
            if ($this->uploadedFile) {
                $this->validate();

                // Traiter le fichier
                $this->processUploadedFile();

                // Récupérer l'ancien fichier pour le supprimer si nécessaire
                $oldFile = InvoiceFile::where('invoice_id', $invoiceId)
                    ->where('is_primary', true)
                    ->first();

                // Supprimer l'ancien fichier du stockage
                if ($oldFile && Storage::disk('public')->exists($oldFile->getRawOriginal('file_path'))) {
                    Storage::disk('public')->delete($oldFile->getRawOriginal('file_path'));
                }

                // Stocker le nouveau fichier
                $this->filePath = $this->uploadedFile->store('invoices', 'public');

                // Créer ou mettre à jour l'enregistrement dans la base de données
                if ($oldFile) {
                    $oldFile->update([
                        'file_path' => $this->filePath,
                        'file_name' => $this->fileName,
                        'file_extension' => $this->fileExtension,
                        'file_size' => $this->fileSize,
                    ]);

                    return $oldFile;
                } else {
                    return $this->storeFile($invoiceId);
                }
            }

            return true;

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la mise à jour du fichier: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Obtenir les informations sur le fichier uploadé
     */
    public function getFileInfo()
    {
        if (! $this->uploadedFile) {
            return null;
        }

        return [
            'name' => $this->fileName ?? $this->uploadedFile->getClientOriginalName(),
            'extension' => $this->fileExtension ?? strtolower($this->uploadedFile->getClientOriginalExtension()),
            'size' => round(($this->fileSize ?? $this->uploadedFile->getSize()) / 1024, 2), // Taille en KB
            'sizeFormatted' => $this->formatFileSize($this->fileSize ?? $this->uploadedFile->getSize()),
            'isImage' => in_array($this->fileExtension ?? strtolower($this->uploadedFile->getClientOriginalExtension()), ['jpg', 'jpeg', 'png']),
            'isPdf' => ($this->fileExtension ?? strtolower($this->uploadedFile->getClientOriginalExtension())) === 'pdf',
            'isDocx' => ($this->fileExtension ?? strtolower($this->uploadedFile->getClientOriginalExtension())) === 'docx',
            'isCsv' => ($this->fileExtension ?? strtolower($this->uploadedFile->getClientOriginalExtension())) === 'csv',
        ];
    }

    /**
     * Formater la taille du fichier
     */
    private function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 2).' '.$units[$pow];
    }

    /**
     * Supprimer le fichier
     */
    public function removeFile()
    {
        $this->uploadedFile = null;
        $this->existingFilePath = null;
        $this->fileName = null;
        $this->filePath = null;
        $this->fileExtension = null;
        $this->fileSize = null;
    }

    /**
     * Définit les informations du fichier existant
     */
    public function setInvoiceFile(InvoiceFile $invoiceFile)
    {
        $this->invoiceFile = $invoiceFile;
        $this->existingFilePath = $invoiceFile->getRawOriginal('file_path');
        $this->filePath = $invoiceFile->file_path;
        $this->fileName = $invoiceFile->file_name;
        $this->fileExtension = $invoiceFile->file_extension;
        $this->fileSize = $invoiceFile->file_size;
    }
}
