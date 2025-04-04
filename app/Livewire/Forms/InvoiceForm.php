<?php

namespace App\Livewire\Forms;

use App\Enums\InvoiceCategoryEnum;
use App\Enums\InvoiceCurrencyEnum;
use App\Enums\InvoiceTypeEnum;
use App\Enums\PaymentFrequencyEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PriorityEnum;
use App\Models\Invoice;
use App\Models\InvoiceFile;
use App\Services\FileStorageService;
use App\Traits\FormatFileSizeTrait;
use App\Traits\HumanDateTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Masmerise\Toaster\Toaster;

class InvoiceForm extends Form
{
    use FormatFileSizeTrait, HumanDateTrait;

    public ?Invoice $invoice = null;

    public ?InvoiceFile $invoiceFile = null;

    // Uploaded file
    #[Validate]
    public $uploadedFile;

    public $existingFilePath = null;

    public $fileName = null;

    public $filePath = null;

    public $fileExtension = null;

    public $fileSize = null;

    public $is_primary = true;

    // 1. Informations générales
    #[Validate]
    public $name;

    #[Validate]
    public $reference;

    public $type;

    public $category;

    #[Validate]
    public $issuer_name;

    #[Validate]
    public $issuer_website;

    // 2. Montants
    #[Validate]
    public $amount;

    public $currency = 'EUR';

    public $family_id;

    public $paid_by_user_id;

    public $user_shares = [];

    // 3. Dates
    #[Validate]
    public $issued_date;

    #[Validate]
    public $payment_due_date;

    #[Validate]
    public $payment_reminder;

    public $payment_frequency;

    // 4. Paiement
    public $payment_status;

    public $payment_method;

    public $priority;

    // 5. Notes et tags
    #[Validate]
    public $notes;

    #[Validate]
    public $tags = [];

    public $tagInput = '';

    // États
    public $is_archived = false;

    public $is_favorite = false;

    public $user_id;

    // Available categories
    public $availableCategories = [];

    public function rules()
    {
        return [
            // Fichier
            'uploadedFile' => $this->existingFilePath
                ? 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:10240'
                : 'required|file|mimes:jpg,jpeg,png,pdf,docx|max:10240',

            // Étape 1 - Informations générales
            'name' => 'required|string|max:255',
            'reference' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'issuer_name' => 'nullable|string|max:255',
            'issuer_website' => 'nullable|url|max:255',

            // Étape 2 - Détails financiers
            'amount' => 'required|numeric|min:0|max:999999999.99',
            'currency' => 'nullable|string|in:'.implode(',', array_map(fn ($case) => $case->value, InvoiceCurrencyEnum::cases())),
            'paid_by_user_id' => 'exists:users,id',
            'family_id' => 'nullable|exists:families,id',
            'user_shares' => 'nullable|array',
            'user_shares.*.id' => 'required|exists:users,id',
            'user_shares.*.amount' => 'nullable|numeric|min:0',
            'user_shares.*.percentage' => 'nullable|numeric|min:0|max:100',

            // Étape 3 - Dates
            'issued_date' => 'nullable|date',
            'payment_due_date' => 'nullable|date',
            'payment_reminder' => 'nullable|date',
            'payment_frequency' => 'nullable|string|in:'.implode(',', array_map(fn ($case) => $case->value, PaymentFrequencyEnum::cases())),

            // Étape 4 - Statut de paiement
            'payment_status' => 'nullable|string|in:'.implode(',', array_map(fn ($case) => $case->value, PaymentStatusEnum::cases())),
            'payment_method' => 'nullable|in:'.implode(',', array_map(fn ($case) => $case->value, PaymentMethodEnum::cases())),
            'priority' => 'nullable|in:'.implode(',', array_map(fn ($case) => $case->value, PriorityEnum::cases())),

            // Étape 5 - Notes et tags
            'notes' => 'nullable|string|min:3|max:500',
            'tags' => 'nullable|array',
            'tagInput' => 'nullable|string',

            // États
            'is_archived' => 'boolean',
            'is_favorite' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            // Messages d'erreur pour le fichier d'importation
            'uploadedFile.required' => 'Veuillez sélectionner un fichier.',
            'uploadedFile.file' => 'Le fichier doit être un fichier valide.',
            'uploadedFile.mimes' => 'Le fichier doit être au format PDF, Word, JPEG, JPG ou PNG.',
            'uploadedFile.max' => 'Le fichier ne doit pas dépasser 10 Mo.',

            // Messages d'erreur pour les informations facture
            'name.required' => 'Le nom de la facture est obligatoire.',
            'issuer_website.url' => "L'URL du site web du fournisseur n'est pas valide.",
            'amount.required' => 'Le montant est obligatoire.',
            'amount.numeric' => 'Le montant doit être un nombre.',
            'amount.min' => 'Le montant doit être supérieur ou égal à zéro.',
            'amount.max' => 'Le montant doit être inférieur à 999 999 999,99. À moins que vous ne soyez John D. Rockefeller, auquel cas nous vous suggérons de nous contactez !',
            'paid_by_user_id.exists' => 'L\'utilisateur sélectionné n\'existe pas.',
            'family_id.exists' => 'La famille sélectionnée n\'existe pas.',
            'user_shares.*.amount' => 'Le montant de la part doit être un nombre valide.',
            'user_shares.*.percentage' => 'Le pourcentage doit être entre 0 et 100.',
            'issued_date.date' => "La date d'émission doit être une date valide.",
            'payment_due_date.date' => "La date d'échéance doit être une date valide.",
            'payment_status.in' => 'Le statut de paiement doit être parmi : non-payée, payée, en retard, ou partiellement payée.',
            'payment_method.in' => 'La méthode de paiement doit être parmi : carte, espèces ou virement.',
            'priority.in' => 'La priorité doit être parmi : haute, moyenne, basse.',
            'tags.array' => 'Les tags doivent être un tableau.',
        ];
    }

    public function removeFile(): void
    {
        $this->uploadedFile = null;
        $this->existingFilePath = null;
        $this->fileName = null;
        $this->filePath = null;
        $this->fileExtension = null;
        $this->fileSize = null;
        $this->is_primary = true;
    }

    public function updateAvailableCategories(): void
    {
        if ($this->type) {
            try {
                $typeEnum = $this->type instanceof InvoiceTypeEnum
                    ? $this->type
                    : InvoiceTypeEnum::from($this->type);

                $categoriesForType = $typeEnum->categories();

                $this->availableCategories = [];

                foreach ($categoriesForType as $category) {
                    foreach (InvoiceCategoryEnum::cases() as $case) {
                        if ($case->value === $category) {
                            // Add the emoji to the label
                            $this->availableCategories[$category] = $case->labelWithEmoji();
                            break;
                        }
                    }

                    // If no emoji found, fallback to the original category
                    if (! isset($this->availableCategories[$category])) {
                        $this->availableCategories[$category] = $category;
                    }
                }

                return;
            } catch (\ValueError) {
                $this->availableCategories = [];
                Toaster::error('Erreur lors de la récupération des catégories::Vérifiez le type de facture sélectionné.');
            }
        }
    }

    // Créer ou modifier une facture
    public function saveInvoice(FileStorageService $fileStorageService)
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Normaliser le montant avant stockage
            $amount = $this->normalizeAmount($this->amount);

            // Si le payeur n'est pas défini, le définir sur l'utilisateur connecté
            if (! $this->paid_by_user_id) {
                $this->paid_by_user_id = auth()->id();
            }

            // Préparation des données communes
            $invoiceData = [
                // Informations générales
                'name' => $this->name,
                'reference' => $this->reference,
                'type' => $this->type,
                'category' => $this->category,
                'issuer_name' => $this->issuer_name,
                'issuer_website' => $this->issuer_website,
                // Détails financiers
                'amount' => $amount,
                'currency' => $this->currency,
                'paid_by_user_id' => $this->paid_by_user_id,
                'family_id' => $this->family_id,
                // Dates
                'issued_date' => $this->issued_date,
                'payment_due_date' => $this->payment_due_date,
                'payment_reminder' => $this->payment_reminder,
                'payment_frequency' => $this->payment_frequency,
                // Statut de paiement
                'payment_status' => $this->payment_status,
                'payment_method' => $this->payment_method,
                'priority' => $this->priority,
                // Notes et tags
                'notes' => $this->notes,
                'tags' => $this->tags ?? [],
                // Archives et favoris
                'is_archived' => $this->is_archived,
                'is_favorite' => $this->is_favorite,
            ];

            // Si c'est une mise à jour ou une création
            if ($this->invoice) {
                // Mise à jour
                $this->invoice->update($invoiceData);
                $invoice = $this->invoice;
            } else {
                // Création
                $invoiceData['user_id'] = auth()->id();
                $invoice = auth()->user()->invoices()->create($invoiceData);
            }

            // Traitement des parts d'utilisateurs
            $this->processInvoiceShares($invoice);

            // Traitement du fichier si présent
            if ($this->uploadedFile) {
                // Récupérer l'ancien fichier si édition
                $oldFile = $this->invoice ? InvoiceFile::where('invoice_id', $invoice->id)
                    ->where('is_primary', true)
                    ->first() : null;

                // Stocker le fichier
                $this->invoiceFile = $fileStorageService->processInvoiceFile($invoice, $this->uploadedFile, $oldFile);
            }

            DB::commit();

            return $invoice;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du traitement de la facture: '.$e->getMessage());
            Toaster::error('Une erreur est survenue lors du traitement de la facture');

            return false;
        }
    }

    private function processInvoiceShares(Invoice $invoice): void
    {
        // Supprimer d'abord toutes les anciennes parts
        $invoice->sharedUsers()->detach();

        // Si nous avons des parts à ajouter
        if (! empty($this->user_shares)) {
            foreach ($this->user_shares as $share) {
                if (isset($share['id']) && ($share['amount'] > 0 || $share['percentage'] > 0)) {
                    $invoice->sharedUsers()->attach($share['id'], [
                        'share_amount' => $share['amount'] ?? null,
                        'share_percentage' => $share['percentage'] ?? null,
                    ]);
                }
            }
        }
    }

    public function store(FileStorageService $fileStorageService)
    {
        return $this->saveInvoice($fileStorageService);
    }

    public function update(FileStorageService $fileStorageService)
    {
        if (! $this->invoice) {
            throw new \Exception('Impossible de mettre à jour une facture sans son ID');
        }

        return $this->saveInvoice($fileStorageService);
    }

    public function archive(): bool
    {
        if (! $this->invoice) {
            return false;
        }

        try {
            $this->invoice->update([
                'is_archived' => true,
            ]);

            $this->is_archived = true;

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'archivage de la facture: '.$e->getMessage());

            return false;
        }
    }

    public function restore(): bool
    {
        if (! $this->invoice) {
            return false;
        }

        try {
            $this->invoice->update([
                'is_archived' => false,
            ]);

            $this->is_archived = false;

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la restauration de la facture: '.$e->getMessage());

            return false;
        }
    }

    public function delete(): bool
    {
        if (! $this->invoice) {
            return false;
        }

        try {
            DB::beginTransaction();

            // TODO: Supprimer les fichiers de S3 avant de supprimer les enregistrements

            $this->invoice->delete();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression définitive de la facture: '.$e->getMessage());

            return false;
        }
    }

    public function setFromInvoice(Invoice $invoice): static
    {
        $this->invoice = $invoice;

        // Informations générales
        $this->name = $invoice->name;
        $this->reference = $invoice->reference;
        $this->type = $invoice->type;
        $this->category = $invoice->category;
        $this->issuer_name = $invoice->issuer_name;
        $this->issuer_website = $invoice->issuer_website;

        // Détails financiers
        $this->amount = is_numeric($invoice->amount)
            ? (float) $invoice->amount
            : null;

        $this->currency = $invoice->currency ?? 'EUR';
        $this->paid_by_user_id = $invoice->paid_by_user_id;
        $this->family_id = $invoice->family_id;

        // Charger les parts d'utilisateurs
        $this->user_shares = [];
        foreach ($invoice->sharedUsers as $user) {
            $this->user_shares[] = [
                'id' => $user->id,
                'amount' => $user->pivot->share_amount,
                'percentage' => $user->pivot->share_percentage,
            ];
        }

        // Dates - Les formater au format Y-m-d pour les champs input
        $this->issued_date = $invoice->issued_date ? date('Y-m-d', strtotime($invoice->issued_date)) : null;
        $this->payment_due_date = $invoice->payment_due_date ? date('Y-m-d', strtotime($invoice->payment_due_date)) : null;
        $this->payment_reminder = $invoice->payment_reminder ? date('Y-m-d', strtotime($invoice->payment_reminder)) : null;
        $this->payment_frequency = $invoice->payment_frequency;

        // Statut de paiement
        $this->payment_status = $invoice->payment_status;
        $this->payment_method = $invoice->payment_method;
        $this->priority = $invoice->priority;

        // Notes et tags
        $this->notes = $invoice->notes;
        $this->tags = is_array($invoice->tags) ? $invoice->tags : [];

        // États
        $this->is_archived = $invoice->is_archived;
        $this->is_favorite = $invoice->is_favorite;

        // Récupération du fichier associé
        $invoiceFile = InvoiceFile::where('invoice_id', $invoice->id)
            ->where('is_primary', true)
            ->first();

        if ($invoiceFile) {
            $this->setFromInvoiceFile($invoiceFile);
        }

        $this->updateAvailableCategories();

        return $this;
    }

    public function setFromInvoiceFile(InvoiceFile $invoiceFile): static
    {
        $this->invoiceFile = $invoiceFile;
        $this->existingFilePath = $invoiceFile->getRawOriginal('file_path');
        $this->filePath = $invoiceFile->file_path;
        $this->fileName = $invoiceFile->file_name;
        $this->fileExtension = $invoiceFile->file_extension;
        $this->fileSize = $invoiceFile->file_size;
        $this->is_primary = $invoiceFile->is_primary ?? true;

        return $this;
    }

    public function getFileInfo(?FileStorageService $fileStorageService = null): ?array
    {
        if (! $this->uploadedFile && ! $this->fileName) {
            return null;
        }

        if ($fileStorageService) {
            if ($this->uploadedFile) {
                return $fileStorageService->getFileInfo($this->uploadedFile);
            } elseif ($this->fileName) {
                return $fileStorageService->getFileInfo(null, $this->fileName, $this->fileExtension, $this->fileSize);
            }
        }

        // Fallback si le service n'est pas fourni
        if (! $this->uploadedFile && ! $this->fileName) {
            return null;
        }

        $name = $this->fileName ?? ($this->uploadedFile ? $this->uploadedFile->getClientOriginalName() : null);
        $extension = $this->fileExtension ?? ($this->uploadedFile ? strtolower($this->uploadedFile->getClientOriginalExtension()) : null);
        $size = $this->fileSize ?? ($this->uploadedFile ? $this->uploadedFile->getSize() : 0);

        return [
            'name' => $name,
            'extension' => $extension,
            'size' => round($size / 1024, 2), // Taille en KB
            'sizeFormatted' => $this->formatFileSize($size),
            'isImage' => in_array($extension, ['jpg', 'jpeg', 'png']),
            'isPdf' => $extension === 'pdf',
            'isDocx' => $extension === 'docx',
            'isCsv' => $extension === 'csv',
        ];
    }

    public function normalizeAmount($amount): ?float
    {
        // Si le montant est vide ou null, retourner null
        if ($amount === null || $amount === '') {
            return null;
        }

        // Convertir en chaîne si ce n'est pas déjà le cas
        $amount = (string) $amount;

        // Supprimer les espaces (que le mask ajoute comme séparateurs de milliers)
        $amount = str_replace(' ', '', $amount);

        // Convertir la virgule en point (format standard pour PHP)
        $amount = str_replace(',', '.', $amount);

        // Utiliser Number::format pour garantir la précision
        return (float) number_format((float) $amount, 2, '.', '');
    }
}
