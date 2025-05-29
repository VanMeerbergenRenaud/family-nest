<?php

namespace App\Livewire\Forms;

use App\Enums\CategoryEnum;
use App\Enums\CurrencyEnum;
use App\Enums\PaymentFrequencyEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PriorityEnum;
use App\Enums\TypeEnum;
use App\Models\Invoice;
use App\Models\InvoiceFile;
use App\Models\InvoiceSharing;
use App\Services\FileStorageService;
use App\Services\InvoiceReminderService;
use App\Traits\FormatFileSizeTrait;
use App\Traits\HumanDateTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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
            'reference' => 'nullable|string|max:50',
            'type' => 'nullable|string|in:'.implode(',', array_map(fn ($case) => $case->value, TypeEnum::cases())),
            'category' => 'nullable|string|in:'.implode(',', array_map(fn ($case) => $case->value, CategoryEnum::cases())),
            'issuer_name' => 'nullable|string|max:255',
            'issuer_website' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^(https?:\/\/)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,}(:[0-9]{1,5})?(\/.*)?$/i',
            ],

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
            'issuer_website.regex' => "Le format de l'URL n'est pas valide. Assurez-vous qu'elle termine par un nom de domaine existant (.be, .com, etc.).",
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

    // Méthode générique pour la sauvegarde de la facture
    public function save(FileStorageService $fileStorageService, bool $enableSharing = false)
    {
        $this->family_id = auth()->user()->family()->id;

        $this->normalizeUrl();

        // Ne vérifier les parts que si la répartition est activée
        if ($enableSharing && ! $this->validateAndAdjustShares()) {
            $this->addError('user_shares', 'Vous devez avoir au moins 2 membres avec un montant de répartition pour utiliser cette fonctionnalité.');

            return false;
        }

        // Si la répartition n'est pas activée, réinitialiser les parts
        if (! $enableSharing) {
            $this->user_shares = [];
        }

        $this->validate();

        try {
            DB::beginTransaction();

            // Préparer les données communes
            $invoiceData = $this->prepareInvoiceData();

            // Création ou mise à jour
            if ($this->invoice) {
                $this->invoice->update($invoiceData);
                $invoice = $this->invoice;
            } else {
                $invoiceData['user_id'] = auth()->id();
                $invoice = auth()->user()->invoices()->create($invoiceData);
            }

            // Traitement des parts d'utilisateurs
            $this->processInvoiceShares($invoice);

            // Traitement du fichier si présent
            if ($this->uploadedFile) {
                $oldFile = $this->invoice
                    ? InvoiceFile::where('invoice_id', $invoice->id)
                        ->where('is_primary', true)
                        ->first()
                    : null;

                $this->invoiceFile = $fileStorageService->processInvoiceFile($invoice, $this->uploadedFile, $oldFile);
            }

            DB::commit();

            // Gérer le rappel de paiement
            if ($invoice && $this->payment_reminder) {
                app(InvoiceReminderService::class)->scheduleReminder($invoice);
                // Tester directement : auth()->user()->notify(new InvoicePaymentReminder($invoice));
            }

            return $invoice;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du traitement de la facture: '.$e->getMessage());
            Toaster::error('Une erreur est survenue lors du traitement de la facture');

            return false;
        }
    }

    // Préparer les données de la facture
    private function prepareInvoiceData(): array
    {
        // Normaliser le montant avant stockage
        $amount = $this->normalizeAmount($this->amount);

        // Vérifier que le payeur est valide
        $validPayerIds = auth()->user()->families->flatMap(function ($family) {
            return $family->users->pluck('id');
        })->push(auth()->id())->unique()->toArray();

        $payerId = $this->paid_by_user_id;
        if (! in_array($payerId, $validPayerIds)) {
            $payerId = auth()->id();
        }

        return [
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
    }

    // Rempli les données de la facture
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

        if (! $invoice->sharings->isEmpty()) {
            foreach ($invoice->sharings as $sharing) {
                $this->user_shares[] = [
                    'id' => $sharing->user_id,
                    'amount' => $sharing->share_amount,
                    'percentage' => $sharing->share_percentage,
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

    // Rempli le fichier de la facture
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

    // Met à jour les catégories en fonction du type choisi
    public function updateAvailableCategories(): void
    {
        if (empty($this->type)) {
            $this->type = null;
            $this->category = null;
            $this->availableCategories = [];

            return;
        }

        try {
            $typeEnum = $this->type instanceof TypeEnum
                ? $this->type
                : TypeEnum::tryFrom($this->type);

            if (! $typeEnum) {
                $this->type = null;
                $this->category = null;
                $this->availableCategories = [];

                return;
            }

            $this->availableCategories = [];

            foreach ($typeEnum->categoryEnums() as $category) {
                $this->availableCategories[$category->value] = $category->labelWithEmoji();
            }

            if (! empty($this->category) && ! isset($this->availableCategories[$this->category])) {
                $this->category = null;
            }
        } catch (\Throwable $e) {
            $this->type = null;
            $this->category = null;
            $this->availableCategories = [];
            Toaster::error('Erreur lors de la récupération des catégories.');
            Log::error('Erreur lors de la récupération des catégories: '.$e->getMessage());
        }
    }

    // Calculer le montant restant à répartir
    public function validateAndAdjustShares(): bool
    {
        // Si le montant total est nul, pas besoin de vérifier les répartitions
        if (floatval($this->amount ?? 0) <= 0) {
            $this->user_shares = [];

            return true;
        }

        // Filtrer pour ne garder que les parts actives (avec montant ou pourcentage > 0)
        $activeShares = array_filter($this->user_shares ?? [], function ($share) {
            return (isset($share['amount']) && floatval($share['amount']) > 0) ||
                (isset($share['percentage']) && floatval($share['percentage']) > 0);
        });

        // Si le mode de répartition est activé, mais qu'il y a moins de 2 membres actifs, la validation échoue
        if (! empty($this->user_shares) && count($activeShares) < 2) {
            return false;
        }

        // Vérifier si le total des pourcentages ne dépasse pas 100%
        $totalPercentage = 0;
        foreach ($activeShares as $share) {
            if (isset($share['percentage']) && floatval($share['percentage']) > 0) {
                $totalPercentage += floatval($share['percentage']);
            }
        }

        if ($totalPercentage > 100) {
            return false;
        }

        // Si pas de répartition active, réinitialiser complètement les parts
        if (empty($activeShares)) {
            $this->user_shares = [];
        }

        return true;
    }

    // Méthode pour traiter les parts d'utilisateurs
    private function processInvoiceShares(Invoice $invoice): void
    {
        // Supprimer tous les partages existants
        $invoice->sharings()->delete();

        // Si des parts sont définies, les créer
        if (! empty($this->user_shares)) {
            foreach ($this->user_shares as $share) {
                if (isset($share['id']) && ($share['amount'] > 0 || $share['percentage'] > 0)) {
                    InvoiceSharing::create([
                        'invoice_id' => $invoice->id,
                        'user_id' => $share['id'],
                        'share_amount' => $share['amount'],
                        'share_percentage' => $share['percentage'],
                    ]);
                }
            }
        }
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

    // Normalise l'URL du site web du fournisseur en ajoutant 'https://' si nécessaire
    public function normalizeUrl(): void
    {
        if (! empty($this->issuer_website)) {
            $this->issuer_website = Str::startsWith($this->issuer_website, ['http://', 'https://'])
                ? $this->issuer_website
                : 'https://'.$this->issuer_website;
        }
    }
}
