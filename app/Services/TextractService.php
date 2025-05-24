<?php

namespace App\Services;

use Aws\Textract\TextractClient;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class TextractService
{
    protected ?TextractClient $textractClient = null;
    protected string $ocrSpaceApiKey;
    protected int $timeout = 60;

    // Patterns pour identifier les montants totaux
    private const TOTAL_PATTERNS = [
        // Patterns très spécifiques (score élevé)
        'high' => [
            '/montant\s+total\s+à\s+payer[^0-9€]*([0-9\s.,]+)\s*€?/i',
            '/net\s+à\s+payer[^0-9€]*([0-9\s.,]+)\s*€?/i',
            '/total\s+ttc[^0-9€]*([0-9\s.,]+)\s*€?/i',
            '/total\s+tvac[^0-9€]*([0-9\s.,]+)\s*€?/i',
            '/montant\s+total\s+facture[^0-9€]*([0-9\s.,]+)\s*€?/i',
            '/paiement\s+unique[^0-9€]*([0-9\s.,]+)\s*€?/i',
            '/€\s*([0-9\s.,]+)\s*$/i', // Montant à la fin d'une ligne
        ],
        // Patterns moyens
        'medium' => [
            '/total[^0-9€]*([0-9\s.,]+)\s*€?/i',
            '/à\s+payer[^0-9€]*([0-9\s.,]+)\s*€?/i',
            '/montant[^0-9€]*([0-9\s.,]+)\s*€?/i',
        ]
    ];

    // Patterns pour exclure les faux positifs
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

    public function analyzeInvoice(string|UploadedFile $file): array
    {
        $startTime = time();

        if (!$this->hasEnabledService()) {
            return $this->errorResponse('Aucun service OCR n\'est activé');
        }

        try {
            // Essayer Textract en premier si disponible
            if (config('services.textract.enabled')) {
                $result = $this->tryTextract($file, $startTime);
                if ($result['success']) {
                    return $result;
                }
            }

            // Fallback vers OCR.space
            if (config('services.ocr_space.enabled')) {
                return $this->tryOcrSpace($file, $startTime);
            }

            return $this->errorResponse('Tous les services OCR ont échoué');

        } catch (Exception $e) {
            Log::error('Erreur OCR générale', ['error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage());
        }
    }

    protected function tryTextract($file, int $startTime): array
    {
        if ($this->isTimeoutReached($startTime)) {
            return $this->timeoutResponse($startTime);
        }

        try {
            $fileContent = $this->getFileContent($file);

            $result = $this->textractClient->analyzeDocument([
                'Document' => ['Bytes' => $fileContent],
                'FeatureTypes' => ['FORMS', 'TABLES'],
                '@http' => ['timeout' => $this->getRemainingTimeout($startTime)]
            ]);

            $extractedText = $this->extractTextFromTextract($result);
            return $this->successResponse(
                $this->extractInvoiceData($extractedText),
                'textract',
                time() - $startTime
            );

        } catch (Exception $e) {
            Log::warning('Textract failed', ['error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage(), 'textract');
        }
    }

    protected function tryOcrSpace($file, int $startTime): array
    {
        if ($this->isTimeoutReached($startTime)) {
            return $this->timeoutResponse($startTime);
        }

        try {
            $response = Http::timeout($this->getRemainingTimeout($startTime))
                ->attach('file', $this->getFileContent($file), $this->getFileName($file))
                ->post('https://api.ocr.space/parse/image', [
                    'apikey' => $this->ocrSpaceApiKey,
                    'language' => 'fre',
                    'isTable' => 'true',
                    'OCREngine' => '2',
                ]);

            if (!$response->successful()) {
                throw new Exception('OCR.space request failed: ' . $response->status());
            }

            $ocrResult = $response->json();

            if ($ocrResult['IsErroredOnProcessing'] ?? true) {
                throw new Exception($ocrResult['ErrorMessage'] ?? 'Unknown OCR.space error');
            }

            $extractedText = $this->extractTextFromOcrSpace($ocrResult);
            return $this->successResponse(
                $this->extractInvoiceData($extractedText),
                'ocr.space',
                time() - $startTime
            );

        } catch (Exception $e) {
            Log::error('OCR.space failed', ['error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage(), 'ocr.space');
        }
    }

    protected function extractInvoiceData(string $text): array
    {
        $lines = $this->getTextLines($text);

        return [
            'name' => $this->generateInvoiceName(
                $this->extractIssuerName($lines),
                $this->extractDate($text, 'issued')
            ),
            'reference' => $this->extractReference($text),
            'issuer_name' => $this->extractIssuerName($lines),
            'issuer_website' => $this->extractWebsite($text),
            'amount' => $this->extractTotalAmount($text, $lines),
            'issued_date' => $this->extractDate($text, 'issued'),
            'payment_due_date' => $this->extractDate($text, 'due'),
        ];
    }

    protected function extractTotalAmount(string $text, array $lines): ?string
    {
        // Stratégie 1 : Chercher avec les patterns spécifiques
        foreach (self::TOTAL_PATTERNS['high'] as $pattern) {
            if (preg_match_all($pattern, $text, $matches, PREG_OFFSET_CAPTURE)) {
                $candidates = $this->evaluateAmountCandidates($matches[1], $text);
                if ($amount = $this->selectBestAmount($candidates)) {
                    return $amount;
                }
            }
        }

        // Stratégie 2 : Chercher tous les montants et les évaluer
        $allAmounts = $this->findAllAmounts($lines);
        if ($amount = $this->selectBestAmountFromAll($allAmounts)) {
            return $amount;
        }

        // Stratégie 3 : Patterns moins spécifiques
        foreach (self::TOTAL_PATTERNS['medium'] as $pattern) {
            if (preg_match_all($pattern, $text, $matches, PREG_OFFSET_CAPTURE)) {
                $candidates = $this->evaluateAmountCandidates($matches[1], $text);
                if ($amount = $this->selectBestAmount($candidates)) {
                    return $amount;
                }
            }
        }

        return null;
    }

    protected function findAllAmounts(array $lines): array
    {
        $amounts = [];
        $pattern = '/([0-9]{1,3}(?:[.\s]?[0-9]{3})*[,.]?[0-9]{0,2})\s*€?/';

        foreach ($lines as $index => $line) {
            if ($this->shouldExcludeLine($line)) {
                continue;
            }

            if (preg_match_all($pattern, $line, $matches)) {
                foreach ($matches[1] as $match) {
                    $cleanAmount = $this->normalizeAmount($match);
                    $floatValue = floatval($cleanAmount);

                    if ($floatValue >= 1) {
                        $amounts[] = [
                            'value' => $cleanAmount,
                            'float' => $floatValue,
                            'line' => $line,
                            'index' => $index,
                            'position' => $index / max(count($lines), 1),
                        ];
                    }
                }
            }
        }

        return $amounts;
    }

    protected function selectBestAmountFromAll(array $amounts): ?string
    {
        if (empty($amounts)) {
            return null;
        }

        // Privilégier les montants dans le dernier tiers du document
        $lastThird = array_filter($amounts, fn($a) => $a['position'] > 0.67);

        if (!empty($lastThird)) {
            // Prendre le plus gros montant du dernier tiers
            usort($lastThird, fn($a, $b) => $b['float'] <=> $a['float']);

            // Vérifier si c'est vraiment un total
            $candidate = $lastThird[0];
            if ($this->looksLikeTotal($candidate['line'])) {
                return $candidate['value'];
            }
        }

        // Sinon, prendre le plus gros montant global
        usort($amounts, fn($a, $b) => $b['float'] <=> $a['float']);
        return $amounts[0]['value'];
    }

    protected function looksLikeTotal(string $line): bool
    {
        $totalKeywords = ['total', 'payer', 'ttc', 'tvac', 'net', 'montant'];
        $line = strtolower($line);

        foreach ($totalKeywords as $keyword) {
            if (str_contains($line, $keyword)) {
                return true;
            }
        }

        return false;
    }

    protected function shouldExcludeLine(string $line): bool
    {
        foreach (self::EXCLUDE_PATTERNS as $pattern) {
            if (preg_match($pattern, $line)) {
                return true;
            }
        }
        return false;
    }

    protected function evaluateAmountCandidates(array $matches, string $fullText): array
    {
        $candidates = [];

        foreach ($matches as $match) {
            $amount = $this->normalizeAmount($match[0]);
            $floatValue = floatval($amount);

            if ($floatValue >= 1) {
                $position = $match[1] / strlen($fullText);
                $candidates[] = [
                    'value' => $amount,
                    'float' => $floatValue,
                    'position' => $position,
                ];
            }
        }

        return $candidates;
    }

    protected function selectBestAmount(array $candidates): ?string
    {
        if (empty($candidates)) {
            return null;
        }

        // Privilégier les montants en fin de document
        usort($candidates, function($a, $b) {
            // D'abord par position (plus tard = mieux)
            if (abs($a['position'] - $b['position']) > 0.1) {
                return $b['position'] <=> $a['position'];
            }
            // Ensuite par valeur (plus gros = mieux)
            return $b['float'] <=> $a['float'];
        });

        return $candidates[0]['value'];
    }

    protected function normalizeAmount(string $amount): string
    {
        // Enlever les espaces et normaliser les séparateurs
        $amount = preg_replace('/\s+/', '', $amount);
        $amount = str_replace(',', '.', $amount);

        // S'assurer du format décimal correct
        if (preg_match('/^(\d+)\.(\d{2})$/', $amount, $matches)) {
            return $matches[1] . '.' . $matches[2];
        }

        return $amount;
    }

    protected function extractIssuerName(array $lines): ?string
    {
        // Chercher dans les 5 premières lignes non vides
        foreach (array_slice($lines, 0, 5) as $line) {
            if (strlen($line) > 2 && strlen($line) < 60 &&
                !preg_match('/(facture|invoice|devis|reçu|document)/i', $line)) {
                return $line;
            }
        }
        return null;
    }

    protected function extractReference(string $text): ?string
    {
        $patterns = [
            '/(facture|invoice|document)\s*n°\s*:?\s*([A-Z0-9\-\/]+)/i',
            '/n°\s*(?:de\s*)?(?:facture|reference|document)\s*:?\s*([A-Z0-9\-\/]+)/i',
            '/référence\s*:?\s*([A-Z0-9\-\/]+)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return $matches[count($matches) - 1];
            }
        }

        return null;
    }

    protected function extractWebsite(string $text): ?string
    {
        if (preg_match('/https?:\/\/\S+|www\.\S+\.[a-z]{2,}/i', $text, $matches)) {
            $url = $matches[0];
            return str_starts_with($url, 'http') ? $url : 'http://' . $url;
        }
        return null;
    }

    protected function extractDate(string $text, string $type): ?string
    {
        $patterns = match($type) {
            'issued' => [
                '/date\s+(?:de\s+)?facture\s*:?\s*(\d{1,2}[\/.\-]\d{1,2}[\/.\-]\d{2,4})/i',
                '/date\s*:?\s*(\d{1,2}[\/.\-]\d{1,2}[\/.\-]\d{2,4})/i',
            ],
            'due' => [
                '/(?:date\s+d[\'e]?)?échéance\s*:?\s*(\d{1,2}[\/.\-]\d{1,2}[\/.\-]\d{2,4})/i',
                '/payer\s+avant\s+le\s*:?\s*(\d{1,2}[\/.\-]\d{1,2}[\/.\-]\d{2,4})/i',
            ],
            default => []
        };

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return $this->parseDate($matches[1]);
            }
        }

        return null;
    }

    protected function parseDate(string $dateString): ?string
    {
        $formats = ['d/m/Y', 'd-m-Y', 'd.m.Y', 'd/m/y'];

        foreach ($formats as $format) {
            if ($date = \DateTime::createFromFormat($format, $dateString)) {
                return $date->format('Y-m-d');
            }
        }

        return null;
    }

    protected function generateInvoiceName(?string $issuerName, ?string $issuedDate): ?string
    {
        if (!$issuerName) {
            return null;
        }

        $cleanName = preg_replace('/[^\p{L}\p{N}\s]/u', '', $issuerName);
        $cleanName = substr($cleanName, 0, 30);

        $name = 'Facture ' . trim($cleanName);

        if ($issuedDate && $date = \DateTime::createFromFormat('Y-m-d', $issuedDate)) {
            $months = [
                1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
            ];

            $name .= ' ' . $months[(int)$date->format('n')] . ' ' . $date->format('Y');
        }

        return $name;
    }

    // Méthodes utilitaires
    protected function getFileContent($file): string
    {
        return $file instanceof UploadedFile
            ? file_get_contents($file->getRealPath())
            : file_get_contents($file);
    }

    protected function getFileName($file): string
    {
        return $file instanceof UploadedFile
            ? $file->getClientOriginalName()
            : basename($file);
    }

    protected function getTextLines(string $text): array
    {
        return array_values(array_filter(
            array_map('trim', explode("\n", $text)),
            'strlen'
        ));
    }

    protected function extractTextFromTextract($result): string
    {
        $lines = [];
        foreach ($result['Blocks'] ?? [] as $block) {
            if ($block['BlockType'] === 'LINE') {
                $lines[] = $block['Text'];
            }
        }
        return implode("\n", $lines);
    }

    protected function extractTextFromOcrSpace(array $result): string
    {
        $text = '';
        foreach ($result['ParsedResults'] ?? [] as $parsed) {
            $text .= $parsed['ParsedText'] . "\n";
        }
        return $text;
    }

    // Helpers pour les réponses
    protected function successResponse(array $data, string $service, int $duration): array
    {
        return [
            'success' => true,
            'data' => $data,
            'service' => $service,
            'duration' => $duration
        ];
    }

    protected function errorResponse(string $message, ?string $service = null): array
    {
        return [
            'success' => false,
            'message' => $message,
            'service' => $service
        ];
    }

    protected function timeoutResponse(int $startTime): array
    {
        return [
            'success' => false,
            'message' => 'Timeout après ' . $this->timeout . ' secondes',
            'timeout' => true,
            'duration' => time() - $startTime
        ];
    }

    protected function hasEnabledService(): bool
    {
        return config('services.textract.enabled') || config('services.ocr_space.enabled');
    }

    protected function isTimeoutReached(int $startTime): bool
    {
        return (time() - $startTime) >= $this->timeout;
    }

    protected function getRemainingTimeout(int $startTime): int
    {
        return max(1, $this->timeout - (time() - $startTime));
    }
}
