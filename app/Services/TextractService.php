<?php

namespace App\Services;

use Aws\Textract\TextractClient;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TextractService
{
    protected ?TextractClient $textractClient = null;

    protected string $ocrSpaceApiKey;

    public function __construct()
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

        if (config('services.ocr_space.enabled')) {
            $this->ocrSpaceApiKey = config('services.ocr_space.key', 'helloworld');
        }
    }

    public function analyzeInvoice(string|UploadedFile $file): array
    {
        if (! config('services.textract.enabled') && ! config('services.ocr_space.enabled')) {
            return ['success' => false, 'message' => 'Aucun service OCR n\'est activé'];
        }

        try {
            if (config('services.textract.enabled')) {
                try {
                    $fileContent = $file instanceof UploadedFile
                        ? file_get_contents($file->getRealPath())
                        : file_get_contents($file);

                    $result = $this->textractClient->analyzeDocument([
                        'Document' => ['Bytes' => $fileContent],
                        'FeatureTypes' => ['FORMS', 'TABLES'],
                    ]);

                    return [
                        'success' => true,
                        'data' => $this->processTextractResponse($result),
                        'service' => 'textract',
                    ];
                } catch (\Exception $e) {
                    Log::warning('AWS Textract a échoué: '.$e->getMessage());

                    if (config('services.ocr_space.enabled')) {
                        Log::info('Tentative avec OCR.space comme solution de secours...');

                        return $this->analyzeWithOcrSpace($file);
                    }

                    throw $e;
                }
            } elseif (config('services.ocr_space.enabled')) {
                return $this->analyzeWithOcrSpace($file);
            }

            throw new \Exception('Aucun service OCR disponible');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'analyse OCR: '.$e->getMessage());

            return ['success' => false, 'message' => 'Erreur lors de l\'analyse OCR: '.$e->getMessage()];
        }
    }

    protected function analyzeWithOcrSpace(string|UploadedFile $file): array
    {
        try {
            $filePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;
            $fileName = $file instanceof UploadedFile ? $file->getClientOriginalName() : basename($file);

            $response = Http::attach(
                'file', file_get_contents($filePath), $fileName
            )->post('https://api.ocr.space/parse/image', [
                'apikey' => $this->ocrSpaceApiKey,
                'language' => 'fre',
                'isOverlayRequired' => 'false',
                'isTable' => 'true',
                'OCREngine' => '2',
            ]);

            if ($response->successful()) {
                $ocrResult = $response->json();

                if (isset($ocrResult['IsErroredOnProcessing']) && $ocrResult['IsErroredOnProcessing'] === false) {
                    $allText = '';
                    foreach ($ocrResult['ParsedResults'] as $parsedResult) {
                        $allText .= $parsedResult['ParsedText'].' ';
                    }

                    return [
                        'success' => true,
                        'data' => $this->processOcrText($allText),
                        'service' => 'ocr.space',
                    ];
                } else {
                    throw new \Exception($ocrResult['ErrorMessage'] ?? 'Erreur inconnue avec OCR.space');
                }
            } else {
                throw new \Exception('Erreur de communication avec OCR.space: '.$response->status());
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'analyse OCR.space: '.$e->getMessage());

            return ['success' => false, 'message' => 'Erreur lors de l\'analyse OCR.space: '.$e->getMessage()];
        }
    }

    protected function processOcrText(string $text, array $keyValuePairs = []): array
    {
        $extractedData = [
            'name' => null, 'reference' => null, 'issuer_name' => null,
            'issuer_website' => null, 'amount' => null,
            'issued_date' => null, 'payment_due_date' => null,
        ];

        $lines = array_values(array_filter(array_map('trim', explode("\n", $text)), 'strlen'));
        $allText = $text;

        // Rechercher le nom du fournisseur
        foreach (array_slice($lines, 0, 5) as $line) {
            if (! preg_match('/(facture|invoice|devis|reçu)/i', $line) && strlen($line) > 2 && strlen($line) < 60) {
                $extractedData['issuer_name'] = $line;
                break;
            }
        }

        // Rechercher les dates
        $datePatterns = [
            // Date d'émission
            '/(date\s+d[\'e]mission|date\s+facture|date\s+du\s+document|émis\s+le|emis\s+le|date\s+:)\s*:?\s*(\d{1,2}[\/\.\-]\d{1,2}[\/\.\-]\d{2,4}|\d{1,2}\s+[a-zéûôàçù]+\s+\d{2,4})/i',
            // Date d'échéance
            '/(date\s+d[\'e]ch[ée]ance|[ée]ch[ée]ance|date\s+limite|paiement\s+avant\s+le|due\s+date|payer\s+avant\s+le)\s*:?\s*(\d{1,2}[\/\.\-]\d{1,2}[\/\.\-]\d{2,4}|\d{1,2}\s+[a-zéûôàçù]+\s+\d{2,4})/i',
        ];

        foreach ($lines as $line) {
            if (! $extractedData['issued_date'] && preg_match($datePatterns[0], $line, $matches)) {
                $extractedData['issued_date'] = $this->parseDate($matches[2]);
            }
            if (! $extractedData['payment_due_date'] && preg_match($datePatterns[1], $line, $matches)) {
                $extractedData['payment_due_date'] = $this->parseDate($matches[2]);
            }

            if (preg_match('/(total|montant|ttc|tva compris|tva incl|à payer|€)/i', $line) &&
                preg_match('/(\d{1,3}(?:[ .,]?\d{3})*[.,]\d{2})\s*(?:€|EUR)?/i', $line, $matches)) {
                $extractedData['amount'] = str_replace(',', '.', preg_replace('/[^\d.,]/', '', $matches[1]));
            }

            if (! $extractedData['issuer_website'] &&
                preg_match('/https?:\/\/\S+|www\.\S+\.[a-z]{2,}/i', $line, $matches)) {
                $extractedData['issuer_website'] = $matches[0];
                if (! str_starts_with($extractedData['issuer_website'], 'http')) {
                    $extractedData['issuer_website'] = 'http://'.$extractedData['issuer_website'];
                }
            }

            if (! $extractedData['reference'] &&
                preg_match('/(facture|invoice|n°|numero|number|ref|référence|reference)\s*:?\s*([A-Z0-9\-_\/]+)/i', $line, $matches)) {
                $extractedData['reference'] = $matches[2];
            }
        }

        // Rechercher des dates dans le texte brut si pas encore trouvé
        if (! $extractedData['issued_date'] && preg_match('/\b(\d{1,2}[\/\.\-]\d{1,2}[\/\.\-]\d{2,4})\b/', $allText, $matches)) {
            $extractedData['issued_date'] = $this->parseDate($matches[1]);
        }

        // Formater le nom de facture
        if ($extractedData['issuer_name']) {
            $cleanIssuerName = trim(preg_replace('/[^\p{L}\p{N}\s]/u', '', $extractedData['issuer_name']));

            if (strlen($cleanIssuerName) > 30) {
                $words = explode(' ', $cleanIssuerName);
                $cleanIssuerName = $words[0];
                if (strlen($cleanIssuerName) > 30) {
                    $cleanIssuerName = substr($cleanIssuerName, 0, 30);
                }
            }

            $factureName = 'Facture '.$cleanIssuerName;

            if ($extractedData['issued_date']) {
                $date = \DateTime::createFromFormat('Y-m-d', $extractedData['issued_date']);
                if ($date) {
                    $frenchMonths = [
                        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                        5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                        9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
                    ];

                    $month = $frenchMonths[(int) $date->format('n')];
                    $year = $date->format('Y');
                    $factureName .= ' '.$month.' '.$year;
                }
            }

            $extractedData['name'] = $factureName;
        }

        return $extractedData;
    }

    protected function processTextractResponse($result): array
    {
        $blocks = $result['Blocks'] ?? [];
        $keyValuePairs = [];
        $lines = [];
        $allText = '';
        $potentialCompanyNames = [];

        foreach ($blocks as $block) {
            if ($block['BlockType'] === 'LINE') {
                $lines[] = $block['Text'];
                $allText .= $block['Text'].' ';
            }
        }

        foreach ($blocks as $block) {
            if ($block['BlockType'] === 'KEY_VALUE_SET' && isset($block['EntityTypes']) && in_array('KEY', $block['EntityTypes'])) {
                $key = $this->getKeyFromBlock($block, $blocks);
                $value = $this->getValueFromBlock($block, $blocks);

                if ($key && $value) {
                    $keyValuePairs[strtolower($key)] = $value;
                }
            }
        }

        Log::debug('Textract key-value pairs:', $keyValuePairs);
        Log::debug('Textract text lines:', $lines);

        foreach (array_slice($lines, 0, 7) as $index => $line) {
            if (! preg_match('/(facture|invoice|devis|reçu|commande|date|total|montant|\d{2}\/\d{2}\/\d{4}|\d{2}\.\d{2}\.\d{4})/i', $line) &&
                ! preg_match('/^\d+(\.\d+)?$/', $line) && strlen($line) < 60 && strlen($line) > 2 &&
                ! preg_match('/^[!\@\#\$\%\^\&\*\(\)\_\+\-\=\[\]\{\}\;\'\:\"\,\.\/\<\>\?\\\\]+$/', $line)) {

                $hasWords = false;
                foreach (explode(' ', $line) as $word) {
                    if (strlen($word) > 2) {
                        $hasWords = true;
                        break;
                    }
                }

                if ($hasWords) {
                    $score = 10 - $index;
                    if (strtoupper($line) === $line) {
                        $score += 5;
                    } elseif (preg_match_all('/\b[A-Z][a-zA-Z]*\b/', $line) > 0) {
                        $score += 3;
                    }
                    if (preg_match('/(SA|SPRL|SRL|ASBL|bvba|BV|nv|NV|SCRL|scrl)(\s|$)/i', $line)) {
                        $score += 4;
                    }

                    $potentialCompanyNames[] = ['text' => $line, 'score' => $score];
                }
            }
        }

        // Utiliser le même traitement que pour OCR.space
        $extractedData = $this->processOcrText($allText, $keyValuePairs);

        // Si aucun fournisseur dans processOcrText, utiliser les potentiels noms d'entreprise
        if (! $extractedData['issuer_name'] && ! empty($potentialCompanyNames)) {
            usort($potentialCompanyNames, fn ($a, $b) => $b['score'] - $a['score']);
            $extractedData['issuer_name'] = trim($potentialCompanyNames[0]['text'], " \t\n\r\0\x0B.,;:!?\"'()[]{}<>*/\\");

            // Régénérer le nom de la facture
            $cleanIssuerName = trim(preg_replace('/[^\p{L}\p{N}\s]/u', '', $extractedData['issuer_name']));
            if (strlen($cleanIssuerName) > 30) {
                $cleanIssuerName = substr(explode(' ', $cleanIssuerName)[0], 0, 30);
            }

            $factureName = 'Facture '.$cleanIssuerName;
            if ($extractedData['issued_date']) {
                $date = \DateTime::createFromFormat('Y-m-d', $extractedData['issued_date']);
                if ($date) {
                    $frenchMonths = [
                        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
                        5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
                        9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
                    ];
                    $factureName .= ' '.$frenchMonths[(int) $date->format('n')].' '.$date->format('Y');
                }
            }
            $extractedData['name'] = $factureName;
        }

        // Rechercher spécifiquement des clés dans keyValuePairs
        foreach ($keyValuePairs as $key => $value) {
            if (preg_match('/(fournisseur|societe|company|emetteur|issuer|vendor|entreprise|fourni par|par|from|nom)/i', $key) &&
                ! preg_match('/^\d+$/', $value)) {
                $extractedData['issuer_name'] = $value;
            }
            if (preg_match('/(montant|amount|total|ttc|à payer|a payer|somme|price|prix)/i', $key)) {
                preg_match('/[\d\s.,€$]+/', $value, $matches);
                if (! empty($matches)) {
                    $extractedData['amount'] = str_replace(',', '.', preg_replace('/[^\d.,]/', '', $matches[0]));
                }
            }
            if (preg_match('/(date|emission|emise|issued|facture)/i', $key)) {
                $parsedDate = $this->parseDate($value);
                if ($parsedDate) {
                    $extractedData['issued_date'] = $parsedDate;
                }
            }
            if (preg_match('/(facture|invoice|n°|numero|number|ref|référence|reference|commande|order)/i', $key)) {
                $extractedData['reference'] = $value;
            }
        }

        return $extractedData;
    }

    protected function parseDate($dateString): ?string
    {
        $dateString = trim($dateString);
        $formats = ['d/m/Y', 'd-m-Y', 'd.m.Y', 'Y-m-d', 'Y/m/d', 'd M Y', 'j F Y', 'd/m/y', 'j/n/Y', 'j/n/y'];
        $frenchMonths = [
            'janvier' => '01', 'février' => '02', 'fevrier' => '02', 'mars' => '03', 'avril' => '04',
            'mai' => '05', 'juin' => '06', 'juillet' => '07', 'aout' => '08', 'août' => '08',
            'septembre' => '09', 'octobre' => '10', 'novembre' => '11', 'décembre' => '12', 'decembre' => '12',
        ];

        foreach ($frenchMonths as $month => $number) {
            if (preg_match('/(\d{1,2})\s*'.$month.'\s*(\d{4}|\d{2})/i', $dateString, $matches)) {
                $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                $year = strlen($matches[2]) == 2 ? '20'.$matches[2] : $matches[2];

                return "$year-$number-$day";
            }
        }

        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date) {
                return $date->format('Y-m-d');
            }
        }

        if (preg_match('/\b(\d{1,2})[\/.\-](\d{1,2})[\/.\-](\d{2,4})\b/', $dateString, $matches)) {
            $day = $matches[1];
            $month = $matches[2];
            $year = strlen($matches[3]) == 2 ? '20'.$matches[3] : $matches[3];
            if (checkdate((int) $month, (int) $day, (int) $year)) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
        }

        return null;
    }

    protected function getKeyFromBlock($block, $blocks): ?string
    {
        foreach ($block['Relationships'] ?? [] as $relationship) {
            if ($relationship['Type'] === 'CHILD') {
                foreach ($relationship['Ids'] as $id) {
                    foreach ($blocks as $childBlock) {
                        if ($childBlock['Id'] === $id && $childBlock['BlockType'] === 'WORD') {
                            return strtolower($childBlock['Text']);
                        }
                    }
                }
            }
        }

        return null;
    }

    protected function getValueFromBlock($keyBlock, $blocks): ?string
    {
        foreach ($keyBlock['Relationships'] ?? [] as $relationship) {
            if ($relationship['Type'] === 'VALUE') {
                foreach ($relationship['Ids'] as $valueId) {
                    foreach ($blocks as $block) {
                        if ($block['Id'] === $valueId) {
                            $valueText = '';
                            foreach ($block['Relationships'] ?? [] as $childRelationship) {
                                if ($childRelationship['Type'] === 'CHILD') {
                                    foreach ($childRelationship['Ids'] as $childId) {
                                        foreach ($blocks as $childBlock) {
                                            if ($childBlock['Id'] === $childId && $childBlock['BlockType'] === 'WORD') {
                                                $valueText .= $childBlock['Text'].' ';
                                            }
                                        }
                                    }
                                }
                            }

                            return trim($valueText);
                        }
                    }
                }
            }
        }

        return null;
    }
}
