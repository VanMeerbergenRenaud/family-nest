<?php

namespace App\Services;

use Aws\Result;
use Aws\Textract\Exception\TextractException;
use Aws\Textract\TextractClient;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TextractService
{
    protected ?TextractClient $textractClient = null;

    protected string $ocrSpaceApiKey;

    protected int $timeout = 60;

    protected int $maxFileSize = 10 * 1024 * 1024;

    private const SUPPORTED_FORMATS = ['pdf', 'jpg', 'jpeg', 'png'];

    private const TOTAL_PATTERNS = [
        'very_high' => [
            '/total(?:\s+ttc|\s+tvac)?\s*:?\s*(\d{1,3}(?:\.\d{3})*(?:,\d{1,2})?)\s*€/i',
            '/montant(?:\s+total)?\s*:?\s*(\d{1,3}(?:\.\d{3})*(?:,\d{1,2})?)\s*€/i',
            '/à\s+payer\s*:?\s*(\d{1,3}(?:\.\d{3})*(?:,\d{1,2})?)\s*€/i',
            '/(\d{1,3}(?:\.\d{3})*(?:,\d{1,2})?)\s*€\s*(?:ttc|tvac)/i',
            '/net\s+à\s+payer\s*:?\s*(\d{1,3}(?:\.\d{3})*(?:,\d{1,2})?)\s*€/i',
        ],
        'high' => [
            '/montant\s+total\s+à\s+payer[^0-9€]*([0-9]{1,3}(?:\.[0-9]{3})*(?:,[0-9]{1,2})?)\s*€?/i',
            '/net\s+à\s+payer[^0-9€]*([0-9]{1,3}(?:\.[0-9]{3})*(?:,[0-9]{1,2})?)\s*€?/i',
            '/total\s+ttc[^0-9€]*([0-9]{1,3}(?:\.[0-9]{3})*(?:,[0-9]{1,2})?)\s*€?/i',
            '/total\s+tvac[^0-9€]*([0-9]{1,3}(?:\.[0-9]{3})*(?:,[0-9]{1,2})?)\s*€?/i',
            '/montant\s+total\s+facture[^0-9€]*([0-9]{1,3}(?:\.[0-9]{3})*(?:,[0-9]{1,2})?)\s*€?/i',
            '/€\s*([0-9]{1,3}(?:\.[0-9]{3})*(?:,[0-9]{1,2})?)\s*$/im',
        ],
        'medium' => [
            '/total[^0-9€]*([0-9]{1,3}(?:\.[0-9]{3})*(?:,[0-9]{1,2})?)\s*€?/i',
            '/à\s+payer[^0-9€]*([0-9]{1,3}(?:\.[0-9]{3})*(?:,[0-9]{1,2})?)\s*€?/i',
            '/montant[^0-9€]*([0-9]{1,3}(?:\.[0-9]{3})*(?:,[0-9]{1,2})?)\s*€?/i',
            '/facture[^0-9€]*([0-9]{1,3}(?:\.[0-9]{3})*(?:,[0-9]{1,2})?)\s*€?/i',
            '/:\s*([0-9]{1,3}(?:\.[0-9]{3})*(?:,[0-9]{1,2})?)\s*€?/i',
            '/=\s*([0-9]{1,3}(?:\.[0-9]{3})*(?:,[0-9]{1,2})?)\s*€?/i',
        ],
    ];

    private const EXCLUDE_PATTERNS = [
        '/sous[\s-]?total/i',
        '/h\.?t\.?v?\.?a?\.?/i',
        '/hors\s+tax/i',
        '/tva\s*\d+\s*%/i',
        '/acompte/i',
        '/abonnement/i',
        '/consommation/i',
        '/frais\s+d[\'e]\s*inscription/i',
    ];

    /* Configuration et initialisation */
    public function __construct()
    {
        $this->initializeServices();
    }

    private function initializeServices(): void
    {
        if (config('services.textract.enabled')) {
            $this->textractClient = new TextractClient([
                'version' => 'latest',
                'region' => config('services.textract.region', 'eu-north-1'),
                'credentials' => [
                    'key' => config('services.textract.key'),
                    'secret' => config('services.textract.secret'),
                ],
            ]);
        }
        $this->ocrSpaceApiKey = config('services.ocr_space.key', 'helloworld');
    }

    /* Méthodes principales */
    public function analyzeInvoice(string|UploadedFile $file): array
    {
        $startTime = time();
        $sessionId = uniqid('ocr_', true);

        Log::info('Début analyse OCR', [
            'session_id' => $sessionId,
            'file_name' => $this->getFileName($file),
            'file_size' => $this->getFileSize($file),
        ]);

        // Validation
        $preCheck = $this->performPreChecks($file);
        if (! $preCheck['success']) {
            Log::error('Validation échouée', ['session_id' => $sessionId, 'error' => $preCheck['message']]);

            return $preCheck;
        }

        // Services à tester
        $services = [];
        if (config('services.textract.enabled') && $this->textractClient) {
            $services[] = ['name' => 'textract', 'method' => 'tryTextractAnalyzeExpense'];
        }
        if (config('services.ocr_space.enabled')) {
            $services[] = ['name' => 'ocr_space', 'method' => 'tryOcrSpace'];
        }

        $lastError = null;

        foreach ($services as $service) {
            if ($this->isTimeoutReached($startTime)) {
                return $this->timeoutResponse($startTime, $sessionId);
            }

            try {
                $result = $this->{$service['method']}($file, $startTime);

                if ($result['success'] && ! empty($result['data']['amount'])) {
                    Log::info('OCR réussi', [
                        'service' => $service['name'],
                        'session_id' => $sessionId,
                        'duration' => time() - $startTime,
                        'amount' => $result['data']['amount'],
                    ]);

                    $result['duration'] = time() - $startTime;
                    $result['session_id'] = $sessionId;

                    return $result;
                }

                $lastError = $result;
                Log::warning('Service OCR échoué', [
                    'service' => $service['name'],
                    'session_id' => $sessionId,
                    'error' => $result['message'] ?? 'Aucun montant trouvé',
                ]);

            } catch (Exception $e) {
                $lastError = $this->errorResponse("Erreur {$service['name']}: ".$e->getMessage(), $service['name']);
                Log::error('Service OCR échoué', [
                    'service' => $service['name'],
                    'session_id' => $sessionId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::error('Tous les services OCR ont échoué', ['session_id' => $sessionId]);

        return $lastError ?: $this->errorResponse('Aucun service OCR activé ou tous ont échoué');
    }

    protected function performPreChecks(string|UploadedFile $file): array
    {
        if (! config('services.textract.enabled') && ! config('services.ocr_space.enabled')) {
            return $this->errorResponse('Aucun service OCR activé');
        }

        $fileSize = $this->getFileSize($file);
        if ($fileSize === 0) {
            return $this->errorResponse('Fichier vide ou inexistant');
        }
        if ($fileSize > $this->maxFileSize) {
            return $this->errorResponse('Fichier trop volumineux');
        }

        $extension = $this->getFileExtension($file);
        if (! in_array($extension, self::SUPPORTED_FORMATS)) {
            return $this->errorResponse('Format non supporté');
        }

        return ['success' => true];
    }

    /*----------------------------
        ÉTAPE 1 : AWS Textract
     ----------------------------*/
    protected function tryTextractAnalyzeExpense(string|UploadedFile $file, int $startTime): array
    {
        try {
            $fileContent = $this->getFileContent($file);
            $result = $this->textractClient->analyzeExpense([
                'Document' => ['Bytes' => $fileContent],
                '@http' => ['timeout' => $this->getRemainingTimeout($startTime)],
            ]);

            $extractedData = $this->extractDataFromAnalyzeExpenseResult($result);

            return $this->successResponse($extractedData, 'textract_analyze_expense');

        } catch (TextractException $e) {
            $message = $e->getAwsErrorCode() === 'UnsupportedDocumentException'
                ? 'Format de document non supporté par AWS Textract'
                : 'Erreur AWS Textract: '.$e->getAwsErrorCode();

            return $this->errorResponse($message, 'textract_analyze_expense');
        }
    }

    protected function extractDataFromAnalyzeExpenseResult(Result $textractResult): array
    {
        $data = [
            'name' => null, 'reference' => null, 'issuer_name' => null,
            'issuer_website' => null, 'amount' => null, 'issued_date' => null,
            'payment_due_date' => null, 'raw_fields' => [],
        ];

        $expenseDocuments = $textractResult->get('ExpenseDocuments');
        if (empty($expenseDocuments) || ! isset($expenseDocuments[0]['SummaryFields'])) {
            return $data;
        }

        $summaryFields = $expenseDocuments[0]['SummaryFields'];
        $fieldsMap = [];

        foreach ($summaryFields as $field) {
            $type = $field['Type']['Text'] ?? null;
            $value = $field['ValueDetection']['Text'] ?? null;
            $confidence = $field['ValueDetection']['Confidence'] ?? null;

            if ($type && $value !== null) {
                $fieldsMap[$type] = $value;
            }

            $data['raw_fields'][] = [
                'type' => $type,
                'value' => $value,
                'label' => $field['LabelDetection']['Text'] ?? null,
                'confidence' => $confidence,
            ];
        }

        // Mappage des champs
        $data['issuer_name'] = $fieldsMap['VENDOR_NAME'] ?? null;
        $data['issuer_website'] = $fieldsMap['VENDOR_URL'] ?? null;
        $data['reference'] = $fieldsMap['INVOICE_RECEIPT_ID'] ?? $fieldsMap['RECEIVER_DOCUMENT_NUMBER'] ?? $fieldsMap['ORDER_ID'] ?? null;
        $data['amount'] = $this->extractAmountFromFields($fieldsMap);
        $data['issued_date'] = $this->parseAndFormatDate($fieldsMap['INVOICE_RECEIPT_DATE'] ?? $fieldsMap['EXPENSE_RECEIPT_DATE'] ?? $fieldsMap['RECEIPT_DATE'] ?? null);
        $data['payment_due_date'] = $this->parseAndFormatDate($fieldsMap['DUE_DATE'] ?? null);
        $data['name'] = $this->generateInvoiceName($data['issuer_name'], $data['issued_date']);

        return $data;
    }

    protected function extractAmountFromFields(array $fieldsMap): ?string
    {
        $potentialFields = ['TOTAL', 'TAX', 'AMOUNT_DUE', 'NET_AMOUNT', 'SUBTOTAL'];

        foreach ($potentialFields as $field) {
            if (isset($fieldsMap[$field])) {
                $amount = $this->normalizeAmountForTextract($fieldsMap[$field]);
                if ($amount !== null) {
                    return $amount;
                }
            }
        }

        return null;
    }

    protected function normalizeAmountForTextract(?string $amountString): ?string
    {
        if (empty($amountString)) {
            return null;
        }

        $cleanAmount = preg_replace('/[€$£¥]/u', '', $amountString);

        // Cas spéciaux
        if (preg_match('/^(\d{1,3})\s+(\d{2})$/', $cleanAmount, $matches)) {
            return $matches[1].'.'.$matches[2];
        }

        if (preg_match('/^(\d{1,3}(?:\s+\d{3})+)\s+(\d{2})$/', $cleanAmount, $matches)) {
            return str_replace(' ', '', $matches[1]).'.'.$matches[2];
        }

        $result = str_replace(' ', '', $cleanAmount);

        // Format européen
        if (str_contains($result, ',') && (! str_contains($result, '.') || strrpos($result, ',') > strrpos($result, '.'))) {
            $result = str_replace('.', '', $result);
            $result = str_replace(',', '.', $result);
        }

        if (! is_numeric($result)) {
            $result = preg_replace('/[^\d.]/', '', $result);
        }

        return is_numeric($result) ? number_format((float) $result, 2, '.', '') : null;
    }

    /*----------------------------
        ÉTAPE 2 : OCR SPACE
     ----------------------------*/
    protected function tryOcrSpace(string|UploadedFile $file, int $startTime): array
    {
        try {
            $fileContent = $this->getFileContent($file);
            $fileName = $this->getFileName($file);
            $extension = $this->getFileExtension($file);

            // Configuration optimisée pour la vitesse
            $params = [
                'apikey' => $this->ocrSpaceApiKey,
                'language' => 'fre',
                'OCREngine' => '1', // Moteur plus rapide
                'scale' => 'true',
                'isOverlayRequired' => 'false',
                'isCreateSearchablePdf' => 'false',
                'isSearchablePdfHideTextLayer' => 'false',
            ];

            if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                $params['isTable'] = 'true';
            }

            if ($extension === 'pdf') {
                $params['filetype'] = 'pdf';
            }

            $response = Http::timeout($this->getRemainingTimeout($startTime))
                ->attach('file', $fileContent, $fileName)
                ->post('https://api.ocr.space/parse/image', $params);

            if (! $response->successful()) {
                throw new Exception('Requête OCR.space échouée: '.$response->status());
            }

            $ocrResult = $response->json();

            if ($ocrResult['IsErroredOnProcessing'] ?? false) {
                $errorMessage = $this->parseOcrSpaceError($ocrResult);

                if (str_contains($errorMessage, 'maximum page limit') && ! empty($ocrResult['ParsedResults'])) {
                    $extractedText = $this->extractTextFromOcrSpace($ocrResult);
                    $data = $this->extractInvoiceDataFromText($extractedText);
                    $data['page_limit_warning'] = true;

                    return $this->successResponse($data, 'ocr.space');
                }

                return $this->errorResponse($errorMessage, 'ocr.space');
            }

            $extractedText = $this->extractTextFromOcrSpace($ocrResult);
            if (empty(trim($extractedText))) {
                return $this->errorResponse('OCR.space n\'a extrait aucun texte', 'ocr.space');
            }

            $data = $this->extractInvoiceDataFromText($extractedText);

            return $this->successResponse($data, 'ocr.space');

        } catch (Exception $e) {
            return $this->errorResponse('Erreur OCR.space: '.$e->getMessage(), 'ocr.space');
        }
    }

    protected function parseOcrSpaceError(array $ocrResult): string
    {
        $errorMessage = $ocrResult['ErrorMessage'] ?? 'Unknown OCR.space error';

        if (is_array($errorMessage)) {
            if (isset($errorMessage[0])) {
                return str_contains($errorMessage[0], 'maximum page limit') ? 'maximum page limit' : $errorMessage[0];
            }

            return json_encode($errorMessage);
        }

        return (string) $errorMessage;
    }

    protected function extractTextFromOcrSpace(array $result): string
    {
        $text = '';
        foreach ($result['ParsedResults'] ?? [] as $parsed) {
            $text .= ($parsed['ParsedText'] ?? '')."\n";
        }

        return $text;
    }

    protected function extractInvoiceDataFromText(string $text): array
    {
        $lines = $this->getTextLines($text);
        $issuedDate = $this->extractDateWithRegex($text, 'issued');
        $issuerName = $this->extractIssuerNameWithRegex($lines);

        return [
            'name' => $this->generateInvoiceName($issuerName, $issuedDate),
            'reference' => $this->extractReferenceWithRegex($text),
            'issuer_name' => $issuerName,
            'issuer_website' => $this->extractWebsiteWithRegex($text),
            'amount' => $this->extractTotalAmountWithRegex($text, $lines),
            'issued_date' => $issuedDate,
            'payment_due_date' => $this->extractDateWithRegex($text, 'due'),
            'raw_text_length' => strlen($text),
        ];
    }

    /* Extraction de montants (par regex) */
    protected function extractTotalAmountWithRegex(string $text, array $lines): ?string
    {
        $allCandidates = [];

        // Chercher avec les patterns prioritaires
        foreach (self::TOTAL_PATTERNS as $priority => $patterns) {
            foreach ($patterns as $pattern) {
                if (preg_match_all($pattern, $text, $matches, PREG_OFFSET_CAPTURE)) {
                    // Évalue chaque montant trouvé avec ce pattern
                    $candidates = $this->evaluateAmountCandidatesWithRegex($matches[1], $text, $priority);
                    $allCandidates = array_merge($allCandidates, $candidates);
                }
            }
        }

        // Solution de dernier recours : recherche tous les montants
        if (empty($allCandidates)) {
            $allAmounts = $this->findAllAmountsWithRegex($lines);
            foreach ($allAmounts as &$amount) {
                $amount['priority'] = 'low';
            }
            $allCandidates = $allAmounts;
        }

        return $this->selectBestAmountFromAllCandidatesWithRegex($allCandidates);
    }

    protected function evaluateAmountCandidatesWithRegex(array $matches, string $fullText, string $priority): array
    {
        $candidates = [];
        foreach ($matches as $match) {
            $amount = $this->normalizeAmountForRegex($match[0]);
            $floatValue = floatval($amount);
            if ($floatValue >= 0.01) {
                $candidates[] = [
                    'value' => $amount,
                    'float' => $floatValue,
                    'position' => $match[1] / max(1, strlen($fullText)),
                    'priority' => $priority,
                ];
            }
        }

        return $candidates;
    }

    protected function findAllAmountsWithRegex(array $lines): array
    {
        $amounts = [];
        $pattern = '/([0-9]{1,3}(?:\.[0-9]{3})*(?:,[0-9]{1,2})?|[0-9]+,[0-9]{1,2})\s*€?/';

        foreach ($lines as $index => $line) {
            if ($this->shouldExcludeLineWithRegex($line)) {
                continue;
            }

            if (preg_match_all($pattern, $line, $matches)) {
                foreach ($matches[1] as $match) {
                    $cleanAmount = $this->normalizeAmountForRegex($match);
                    if (floatval($cleanAmount) >= 1) {
                        $amounts[] = [
                            'value' => $cleanAmount,
                            'float' => floatval($cleanAmount),
                            'line' => $line,
                            'index' => $index,
                            'position' => $index / max(1, count($lines)),
                        ];
                    }
                }
            }
        }

        return $amounts;
    }

    protected function selectBestAmountFromAllCandidatesWithRegex(array $candidates): ?string
    {
        if (empty($candidates)) {
            return null;
        }

        usort($candidates, function ($a, $b) {
            $priorityOrder = ['very_high' => 3, 'high' => 2, 'medium' => 1, 'low' => 0, 'very_low' => -1];

            $priorityA = $priorityOrder[$a['priority']] ?? 0;
            $priorityB = $priorityOrder[$b['priority']] ?? 0;

            // Si les priorités sont différentes, on les compare
            if ($priorityA !== $priorityB) {
                /* Le sens de la comparaison est inversé (B <=> A au lieu de A <=> B), donc le code
                   trie les éléments par priorité décroissante ('very_high', 'high', etc.) */
                return $priorityB <=> $priorityA;
            }

            // Si les priorités sont égales, on compare les valeurs flottantes
            return ($b['float'] ?? 0) <=> ($a['float'] ?? 0);
        });

        // Retourne la valeur du premier candidat, ou null si aucun candidat n'est trouvé
        return $candidates[0]['value'] ?? null;
    }

    protected function shouldExcludeLineWithRegex(string $line): bool
    {
        foreach (self::EXCLUDE_PATTERNS as $pattern) {
            if (preg_match($pattern, $line)) {
                return true;
            }
        }

        return false;
    }

    protected function normalizeAmountForRegex(string $amount): string
    {
        $amount = preg_replace('/\s+/', '', $amount);

        if (str_contains($amount, ',') && (! str_contains($amount, '.') || strrpos($amount, ',') > strrpos($amount, '.'))) {
            $amount = str_replace('.', '', $amount);
            $amount = str_replace(',', '.', $amount);
        } else {
            $amount = str_replace(',', '', $amount);
        }

        return preg_match('/^\d+(\.\d{1,2})?$/', $amount) ? $amount : preg_replace('/[^\d.]/', '', $amount);
    }

    /*  Extraction d'informations Nom, Référence, Site, Date */
    protected function extractIssuerNameWithRegex(array $lines): ?string
    {
        // Analyse les 5 premières lignes pour trouver un nom de fournisseur
        foreach (array_slice($lines, 0, 5) as $line) {
            $line = trim($line);

            /* Cherche une ligne qui a plus de 2 et moins de 60 caractères
             - Pas de chiffres consécutifs de 2 ou plus
             - Pas de mots comme "facture", "reçu", "date" ou "total" */
            if (strlen($line) > 2 && strlen($line) < 60 &&
                ! preg_match('/\d{2,}/', $line) &&
                ! preg_match('/(facture|reçu|date|total)/i', $line)) {
                return $line;
            }
        }

        // Si aucune ligne n'est trouvée, on retourne null
        return null;
    }

    protected function extractReferenceWithRegex(string $text): ?string
    {
        // Les patterns recherchent "facture n°" ou "document n°" suivi d'un identifiant.
        $patterns = [
            '/(facture|document)\s*n°\s*:?\s*([A-Z0-9\-\/]+)/i',
            '/n°\s*(?:de\s*)?(?:facture|reference|document)\s*:?\s*([A-Z0-9\-\/]+)/i',
        ];

        // Cette boucle examine chaque pattern et essaye de trouver une correspondance
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                // Retourne le dernier texte qui contient l'identifiant de la facture sans les textes comme "facture n°".
                return $matches[count($matches) - 1];
            }
        }

        return null;
    }

    protected function extractWebsiteWithRegex(string $text): ?string
    {
        $pattern = '/(?:https?:\/\/|www\.)[\w\-.\/?=&%#~]+\.[a-z]{2,6}(?:\/[\w\-.\/?=&%#~]*)?/i';

        if (preg_match($pattern, $text, $matches)) {
            $url = rtrim($matches[0], '.,;:!?');
            if (! preg_match('/^https?:\/\//i', $url) && stripos($url, 'www.') === 0) {
                return 'https://'.$url;
            }

            return $url;
        }

        $simpleDomainPattern = '/\b(?:[a-z0-9\-]+\.)+[a-z]{2,6}\b/i';
        if (preg_match($simpleDomainPattern, $text, $matches)) {
            $domain = $matches[0];
            if (preg_match('/^(?:rue|avenue|boulevard|place|mail|email)\b/i', $domain) ||
                preg_match('/\d{2,}/', $domain) && ! str_contains($domain, '.')) {
                return null;
            }

            return 'https://'.$domain;
        }

        return null;
    }

    protected function extractDateWithRegex(string $text, string $type): ?string
    {
        $patterns = match ($type) {
            'issued' => [
                '/date\s+(?:de\s+)?facture\s*:?\s*(\d{1,2}[\/.\-]\d{1,2}[\/.\-]\d{2,4})/i',
                '/date\s*:?\s*(\d{1,2}[\/.\-]\d{1,2}[\/.\-]\d{2,4})/i',
            ],
            'due' => [
                '/(?:date\s+d[\'e]?)?échéance\s*:?\s*(\d{1,2}[\/.\-]\d{1,2}[\/.\-]\d{2,4})/i',
            ],
            default => []
        };

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return $this->parseAndFormatDate($matches[1]);
            }
        }

        return null;
    }

    /* Formatage de donnée */
    protected function generateInvoiceName(?string $issuerName, ?string $issuedDate): ?string
    {
        // Si le nom du fournisseur est vide, on utilise la date d'émission
        if (empty($issuerName)) {
            return $issuedDate
                ? 'Facture '.$this->formatDateForName($issuedDate)
                : 'Nom de facture inconnu';
        }

        // Je récupère grâce à une expression régulière le nom du fournisseur
        $cleanName = preg_replace('/[^\p{L}\p{N}\s_.-]/u', '', $issuerName);
        $cleanName = substr(trim($cleanName), 0, 30);

        $name = 'Facture '.$cleanName;

        // Si la date d'émission est fournie, on l'ajoute au nom
        if ($issuedDate) {
            $name .= ' '.$this->formatDateForName($issuedDate);
        }

        return trim($name);
    }

    protected function getTextLines(string $text): array
    {
        return array_values(array_filter(array_map('trim', explode("\n", $text)), 'strlen'));
    }

    protected function parseAndFormatDate(?string $dateString): ?string
    {
        if (empty($dateString)) {
            return null;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateString)) {
            try {
                return (new \DateTime($dateString))->format('Y-m-d');
            } catch (Exception $e) {
                Log::error('Date format error: '.$e->getMessage());
            }
        }

        $formats = ['d/m/Y', 'd-m-Y', 'd.m.Y', 'd/m/y', 'm/d/Y', 'm-d-Y', 'm.d.Y'];
        foreach ($formats as $format) {
            if ($date = \DateTime::createFromFormat($format, $dateString)) {
                return $date->format('Y-m-d');
            }
        }

        return null;
    }

    private function formatDateForName(string $isoDate): string
    {
        try {
            $date = \DateTime::createFromFormat('Y-m-d', $isoDate);
            if ($date) {
                $months = [1 => 'Jan', 2 => 'Fév', 3 => 'Mar', 4 => 'Avr', 5 => 'Mai', 6 => 'Juin',
                    7 => 'Juil', 8 => 'Août', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Déc'];

                return $months[(int) $date->format('n')].' '.$date->format('Y');
            }
        } catch (Exception $e) {
            Log::error('Date format error: '.$e->getMessage());
        }

        return '';
    }

    /*  Utilitaires de fichiers */
    protected function getFileContent(string|UploadedFile $file): string
    {
        return $file instanceof UploadedFile ? file_get_contents($file->getRealPath()) : file_get_contents($file);
    }

    protected function getFileName(string|UploadedFile $file): string
    {
        return $file instanceof UploadedFile ? $file->getClientOriginalName() : basename($file);
    }

    protected function getFileSize(string|UploadedFile $file): int
    {
        return $file instanceof UploadedFile ? $file->getSize() : (file_exists($file) ? filesize($file) : 0);
    }

    protected function getFileExtension(string|UploadedFile $file): string
    {
        return strtolower($file instanceof UploadedFile
            ? $file->getClientOriginalExtension()
            : pathinfo($file, PATHINFO_EXTENSION));
    }

    /*  Gestion du timeout */
    protected function getRemainingTimeout(int $startTime): int
    {
        return max(1, $this->timeout - (time() - $startTime));
    }

    protected function isTimeoutReached(int $startTime): bool
    {
        return (time() - $startTime) >= $this->timeout;
    }

    /* Construction de réponses */
    protected function successResponse(array $data, string $service): array
    {
        return ['success' => true, 'data' => $data, 'service' => $service];
    }

    protected function errorResponse(string $message, ?string $service = null): array
    {
        return ['success' => false, 'message' => $message, 'service' => $service];
    }

    protected function timeoutResponse(int $startTime, string $sessionId): array
    {
        Log::error('Timeout OCR', ['session_id' => $sessionId, 'duration' => $this->timeout]);

        return [
            'success' => false,
            'message' => 'Timeout après '.$this->timeout.' secondes',
            'timeout' => true,
            'duration' => time() - $startTime,
        ];
    }
}
