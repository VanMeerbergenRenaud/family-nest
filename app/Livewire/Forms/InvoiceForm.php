<?php

namespace App\Livewire\Forms;

use App\Enums\InvoiceTypeEnum;
use App\Jobs\CompressPdfJob;
use App\Models\Invoice;
use App\Models\InvoiceFile;
use App\Traits\FormatSizeTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Form;

class InvoiceForm extends Form
{
    use FormatSizeTrait;

    public ?Invoice $invoice = null;

    public ?InvoiceFile $invoiceFile = null;

    // Fichier uploadé
    #[Validate]
    public $uploadedFile;

    public $existingFilePath = null;

    public $fileName = null;

    public $filePath = null;

    public $fileExtension = null;

    public $fileSize = null;

    public $is_primary = true;

    // Informations générales
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

    // Montants
    #[Validate]
    public $amount;

    public $currency = 'EUR';

    // Nouveaux champs pour la gestion des paiements par membre de famille
    public $family_id;  // ID de la famille

    public $paid_by_user_id;  // ID de l'utilisateur qui paie la facture

    public $paid_by;     // Pour compatibilité avec l'ancien système

    public $user_shares = [];      // Nouvelle version - array de [id => user_id, amount => montant, percentage => pourcentage]

    // Dates
    #[Validate]
    public $issued_date;

    #[Validate]
    public $payment_due_date;

    #[Validate]
    public $payment_reminder;

    #[Validate]
    public $payment_frequency;

    // Paiement
    public $payment_status;

    public $payment_method;

    public $priority;

    // Notes et tags
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

    // Règles de validation
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
            'currency' => 'nullable|string|size:3', // 3 pour le code ISO
            'family_id' => 'nullable|exists:families,id',
            'paid_by_user_id' => 'nullable|exists:users,id',
            'paid_by' => 'nullable|string|max:255',
            'user_shares' => 'nullable|array',
            'user_shares.*.id' => 'required|exists:users,id',
            'user_shares.*.amount' => 'nullable|numeric|min:0',
            'user_shares.*.percentage' => 'nullable|numeric|min:0|max:100',

            // Étape 3 - Dates
            'issued_date' => 'nullable|date',
            'payment_due_date' => 'nullable|date',
            'payment_reminder' => 'nullable|string|max:255',
            'payment_frequency' => 'nullable|string|max:255',

            // Étape 4 - Statut de paiement
            'payment_status' => 'nullable|string|in:unpaid,paid,late,partially_paid',
            'payment_method' => 'nullable|in:card,cash,transfer',
            'priority' => 'nullable|in:high,medium,low,none',

            // Étape 5 - Notes et tags
            'notes' => 'nullable|string|min:3|max:500',
            'tags' => 'nullable|array',
            'tagInput' => 'nullable|string',

            // États
            'is_archived' => 'boolean',
            'is_favorite' => 'boolean',
        ];
    }

    // Messages d'erreur personnalisés
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
            'issued_date.date' => "La date d'émission doit être une date valide.",
            'payment_due_date.date' => "La date d'échéance doit être une date valide.",
            'payment_status.in' => 'Le statut de paiement doit être parmi : non-payée, payée, en retard, ou partiellement payée.',
            'payment_method.in' => 'La méthode de paiement doit être parmi : carte, espèces ou virement.',
            'priority.in' => 'La priorité doit être parmi : haute, moyenne, basse.',
            'tags.array' => 'Les tags doivent être un tableau.',
            'user_shares.*.amount' => 'Le montant de la part doit être un nombre valide.',
            'user_shares.*.percentage' => 'Le pourcentage doit être entre 0 et 100.',
            'paid_by_user_id.exists' => 'L\'utilisateur sélectionné n\'existe pas.',
            'family_id.exists' => 'La famille sélectionnée n\'existe pas.',
        ];
    }

    // Obtenir les informations sur le fichier uploadé
    public function getFileInfo(): ?array
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

    // Traiter le fichier uploadé et récupérer ses informations
    public function processUploadedFile(): bool
    {
        if (! $this->uploadedFile) {
            return false;
        }

        $this->fileName = $this->uploadedFile->getClientOriginalName();
        $this->fileExtension = strtolower($this->uploadedFile->getClientOriginalExtension());
        $this->fileSize = $this->uploadedFile->getSize();

        // Par défaut, définir comme fichier principal
        if (! isset($this->is_primary)) {
            $this->is_primary = true;
        }

        return true;
    }

    // Supprimer le fichier
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

    // Mettre à jour la liste des catégories disponibles
    public function updateAvailableCategories(): void
    {
        if ($this->type) {
            foreach (InvoiceTypeEnum::cases() as $case) {
                if ($case->value === $this->type) {
                    $this->availableCategories = $case->categories();

                    return;
                }
            }
        }

        $this->availableCategories = [];
    }

    // Méthode pour créer ou modifier une facture
    public function saveInvoice()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Normaliser le montant avant stockage
            $amount = $this->normalizeAmount($this->amount);

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
                'paid_by' => $this->paid_by,
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
                $invoiceData['user_id'] = auth()->user()->id;
                $invoice = auth()->user()->invoices()->create($invoiceData);
            }

            // Traitement des parts d'utilisateurs
            $this->processInvoiceShares($invoice);

            // Traitement du fichier si présent
            if ($this->uploadedFile) {
                // Traiter le fichier
                $this->processUploadedFile();

                // Stocker le fichier
                $userPath = 'invoices/user_'.auth()->user()->id;
                $this->filePath = $this->uploadedFile->store($userPath, 's3');

                // Récupérer l'ancien fichier si édition
                $oldFile = null;
                if ($this->invoice) {
                    $oldFile = InvoiceFile::where('invoice_id', $invoice->id)
                        ->where('is_primary', true)
                        ->first();

                    // Supprimer l'ancien fichier du stockage si il existe
                    if ($oldFile && Storage::disk('s3')->exists($oldFile->getRawOriginal('file_path'))) {
                        Storage::disk('s3')->delete($oldFile->getRawOriginal('file_path'));
                    }
                }

                // Mise à jour ou création de l'enregistrement de fichier
                $fileData = [
                    'file_path' => $this->filePath,
                    'file_name' => $this->fileName,
                    'file_extension' => $this->fileExtension,
                    'file_size' => $this->fileSize,
                    'is_primary' => true,
                    'compression_status' => null,
                    'original_size' => null,
                    'compression_rate' => null,
                ];

                if ($oldFile) {
                    // Mettre à jour l'enregistrement de fichier existant
                    $oldFile->update($fileData);
                    $this->invoiceFile = $oldFile;
                } else {
                    // Créer un nouvel enregistrement de fichier
                    $fileData['invoice_id'] = $invoice->id;
                    $this->invoiceFile = InvoiceFile::create($fileData);
                }

                // Si c'est un PDF, marquer pour compression
                /*if ($this->fileExtension === 'pdf') {
                    $this->invoiceFile->update([
                        'compression_status' => 'pending',
                        'original_size' => $this->fileSize,
                    ]);

                    // Dispatch le job de compression
                    CompressPdfJob::dispatch(
                        $this->invoiceFile->id,
                        $this->filePath,
                        $this->fileSize
                    );
                }*/
            }

            DB::commit();

            return $invoice;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du traitement de la facture: '.$e->getMessage());

            return false;
        }
    }

    // Traite les parts d'utilisateurs pour la facture
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

    public function store()
    {
        return $this->saveInvoice();
    }

    public function update()
    {
        if (! $this->invoice) {
            throw new \Exception('Impossible de mettre à jour une facture sans son ID');
        }

        return $this->saveInvoice();
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

            // Supprimer les fichiers de la base de données
            $this->invoice->files()->delete();

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

    // Définir les données à partir d'une facture existante
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
        $this->paid_by = $invoice->paid_by;
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

    // Définir les données à partir d'un fichier de facture existant
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

    // Normaliser le montant avant stockage
    private function normalizeAmount($amount): ?float
    {
        // Si le montant est vide ou null, retourner null
        if ($amount === null || $amount === '') {
            return null;
        }

        // Si le montant est déjà un nombre, le formater avec exactement 2 décimales
        if (is_numeric($amount)) {
            return (float) number_format((float) $amount, 2, '.', '');
        }

        // Convertir en chaîne si ce n'est pas déjà le cas
        $amount = (string) $amount;

        // Supprimer les espaces (que le mask ajoute comme séparateurs de milliers)
        $amount = str_replace(' ', '', $amount);

        // Convertir la virgule en point (format standard pour PHP)
        $amount = str_replace(',', '.', $amount);

        // Conversion en float et formatage avec exactement 2 décimales
        return (float) number_format((float) $amount, 2, '.', '');
    }
}
