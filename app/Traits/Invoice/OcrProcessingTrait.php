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

    // Mapping des erreurs vers leurs types pour un traitement uniforme
    private const ERROR_MAPPINGS = [
        'timeout' => ['timeout' => true, 'message' => 'Le traitement a pris trop de temps. Essayez avec un fichier plus petit (max 10 Mo).'],
        'file_too_large' => ['keywords' => ['Fichier trop volumineux'], 'message' => 'Fichier trop volumineux. La taille maximale est de 10 Mo.'],
        'unsupported_format' => ['keywords' => ['format du document n\'est pas supporté', 'UnsupportedDocumentException'], 'message' => 'Format non supporté. Utilisez PDF, JPG, JPEG ou PNG.'],
        'page_limit' => ['keywords' => ['maximum page limit'], 'message' => 'Document trop long. Seules les 3 premières pages ont été analysées.', 'level' => 'warning'],
        'no_service' => ['keywords' => ['Aucun service OCR'], 'message' => 'Aucun service OCR n\'est configuré. Contactez l\'administrateur.'],
        'all_services_failed' => ['keywords' => ['Tous les services OCR ont échoué'], 'message' => 'L\'analyse a échoué. Vérifiez que le document est lisible et réessayez.'],
    ];

    // Déclenche l'affichage du bouton OCR lors du changement de fichier
    public function updatedFormUploadedFile(): void
    {
        $this->showOcrButton = true;
    }

    // Point d'entrée principal pour le traitement OCR
    public function processOcr(TextractService $textractService): void
    {
        if (! $this->form->uploadedFile) {
            Toaster::error('Veuillez d\'abord télécharger un fichier.');

            return;
        }

        $this->initializeOcrProcess();

        try {
            $result = $this->performOcrAnalysis($textractService);
            $this->handleOcrResult($result);
        } catch (\Exception $e) {
            $this->handleOcrException($e);
        } finally {
            $this->finalizeOcrProcess();
        }
    }

    // Initialise l'état du processus OCR
    private function initializeOcrProcess(): void
    {
        $this->isOcrProcessing = true;
        $this->ocrProgress = 0;
        $this->ocrService = $this->determineActiveService();
        $this->dispatch('ocr-processing-started');
    }

    // Détermine quel service OCR est actif
    private function determineActiveService(): ?string
    {
        return match (true) {
            config('services.textract.enabled') => 'AWS Textract',
            config('services.ocr_space.enabled') => 'OCR.space',
            default => null,
        };
    }

    // Effectue l'analyse OCR avec gestion du fichier temporaire
    private function performOcrAnalysis(TextractService $textractService): array
    {
        $tempPath = $this->storeTemporaryFile();

        try {
            return $textractService->analyzeInvoice(storage_path('app/'.$tempPath));
        } finally {
            $this->cleanupTemporaryFile($tempPath);
        }
    }

    // Stocke temporairement le fichier pour l'analyse
    private function storeTemporaryFile(): string
    {
        return $this->form->uploadedFile->storeAs(
            'temp',
            $this->form->uploadedFile->getClientOriginalName(),
            'local'
        );
    }

    // Nettoie le fichier temporaire
    private function cleanupTemporaryFile(string $path): void
    {
        $fullPath = storage_path('app/'.$path);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    // Traite le résultat de l'analyse OCR
    private function handleOcrResult(array $result): void
    {
        if ($result['success']) {
            $this->ocrData = $result['data'];
            $this->applyOcrDataToForm();
        } else {
            $this->handleOcrError($result);
        }
    }

    // Gère les exceptions du processus OCR
    private function handleOcrException(\Exception $e): void
    {
        Toaster::error('Une erreur inattendue est survenue. Veuillez réessayer.');
        Log::error('Exception lors du traitement OCR: '.$e->getMessage(), [
            'trace' => $e->getTraceAsString(),
        ]);
    }

    // Finalise le processus OCR
    private function finalizeOcrProcess(): void
    {
        $this->isOcrProcessing = false;
        $this->ocrService = null;
        $this->dispatch('ocr-processing-completed');
    }

    // Gestion centralisée des erreurs OCR
    protected function handleOcrError(array $result): void
    {
        $errorType = $this->determineErrorType($result);
        $errorConfig = self::ERROR_MAPPINGS[$errorType] ?? null;

        if (! $errorConfig) {
            $this->handleGenericError($result);

            return;
        }

        $level = $errorConfig['level'] ?? 'error';
        $message = $errorConfig['message'];

        Toaster::{$level}($message);
        $this->logError($errorType, $result);
    }

    // Détermine le type d'erreur basé sur le résultat
    protected function determineErrorType(array $result): string
    {
        $message = $result['message'] ?? '';

        if (isset($result['timeout']) && $result['timeout']) {
            return 'timeout';
        }

        foreach (self::ERROR_MAPPINGS as $type => $config) {
            if (isset($config['keywords'])) {
                foreach ($config['keywords'] as $keyword) {
                    if (str_contains($message, $keyword)) {
                        return $type;
                    }
                }
            }
        }

        return 'generic';
    }

    // Gère les erreurs génériques non catégorisées
    private function handleGenericError(array $result): void
    {
        Toaster::error('Impossible d\'analyser le document::Vérifiez qu\'il ne soit pas plus long que 3 pages ou protégé par un mot de passe.');
        Log::error('Erreur OCR non catégorisée', [
            'message' => $result['message'] ?? 'Erreur inconnue',
            'service' => $result['service'] ?? 'unknown',
        ]);
    }

    // Log l'erreur avec le contexte approprié
    private function logError(string $errorType, array $result): void
    {
        $context = ['message' => $result['message']];

        match ($errorType) {
            'timeout' => $context += [
                'duration' => $result['duration'] ?? 60,
                'file_size' => $this->form->uploadedFile->getSize(),
            ],
            'file_too_large' => $context += [
                'size' => $this->form->uploadedFile->getSize(),
            ],
            'unsupported_format' => $context += [
                'file_type' => $this->form->uploadedFile->getClientOriginalExtension(),
            ],
            default => null,
        };

        Log::warning("OCR Error: {$errorType}", $context);
    }

    // Applique les données OCR extraites au formulaire
    protected function applyOcrDataToForm(): void
    {
        if (! $this->ocrData) {
            return;
        }

        $fieldsUpdated = [];
        $fieldMappings = $this->getFieldMappings();

        foreach ($fieldMappings as $ocrField => $formField) {
            if (! empty($this->ocrData[$ocrField])) {
                $value = $this->processFieldValue($ocrField, $this->ocrData[$ocrField]);
                $this->form->{$formField['property']} = $value;
                $fieldsUpdated[] = $formField['label'];
            }
        }

        $this->showSuccessMessage($fieldsUpdated);
        $this->logSuccessfulOcr($fieldsUpdated);
    }

    // Définit le mapping entre les champs OCR et les propriétés du formulaire
    private function getFieldMappings(): array
    {
        return [
            'name' => ['property' => 'name', 'label' => 'nom'],
            'reference' => ['property' => 'reference', 'label' => 'référence'],
            'issuer_name' => ['property' => 'issuer_name', 'label' => 'fournisseur'],
            'issuer_website' => ['property' => 'issuer_website', 'label' => 'site web'],
            'amount' => ['property' => 'amount', 'label' => 'montant'],
            'issued_date' => ['property' => 'issued_date', 'label' => 'date d\'émission'],
            'payment_due_date' => ['property' => 'payment_due_date', 'label' => 'date d\'échéance'],
        ];
    }

    // Traite la valeur d'un champ selon son type
    private function processFieldValue(string $fieldName, $value)
    {
        return match ($fieldName) {
            'amount' => $this->form->normalizeAmount($value),
            default => $value,
        };
    }

    // Affiche le message de succès avec les détails appropriés
    private function showSuccessMessage(array $fieldsUpdated): void
    {
        $fieldsCount = count($fieldsUpdated);

        if ($fieldsCount === 0) {
            Toaster::warning('Le document a été analysé mais aucune information n\'a pu être extraite.');

            return;
        }

        $service = $this->ocrData['service'] ?? 'OCR';
        $duration = isset($this->ocrData['duration']) ? " ({$this->ocrData['duration']}s)" : '';
        $plural = $fieldsCount > 1;

        $message = "Analyse réussie{$duration} : {$fieldsCount} champ".($plural ? 's' : '').
            ' rempli'.($plural ? 's' : '');

        // Ajouter la liste des champs si pas trop nombreux
        if ($fieldsCount <= 3) {
            $message .= ' ('.implode(', ', $fieldsUpdated).')';
        }

        Toaster::success($message);

        // Déclencher le recalcul si nécessaire
        if (in_array('montant', $fieldsUpdated) && method_exists($this, 'calculateRemainingShares')) {
            $this->calculateRemainingShares();
        }
    }

    // Log les informations de succès OCR
    private function logSuccessfulOcr(array $fieldsUpdated): void
    {
        Log::info('OCR réussi', [
            'service' => $this->ocrData['service'] ?? 'unknown',
            'fields_count' => count($fieldsUpdated),
            'fields' => $fieldsUpdated,
            'duration' => $this->ocrData['duration'] ?? null,
        ]);
    }

    // Réinitialise complètement l'état OCR
    public function resetOcrState(): void
    {
        $this->isOcrProcessing = false;
        $this->showOcrButton = false;
        $this->ocrData = null;
        $this->ocrProgress = 0;
        $this->ocrService = null;
    }
}
