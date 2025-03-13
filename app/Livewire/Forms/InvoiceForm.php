<?php

namespace App\Livewire\Forms;

use App\Enums\InvoiceTypeEnum;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Form;

class InvoiceForm extends Form
{
    public Invoice $invoice;

    #[Validate]
    public $uploadedFile;

    public $existingFilePath = null;

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

    #[Validate]
    public $amount;

    public $currency = 'EUR';

    public $paid_by;

    public $associated_members = [];

    #[Validate]
    public $issued_date;

    #[Validate]
    public $payment_due_date;

    #[Validate]
    public $payment_reminder;

    #[Validate]
    public $payment_frequency;

    public $payment_status = 'unpaid';

    public $payment_method = 'card';

    public $priority = 'none';

    #[Validate]
    public $notes;

    #[Validate]
    public $tags = [];

    public $tagInput = '';

    public $engagement_id;

    public $engagement_name;

    public $is_archived = false;

    public $is_favorite = false;

    public $availableCategories = [];

    public function rules()
    {
        return [
            // Étape d'importation
            'uploadedFile' => $this->existingFilePath
                ? 'nullable|file|mimes:pdf,docx,jpeg,png,jpg|max:10240'
                : 'required|file|mimes:pdf,docx,jpeg,png,jpg|max:10240',
            // Étape 1
            'name' => 'required|string|max:255',
            'reference' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'issuer_name' => 'nullable|string|max:255',
            'issuer_website' => 'nullable|url|max:255',
            // Étape 2
            'amount' => 'required|numeric|min:0|max:999999999.99',
            'currency' => 'nullable|string|size:3', // 3 pour le code ISO
            'paid_by' => 'nullable|string|max:255',
            'associated_members' => 'nullable|array',
            'amount_distribution' => 'nullable|array',
            // Étape 3
            'issued_date' => 'nullable|date',
            'payment_due_date' => 'nullable|date',
            'payment_reminder' => 'nullable|string|max:255',
            'payment_frequency' => 'nullable|string|max:255',
            // Étape 4
            'engagement_id' => 'nullable|string|max:255',
            'engagement_name' => 'nullable|string|max:255',
            // Étape 5
            'payment_status' => 'nullable|string|in:unpaid,paid,late,partially_paid',
            'payment_method' => 'nullable|in:card,cash,transfer',
            'priority' => 'nullable|in:high,medium,low,none',
            // Étape 6
            'notes' => 'nullable|string|min:3|max:500',
            'tags' => 'nullable|array',
            'tagInput' => 'nullable|string',
            // Archives
            'is_archived' => 'boolean',
            // Favoris
            'is_favorite' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'uploadedFile.required' => 'Veuillez sélectionner un fichier.',
            'uploadedFile.file' => 'Le fichier doit être un fichier valide.',
            'uploadedFile.mimes' => 'Le fichier doit être au format PDF, Word, JPEG, JPG ou PNG.',
            'uploadedFile.max' => 'Le fichier ne doit pas dépasser 10 Mo.',
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
        ];
    }

    public function updateAvailableCategories()
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

    // Créer la facture
    public function store()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Vérifier que le fichier existe
            if (! $this->uploadedFile) {
                throw new \Exception('Aucun fichier fourni');
            }

            // Stocker le fichier et récupérer son chemin
            $filePath = $this->uploadedFile->store('invoices', 'public');

            // Vérifier que le stockage a réussi
            if (! $filePath) {
                throw new \Exception('Échec du stockage du fichier');
            }

            // Récupérer la taille du fichier
            $fileSize = Storage::disk('public')->size($filePath);

            // Normaliser le montant avant stockage
            $amount = $this->normalizeAmount($this->amount);

            $invoice = auth()->user()->invoices()->create([
                'user_id' => auth()->user()->id,
                /* Étape d'importation */
                'file_path' => $filePath,
                'file_size' => $fileSize,
                // Autres champs...
                'name' => $this->name,
                'reference' => $this->reference,
                'type' => $this->type,
                'category' => $this->category,
                'issuer_name' => $this->issuer_name,
                'issuer_website' => $this->issuer_website,
                /* Étape 2 */
                'amount' => $amount,
                'currency' => $this->currency,
                'paid_by' => $this->paid_by,
                'associated_members' => $this->associated_members ?? null,
                'amount_distribution' => $this->amount_distribution ?? null,
                /* Étape 3 */
                'issued_date' => $this->issued_date,
                'payment_due_date' => $this->payment_due_date,
                'payment_reminder' => $this->payment_reminder,
                'payment_frequency' => $this->payment_frequency,
                /* Étape 4 */
                'engagement_id' => $this->engagement_id,
                'engagement_name' => $this->engagement_name,
                /* Étape 5 */
                'payment_status' => $this->payment_status,
                'payment_method' => $this->payment_method,
                'priority' => $this->priority,
                /* Étape 6 */
                'notes' => $this->notes,
                'tags' => $this->tags ?? null,
                /* Archives */
                'is_archived' => $this->is_archived,
                /* Favoris */
                'is_favorite' => $this->is_favorite,
            ]);

            DB::commit();

            return $invoice;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la création de la facture: '.$e->getMessage());

            return false;
        }
    }

    // Récupérer la facture
    public function setInvoice(Invoice $invoice)
    {
        $this->invoice = $invoice;

        $this->existingFilePath = $this->invoice->file_path;
        $this->uploadedFile = null;

        $this->name = $this->invoice->name;
        $this->reference = $this->invoice->reference;
        $this->type = $this->invoice->type;
        $this->category = $this->invoice->category;
        $this->issuer_name = $this->invoice->issuer_name;
        $this->issuer_website = $this->invoice->issuer_website;
        $this->amount = is_numeric($this->invoice->amount) ? (float) $this->invoice->amount : null;
        $this->currency = $this->invoice->currency ?? 'EUR';
        $this->paid_by = $this->invoice->paid_by;
        $this->associated_members = $this->invoice->associated_members;
        $this->issued_date = $this->invoice->issued_date;
        $this->payment_due_date = $this->invoice->payment_due_date;
        $this->payment_reminder = $this->invoice->payment_reminder;
        $this->payment_frequency = $this->invoice->payment_frequency;
        $this->engagement_id = $this->invoice->engagement_id;
        $this->engagement_name = $this->invoice->engagement_name;
        $this->payment_status = $this->invoice->payment_status;
        $this->payment_method = $this->invoice->payment_method;
        $this->priority = $this->invoice->priority;
        $this->notes = $this->invoice->notes;
        $this->tags = $this->invoice->tags;
        $this->is_archived = $this->invoice->is_archived;
        $this->is_favorite = $this->invoice->is_favorite;

        $this->updateAvailableCategories();

        return $invoice;
    }

    // Modifier la facture
    public function update()
    {
        if (empty($this->invoice)) {
            throw new \Exception('Impossible de mettre à jour une facture sans son ID');
        }

        $this->validate();

        try {
            DB::beginTransaction();

            $invoice = auth()->user()->invoices()->findOrFail($this->invoice);

            // Si un nouveau fichier est uploadé, supprimer l'ancien et stocker le nouveau
            if ($this->uploadedFile) {
                if ($invoice->file_path && Storage::disk('public')->exists($invoice->file_path)) {
                    Storage::disk('public')->delete($invoice->file_path);
                }

                $filePath = $this->uploadedFile->store('invoices', 'public');
                $fileSize = Storage::disk('public')->size($filePath);
            } // Si l'utilisateur a supprimé l'image, mais n'en a pas uploadé une nouvelle
            elseif ($this->existingFilePath === null && $invoice->file_path) {
                if (Storage::disk('public')->exists($invoice->file_path)) {
                    Storage::disk('public')->delete($invoice->file_path);
                }
                $filePath = null;
                $fileSize = 0;
            } // Si l'utilisateur n'a rien changé
            else {
                $filePath = $invoice->file_path;
                $fileSize = $invoice->file_size;
            }

            // Normaliser le montant avant stockage
            $amount = $this->normalizeAmount($this->amount);

            $invoice->update([
                /* Étape d'importation */
                'file_path' => $filePath,
                'file_size' => $fileSize,
                /* Étape 1 */
                'name' => $this->name,
                'reference' => $this->reference,
                'type' => $this->type,
                'category' => $this->category,
                'issuer_name' => $this->issuer_name,
                'issuer_website' => $this->issuer_website,
                /* Étape 2 */
                'amount' => $amount,
                'currency' => $this->currency,
                'paid_by' => $this->paid_by,
                'associated_members' => $this->associated_members ?? null,
                'amount_distribution' => $this->amount_distribution ?? null,
                /* Étape 3 */
                'issued_date' => $this->issued_date,
                'payment_due_date' => $this->payment_due_date,
                'payment_reminder' => $this->payment_reminder,
                'payment_frequency' => $this->payment_frequency,
                /* Étape 4 */
                'engagement_id' => $this->engagement_id,
                'engagement_name' => $this->engagement_name,
                /* Étape 5 */
                'payment_status' => $this->payment_status,
                'payment_method' => $this->payment_method,
                'priority' => $this->priority,
                /* Étape 6 */
                'notes' => $this->notes,
                'tags' => $this->tags ?? null,
                /* Archives */
                'is_archived' => $this->is_archived,
                /* Favoris */
                'is_favorite' => $this->is_favorite,
            ]);

            DB::commit();

            return $invoice;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage());

            return false;
        }
    }

    // Archiver la facture au lieu de la supprimer
    public function archive($invoiceId)
    {
        dd('Archiver du formulaire');
    }

    // Récupérer une facture archivée
    public function restore($invoiceId)
    {
        dd('Restaurer du formulaire');
    }

    // Supprimer définitivement la facture (à utiliser avec précaution)
    public function forceDelete($invoiceId)
    {
        dd('Supprimer du formulaire');
    }

    private function normalizeAmount($amount)
    {
        // Si le montant est vide ou null, retourner null
        if ($amount === null || $amount === '') {
            return null;
        }

        // Si le montant est déjà un nombre, le retourner directement
        if (is_numeric($amount)) {
            return (float) $amount;
        }

        // Convertir en chaîne si ce n'est pas déjà le cas
        $amount = (string) $amount;

        // Supprimer les espaces (que le mask ajoute comme séparateurs de milliers)
        $amount = str_replace(' ', '', $amount);

        // Convertir la virgule en point (format standard pour PHP)
        $amount = str_replace(',', '.', $amount);

        // Conversion en float et arrondi
        return round((float) $amount, 2);
    }

    public function getFileInfo()
    {
        if (! $this->uploadedFile) {
            return null;
        }

        $fileName = $this->uploadedFile->getClientOriginalName();
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $fileSize = round($this->uploadedFile->getSize() / 1024, 2); // Taille en KB
        $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png']);
        $isPdf = $fileExtension === 'pdf';
        $isDocx = $fileExtension === 'docx';

        return [
            'name' => $fileName,
            'extension' => $fileExtension,
            'size' => $fileSize,
            'sizeFormatted' => $this->formatFileSize($this->uploadedFile->getSize()),
            'isImage' => $isImage,
            'isPdf' => $isPdf,
            'isDocx' => $isDocx,
        ];
    }

    private function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 2).' '.$units[$pow];
    }
}
