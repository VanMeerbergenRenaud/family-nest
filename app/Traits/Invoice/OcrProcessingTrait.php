<?php

namespace App\Traits\Invoice;

use App\Services\TextractService;
use Illuminate\Support\Facades\Log;
use Masmerise\Toaster\Toaster;

trait OcrProcessingTrait
{
    public bool $isOcrProcessing = false;

    public bool $showOcrButton = false;

    public $ocrData = null;

    public $ocrProgress = 0;

    public $ocrService = null;

    public function updatedFormUploadedFile(): void
    {
        $this->showOcrButton = true;
    }

    public function processOcr(TextractService $textractService): void
    {
        if (! $this->form->uploadedFile) {
            Toaster::error('Veuillez d\'abord télécharger un fichier.');

            return;
        }

        $this->isOcrProcessing = true;
        $this->ocrProgress = 0;
        $this->ocrService = null;

        try {
            // Notifier le front-end que le traitement commence
            $this->dispatch('ocr-processing-started');

            // Afficher le service en cours d'utilisation
            if (config('services.textract.enabled')) {
                $this->ocrService = 'AWS Textract';
            } elseif (config('services.ocr_space.enabled')) {
                $this->ocrService = 'OCR.space';
            }

            // Stockage temporaire du fichier pour l'OCR
            $path = $this->form->uploadedFile->storeAs(
                'temp',
                $this->form->uploadedFile->getClientOriginalName(),
                'local'
            );
            $fullPath = storage_path('app/'.$path);

            // Analyse OCR avec gestion du timeout
            $result = $textractService->analyzeInvoice($fullPath);

            // Nettoyage du fichier temporaire
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            // Traiter le résultat
            if ($result['success']) {
                $this->ocrData = $result['data'];
                $this->applyOcrDataToForm();

                // Message de succès avec détails
                $service = $result['service'] === 'textract' ? 'AWS Textract' : 'OCR.space';
                $duration = $result['duration'] ?? null;

                $message = 'Analyse OCR terminée avec succès';

                if ($duration) {
                    $message .= " (via $service en {$duration}s)";
                }

                Toaster::success($message);

                // Log pour le suivi
                Log::info('OCR réussi', [
                    'service' => $result['service'] ?? 'unknown',
                    'duration' => $result['duration'] ?? 0,
                    'data_extracted' => array_filter($result['data'] ?? []),
                ]);

            } else {
                // Gestion des erreurs spécifiques
                if (isset($result['timeout']) && $result['timeout']) {
                    Toaster::error('L\'analyse OCR a pris trop de temps (timeout après 60 secondes). Veuillez réessayer avec un fichier plus petit.');
                    Log::warning('OCR timeout', [
                        'duration' => $result['duration'] ?? 60,
                        'file_size' => $this->form->uploadedFile->getSize(),
                    ]);
                } elseif (str_contains($result['message'] ?? '', 'Fichier trop volumineux')) {
                    Toaster::error('Le fichier est trop volumineux pour l\'analyse OCR. Limite : 10 Mo.');
                    Log::warning('Fichier trop volumineux pour OCR', [
                        'size' => $this->form->uploadedFile->getSize(),
                        'message' => $result['message'],
                    ]);
                } elseif (str_contains($result['message'] ?? '', 'Tous les services OCR ont échoué')) {
                    Toaster::error('Échec de l\'analyse OCR : Tous les services ont échoué. Veuillez vérifier le format du fichier.');
                    Log::error('Tous les services OCR ont échoué', [
                        'message' => $result['message'],
                        'file_type' => $this->form->uploadedFile->getClientOriginalExtension(),
                    ]);
                } else {
                    // Message d'erreur générique
                    $errorMessage = 'Échec de l\'analyse OCR';
                    if (! empty($result['service'])) {
                        $service = $result['service'] === 'textract' ? 'AWS Textract' : 'OCR.space';
                        $errorMessage .= " ($service)";
                    }
                    $errorMessage .= ' : Vérifiez le fichier ou le format du document.';

                    Toaster::error($errorMessage);
                    Log::error('Échec de l\'analyse OCR', [
                        'message' => $result['message'] ?? 'Erreur inconnue',
                        'service' => $result['service'] ?? 'unknown',
                    ]);
                }
            }
        } catch (\Exception $e) {
            Toaster::error('Une erreur inattendue est survenue : Vérifiez le fichier ou réessayez plus tard.');
            Log::error('Exception lors du traitement OCR: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        } finally {
            $this->isOcrProcessing = false;
            $this->ocrService = null;
            // Notifier le front-end que le traitement est terminé
            $this->dispatch('ocr-processing-completed');
        }
    }

    protected function applyOcrDataToForm(): void
    {
        if (! $this->ocrData) {
            return;
        }

        $fieldsUpdated = [];

        // Appliquer les données OCR aux champs du formulaire
        if (! empty($this->ocrData['name'])) {
            $this->form->name = $this->ocrData['name'];
            $fieldsUpdated[] = 'Nom';
        }

        if (! empty($this->ocrData['reference'])) {
            $this->form->reference = $this->ocrData['reference'];
            $fieldsUpdated[] = 'Référence';
        }

        if (! empty($this->ocrData['issuer_name'])) {
            $this->form->issuer_name = $this->ocrData['issuer_name'];
            $fieldsUpdated[] = 'Fournisseur';
        }

        if (! empty($this->ocrData['issuer_website'])) {
            $this->form->issuer_website = $this->ocrData['issuer_website'];
            $fieldsUpdated[] = 'Site web';
        }

        if (! empty($this->ocrData['amount'])) {
            $this->form->amount = $this->form->normalizeAmount($this->ocrData['amount']);
            $this->calculateRemainingShares();
            $fieldsUpdated[] = 'Montant';
        }

        if (! empty($this->ocrData['issued_date'])) {
            $this->form->issued_date = $this->ocrData['issued_date'];
            $fieldsUpdated[] = 'Date d\'émission';
        }

        if (! empty($this->ocrData['payment_due_date'])) {
            $this->form->payment_due_date = $this->ocrData['payment_due_date'];
            $fieldsUpdated[] = 'Date d\'échéance';
        }

        // Afficher un message récapitulatif des champs remplis
        if (! empty($fieldsUpdated)) {
            $fieldsCount = count($fieldsUpdated);
            $fieldsMessage = $fieldsCount.' champ'.($fieldsCount > 1 ? 's' : '').' rempli'.($fieldsCount > 1 ? 's' : '').' automatiquement';
            Toaster::info($fieldsMessage.' : '.implode(', ', $fieldsUpdated));
        } else {
            Toaster::warning('Aucune information n\'a pu être extraite du document.');
        }
    }

    /**
     * Réinitialise l'état OCR (utile lors du changement de fichier)
     */
    public function resetOcrState(): void
    {
        $this->isOcrProcessing = false;
        $this->showOcrButton = false;
        $this->ocrData = null;
        $this->ocrProgress = 0;
        $this->ocrService = null;
    }
}
