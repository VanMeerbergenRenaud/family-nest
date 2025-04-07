<?php

namespace App\Services;

use Aws\Textract\TextractClient;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class TextractService
{
    protected TextractClient $textractClient;

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
    }

    /**
     * Analyse une facture avec OCR et extrait les informations
     *
     * @param  string|UploadedFile  $file  Fichier uploadé ou chemin temporaire
     * @return array Données extraites de la facture
     */
    public function analyzeInvoice(string|UploadedFile $file): array
    {
        if (! config('services.textract.enabled')) {
            return ['success' => false, 'message' => 'Service Textract non activé'];
        }

        try {
            if ($file instanceof UploadedFile) {
                $fileContent = file_get_contents($file->getRealPath());
            } else {
                $fileContent = file_get_contents($file);
            }

            $result = $this->textractClient->analyzeDocument([
                'Document' => [
                    'Bytes' => $fileContent,
                ],
                'FeatureTypes' => ['FORMS', 'TABLES'],
            ]);

            $extractedData = $this->processTextractResponse($result);

            return [
                'success' => true,
                'data' => $extractedData,
            ];
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'analyse Textract: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Erreur lors de l\'analyse OCR: '.$e->getMessage(),
            ];
        }
    }

    // Traite la réponse de Textract pour extraire les données structurées
    protected function processTextractResponse($result): array
    {
        $extractedData = [
            'name' => null,
            'reference' => null,
            'issuer_name' => null,
            'issuer_website' => null,
            'amount' => null,
            'issued_date' => null,
            'payment_due_date' => null,
        ];

        $blocks = $result['Blocks'] ?? [];
        $keyValuePairs = [];
        $lines = [];
        $allText = '';

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

        if (! $extractedData['issuer_name']) {
            foreach ($keyValuePairs as $key => $value) {
                if (preg_match('/(fournisseur|societe|company|emetteur|issuer|vendor|entreprise|fourni par|par|from)/i', $key)) {
                    $extractedData['issuer_name'] = $value;
                    break;
                }
            }

            if (! $extractedData['issuer_name'] && count($lines) > 0) {
                foreach (array_slice($lines, 0, 5) as $line) {
                    if (! preg_match('/(facture|invoice|devis|reçu)/i', $line)) {
                        $extractedData['issuer_name'] = $line;
                        break;
                    }
                }
            }
        }

        if (! $extractedData['amount']) {
            foreach ($keyValuePairs as $key => $value) {
                if (preg_match('/(montant|amount|total|ttc|à payer|a payer|somme|price|prix)/i', $key)) {
                    preg_match('/[\d\s.,€$]+/', $value, $matches);
                    if (! empty($matches)) {
                        $amount = preg_replace('/[^\d.,]/', '', $matches[0]);
                        $extractedData['amount'] = str_replace(',', '.', $amount);
                        break;
                    }
                }
            }

            if (! $extractedData['amount']) {
                $patterns = [
                    '/total\s*:?\s*([\d\s.,]+)[\s€$]/i',
                    '/montant\s*:?\s*([\d\s.,]+)[\s€$]/i',
                    '/ttc\s*:?\s*([\d\s.,]+)[\s€$]/i',
                    '/à payer\s*:?\s*([\d\s.,]+)[\s€$]/i',
                    '/somme\s*:?\s*([\d\s.,]+)[\s€$]/i',
                ];

                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $allText, $matches) && isset($matches[1])) {
                        $amount = preg_replace('/[^\d.,]/', '', $matches[1]);
                        $extractedData['amount'] = str_replace(',', '.', $amount);
                        break;
                    }
                }
            }
        }

        $datePatterns = [
            '/(date\s+d[\'e]mission|date\s+facture|date\s+du\s+document|émis\s+le|emis\s+le|date\s+:)\s*:?\s*(\d{1,2}[\/\.\-]\d{1,2}[\/\.\-]\d{2,4}|\d{1,2}\s+[a-zéûôàçù]+\s+\d{2,4})/i',
            '/(date\s+d[\'e]ch[ée]ance|[ée]ch[ée]ance|date\s+limite|paiement\s+avant\s+le|due\s+date|payer\s+avant\s+le)\s*:?\s*(\d{1,2}[\/\.\-]\d{1,2}[\/\.\-]\d{2,4}|\d{1,2}\s+[a-zéûôàçù]+\s+\d{2,4})/i',
        ];

        foreach ($lines as $line) {
            if (! $extractedData['issued_date'] && preg_match($datePatterns[0], $line, $matches)) {
                $extractedData['issued_date'] = $this->parseDate($matches[2]);
            }

            if (! $extractedData['payment_due_date'] && preg_match($datePatterns[1], $line, $matches)) {
                $extractedData['payment_due_date'] = $this->parseDate($matches[2]);
            }
        }

        if (! $extractedData['issued_date']) {
            foreach ($keyValuePairs as $key => $value) {
                if (preg_match('/(date|emission|emise|issued|facture)/i', $key)) {
                    $extractedData['issued_date'] = $this->parseDate($value);
                    break;
                }
            }
        }

        if (! $extractedData['issuer_website']) {
            foreach ($keyValuePairs as $key => $value) {
                if (preg_match('/(site|web|website|url|internet)/i', $key)) {
                    if (filter_var($value, FILTER_VALIDATE_URL) || preg_match('/www\..+\..+/i', $value)) {
                        $extractedData['issuer_website'] = $value;
                        break;
                    }
                }
            }

            if (! $extractedData['issuer_website']) {
                preg_match('/https?:\/\/\S+|www\.\S+\.[a-z]{2,}/i', $allText, $urlMatches);
                if (! empty($urlMatches)) {
                    $extractedData['issuer_website'] = $urlMatches[0];
                    if (! str_starts_with($extractedData['issuer_website'], 'http')) {
                        $extractedData['issuer_website'] = 'http://'.$extractedData['issuer_website'];
                    }
                }
            }
        }

        if (! $extractedData['reference']) {
            foreach ($keyValuePairs as $key => $value) {
                if (preg_match('/(facture|invoice|n°|numero|number|ref|référence|reference|commande|order)/i', $key)) {
                    $extractedData['reference'] = $value;
                    break;
                }
            }

            if (! $extractedData['reference']) {
                $refPatterns = [
                    '/facture\s+n[o°]\s*:?\s*([A-Z0-9\-_\/]+)/i',
                    '/facture\s*:?\s*([A-Z0-9\-_\/]+)/i',
                    '/invoice\s+number\s*:?\s*([A-Z0-9\-_\/]+)/i',
                    '/reference\s*:?\s*([A-Z0-9\-_\/]+)/i',
                    '/n[o°]\s*:?\s*([A-Z0-9\-_\/]+)/i',
                ];

                foreach ($refPatterns as $pattern) {
                    if (preg_match($pattern, $allText, $matches) && isset($matches[1])) {
                        $extractedData['reference'] = $matches[1];
                        break;
                    }
                }
            }
        }

        if (! $extractedData['name'] && $extractedData['issuer_name']) {
            $extractedData['name'] = 'Facture '.$extractedData['issuer_name'];
            if ($extractedData['issued_date']) {
                $date = \DateTime::createFromFormat('Y-m-d', $extractedData['issued_date']);
                if ($date) {
                    $extractedData['name'] .= ' - '.$date->format('m/Y');
                }
            }
        }

        return $extractedData;
    }

    // Tente de parser une date dans différents formats
    protected function parseDate($dateString): ?string
    {
        $dateString = trim($dateString);

        $formats = [
            'd/m/Y',
            'd-m-Y',
            'd.m.Y',
            'Y-m-d',
            'Y/m/d',
            'd M Y',
            'j F Y',
            'd/m/y',
            'j/n/Y',
            'j/n/y',
        ];

        $frenchMonths = [
            'janvier' => '01',
            'fevrier' => '02',
            'février' => '02',
            'mars' => '03',
            'avril' => '04',
            'mai' => '05',
            'juin' => '06',
            'juillet' => '07',
            'aout' => '08',
            'août' => '08',
            'septembre' => '09',
            'octobre' => '10',
            'novembre' => '11',
            'decembre' => '12',
            'décembre' => '12',
        ];

        foreach ($frenchMonths as $month => $number) {
            if (preg_match('/(\d{1,2})\s*'.$month.'\s*(\d{4}|\d{2})/i', $dateString, $matches)) {
                $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                $year = $matches[2];

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
            $year = $matches[3];

            if (strlen($year) == 2) {
                $year = '20'.$year;
            }

            if (checkdate((int) $month, (int) $day, (int) $year)) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
        }

        return null;
    }

    // Obtient le texte de la clé à partir d'un bloc clé-valeur
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

    // Obtient la valeur associée à une clé à partir d'un bloc clé-valeur
    protected function getValueFromBlock($keyBlock, $blocks): ?string
    {
        foreach ($keyBlock['Relationships'] ?? [] as $relationship) {
            if ($relationship['Type'] === 'VALUE') {
                $valueIds = $relationship['Ids'];

                foreach ($valueIds as $valueId) {
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

    // Extrait les données de la facture à partir des paires clé-valeur et du texte brut
    protected function extractInvoiceData($keyValuePairs, $allText): array
    {
        $data = [
            'name' => null,
            'reference' => null,
            'issuer_name' => null,
            'issuer_website' => null,
            'amount' => null,
            'issued_date' => null,
            'payment_due_date' => null,
        ];

        foreach ($keyValuePairs as $key => $value) {
            if (preg_match('/(facture|invoice|n°|numero|number|ref)/i', $key)) {
                $data['reference'] = $value;
            } elseif (preg_match('/(fournisseur|societe|company|emetteur|issuer|vendor)/i', $key)) {
                $data['issuer_name'] = $value;
            } elseif (preg_match('/(montant|amount|total|ttc)/i', $key)) {
                preg_match('/[\d.,]+/', $value, $matches);
                if (! empty($matches)) {
                    $data['amount'] = str_replace(',', '.', $matches[0]);
                }
            } elseif (preg_match('/(date|emission|emise|issued)/i', $key)) {
                $data['issued_date'] = $this->parseDate($value);
            } elseif (preg_match('/(paiement|payment|due|echeance)/i', $key)) {
                $data['payment_due_date'] = $this->parseDate($value);
            } elseif (preg_match('/(site|web|website|url)/i', $key)) {
                if (filter_var($value, FILTER_VALIDATE_URL)) {
                    $data['issuer_website'] = $value;
                }
            }
        }

        if (! $data['issuer_website']) {
            preg_match('/https?:\/\/\S+/', $allText, $urlMatches);
            if (! empty($urlMatches)) {
                $data['issuer_website'] = $urlMatches[0];
            }
        }

        if (! $data['amount']) {
            preg_match('/total\s*:?\s*([\d.,]+)/i', $allText, $amountMatches);
            if (! empty($amountMatches)) {
                $data['amount'] = str_replace(',', '.', $amountMatches[1]);
            }
        }

        if (! $data['name'] && $data['issuer_name']) {
            $data['name'] = 'Facture '.$data['issuer_name'];
            if ($data['issued_date']) {
                $date = \DateTime::createFromFormat('Y-m-d', $data['issued_date']);
                if ($date) {
                    $data['name'] .= ' - '.$date->format('m/Y');
                }
            }
        }

        return $data;
    }
}
