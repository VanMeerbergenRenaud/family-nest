<?php

namespace App\Livewire\Forms;

use App\Enums\CurrencyEnum;
use App\Enums\PaymentFrequencyEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PriorityEnum;
use App\Enums\TypeEnum;
use App\Models\Invoice;
use App\Models\InvoiceFile;
use App\Notifications\InvoicePaymentReminder;
use App\Services\FileStorageService;
use App\Services\InvoiceReminderService;
use App\Traits\FormatFileSizeTrait;
use App\Traits\HumanDateTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Masmerise\Toaster\Toaster;

class InvoiceForm extends Form
{
    use FormatFileSizeTrait, HumanDateTrait;

    public ?Invoice $invoice = null;

    public ?InvoiceFile $invoiceFile = null;

    // Fichier téléchargé
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

    // Catégories disponibles
    public $availableCategories = [];

    public function rules(): array
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
            'currency' => 'nullable|string|in:'.implode(',', array_map(fn ($case) => $case->value, CurrencyEnum::cases())),
            'paid_by_user_id' => 'exists:users,id',
            'family_id' => 'required|exists:families,id',
            'user_shares' => 'nullable|array',
            'user_shares.*.id' => 'required|exists:users,id',
            'user_shares.*.amount' => 'nullable|numeric|min:0',
            'user_shares.*.percentage' => 'nullable|numeric|min:0|max:100',

            // Étape 3 - Dates
            'issued_date' => 'nullable|date|after_or_equal:2020-01-01',
            'payment_due_date' => [
                'nullable',
                'date',
                'after_or_equal:2020-01-01',
                function ($attribute, $value, $fail) {
                    if (! empty($value) && ! empty($this->issued_date) && strtotime($value) < strtotime($this->issued_date)) {
                        $fail("La date d'échéance ne peut pas être antérieure à la date d'émission.");
                    }
                },
            ],
            'payment_reminder' => [
                'nullable',
                'date',
                'after_or_equal:2020-01-01',
                function ($attribute, $value, $fail) {
                    if (! empty($value) && ! empty($this->payment_due_date) && strtotime($value) > strtotime($this->payment_due_date)) {
                        $fail("La date de rappel ne peut pas être postérieure à la date d'échéance.");
                    } elseif (! empty($value) && ! empty($this->issued_date) && strtotime($value) < strtotime($this->issued_date)) {
                        $fail("La date de rappel ne peut pas être antérieure à la date d'émission.");
                    } elseif (! empty($value) && strtotime($value) < strtotime(now())) {
                        $fail('La date de rappel ne peut pas être dans le passé.');
                    }
                },
            ],
            'payment_frequency' => 'nullable|string|in:'.implode(',', array_map(fn ($case) => $case->value, PaymentFrequencyEnum::cases())),

            // Étape 4 - Statut de paiement
            'payment_status' => 'nullable|string|in:'.implode(',', array_map(fn ($case) => $case->value, PaymentStatusEnum::cases())),
            'payment_method' => 'nullable|in:'.implode(',', array_map(fn ($case) => $case->value, PaymentMethodEnum::cases())),
            'priority' => 'nullable|in:'.implode(',', array_map(fn ($case) => $case->value, PriorityEnum::cases())),

            // Étape 5 - Notes et tags
            'notes' => 'nullable|string|min:3|max:500',
            'tags' => 'nullable|array|max:10',
            'tags.*' => 'alpha', // chaque tag ne contient que des lettres
            'tagInput' => 'nullable|string|alpha|max:15', // tag en cours ne contient que des lettres

            // États
            'is_archived' => 'boolean',
            'is_favorite' => 'boolean',
        ];
    }

    public function messages(): array
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
            'family_id.required' => 'La famille est obligatoire, veuillez en créer une si vous n\'en avez pas.',
            'family_id.exists' => 'La famille sélectionnée n\'existe pas.',
            'user_shares.*.amount' => 'Le montant de la part doit être un nombre valide.',
            'user_shares.*.percentage' => 'Le pourcentage doit être entre 0 et 100.',
            'issued_date.after_or_equal' => "La date d'émission ne peut pas être antérieure à 2020.",
            'payment_due_date.after_or_equal' => "La date d'échéance ne peut pas être antérieure à 2020.",
            'payment_reminder.after_or_equal' => 'La date de rappel ne peut pas être antérieure à 2020.',
            'payment_status.in' => 'Le statut de paiement doit être parmi : non-payée, payée, en retard, ou partiellement payée.',
            'payment_method.in' => 'La méthode de paiement doit être parmi : carte, espèces ou virement.',
            'priority.in' => 'La priorité doit être parmi : haute, moyenne, basse.',
            'tags.array' => 'Les tags doivent être un tableau.',
            'tags.max' => 'Vous ne pouvez pas ajouter plus de 10 tags.',
            'tags.*.alpha' => 'Les tags ne peuvent contenir que des lettres.',
            'tags.*.max' => 'Chaque tag ne peut pas dépasser 15 caractères.',
            'tagInput.alpha' => 'Les tags ne peuvent contenir que des lettres.',
            'tagInput.max' => 'Un tag ne peut pas dépasser 15 caractères.',
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
        if (empty($this->type)) {
            $this->availableCategories = [];

            return;
        }

        try {
            $typeEnum = $this->type instanceof TypeEnum
                ? $this->type
                : TypeEnum::tryFrom($this->type);

            if (! $typeEnum) {
                return;
            }

            $this->availableCategories = [];

            foreach ($typeEnum->categoryEnums() as $category) {
                $this->availableCategories[$category->value] = $category->labelWithEmoji();
            }
        } catch (\Throwable $e) {
            $this->availableCategories = [];
            Toaster::error('Erreur lors de la récupération des catégories.');
            Log::error('Erreur lors de la récupération des catégories: '.$e->getMessage());
        }
    }

    // Create or update invoice
    public function saveInvoice(FileStorageService $fileStorageService)
    {
        $this->family_id = auth()->user()->family()->id;

        $this->validate();

        try {
            DB::beginTransaction();

            // Normaliser le montant avant stockage
            $amount = $this->normalizeAmount($this->amount);

            // Si le payeur n'est pas dans la bonne famille, on le force à être l'utilisateur authentifié
            // Vérifier que le payeur est valide, sans forcer l'utilisateur authentifié
            $validPayerIds = auth()->user()->families->flatMap(function ($family) {
                return $family->users->pluck('id');
            })->push(auth()->id())->unique()->toArray();

            $payerId = $this->paid_by_user_id;
            if (! in_array($payerId, $validPayerIds)) {
                $payerId = auth()->id();
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
                'paid_by_user_id' => $payerId,
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
                $oldFile = $this->invoice
                    ? InvoiceFile::where('invoice_id', $invoice->id)
                        ->where('is_primary', true)
                        ->first()
                    : null;

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

    public function handlePaymentReminder(Invoice $invoice): void
    {
        if ($this->payment_reminder) {
            app(InvoiceReminderService::class)->scheduleReminder($invoice);
        }
    }

    public function store(FileStorageService $fileStorageService)
    {
        $invoice = $this->saveInvoice($fileStorageService);

        if ($invoice) {
            $this->handlePaymentReminder($invoice);
        }

        return $invoice;
    }

    public function update(FileStorageService $fileStorageService)
    {
        if (! $this->invoice) {
            Toaster::error('Impossible de mettre à jour cette facture::Il y a un conflit de données.');
        }

        $invoice = $this->saveInvoice($fileStorageService);

        auth()->user()->notify(new InvoicePaymentReminder($invoice));

        if ($invoice) {
            $this->handlePaymentReminder($invoice);
        }

        return $invoice;
    }

    public function archive(): bool
    {
        if (! $this->invoice) {
            return false;
        }

        try {
            $this->invoice->update([
                'is_archived' => true,
                'is_favorite' => false,
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

            // Supprimer les fichiers associés
            $files = InvoiceFile::where('invoice_id', $this->invoice->id)->get();

            foreach ($files as $file) {
                $filePath = $file->getRawOriginal('file_path');
                if (Storage::disk('s3')->exists($filePath)) {
                    Storage::disk('s3')->delete($filePath);
                }
                $file->delete();
            }

            // Supprimer les parts associées
            $this->invoice->sharedUsers()->detach();

            // Supprimer la facture
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
        $this->type = $invoice->type instanceof \BackedEnum
            ? $invoice->type->value
            : $invoice->type;
        $this->category = $invoice->category instanceof \BackedEnum
            ? $invoice->category->value
            : $invoice->category;
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

        if (!$invoice->sharedUsers->isEmpty()) {
            foreach ($invoice->sharedUsers as $user) {
                $this->user_shares[] = [
                    'id' => $user->id,
                    'amount' => $user->pivot->share_amount,
                    'percentage' => $user->pivot->share_percentage,
                ];
            }
        }

        // Dates - Les formater au format Y-m-d pour les champs input
        $this->issued_date = $invoice->issued_date ? date('Y-m-d', strtotime($invoice->issued_date)) : null;
        $this->payment_due_date = $invoice->payment_due_date ? date('Y-m-d', strtotime($invoice->payment_due_date)) : null;
        $this->payment_reminder = $invoice->payment_reminder ? date('Y-m-d', strtotime($invoice->payment_reminder)) : null;
        $this->payment_frequency = $invoice->payment_frequency instanceof \BackedEnum
            ? $invoice->payment_frequency->value
            : $invoice->payment_frequency;

        // Statut de paiement
        $this->payment_status = $invoice->payment_status instanceof \BackedEnum
            ? $invoice->payment_status->value
            : $invoice->payment_status;
        $this->payment_method = $invoice->payment_method instanceof \BackedEnum
            ? $invoice->payment_method->value
            : $invoice->payment_method;
        $this->priority = $invoice->priority instanceof \BackedEnum
            ? $invoice->priority->value
            : $invoice->priority;

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

    public function normalizeAmount($amount): ?float
    {
        if ($amount === null || $amount === '') {
            return null;
        }

        $amount = (string) $amount;

        $amount = str_replace(' ', '', $amount);

        $amount = str_replace(',', '.', $amount);

        return (float) number_format((float) $amount, 2, '.', '');
    }

    /**
     * Normalise les montants des parts avant la sauvegarde
     * Sans forcer automatiquement une répartition à 100% ou au montant total
     */
    private function normalizeShares(): void
    {
        if (empty($this->user_shares)) {
            return;
        }

        // Simplement normaliser les valeurs numériques sans ajuster les totaux
        foreach ($this->user_shares as &$share) {
            // Normaliser le pourcentage et le montant à 2 décimales
            $share['percentage'] = round(floatval($share['percentage'] ?? 0), 2);
            $share['amount'] = round(floatval($share['amount'] ?? 0), 2);
        }
    }

    /**
     * Traite les parts d'utilisateurs avant de les sauvegarder
     */
    private function processInvoiceShares(Invoice $invoice): void
    {
        // Normaliser les parts sans forcer le total à 100% ou au montant complet
        $this->normalizeShares();

        // Détacher toutes les parts existantes
        $invoice->sharedUsers()->detach();

        // Si des parts sont définies, les attacher
        if (! empty($this->user_shares)) {
            foreach ($this->user_shares as $share) {
                if (isset($share['id']) && ($share['amount'] > 0 || $share['percentage'] > 0)) {
                    $invoice->sharedUsers()->attach($share['id'], [
                        'share_amount' => $share['amount'],
                        'share_percentage' => $share['percentage'],
                    ]);
                }
            }
        }
    }
}
