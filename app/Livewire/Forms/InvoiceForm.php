<?php

namespace App\Livewire\Forms;

use App\Enums\InvoiceTypeEnum;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Form;

class InvoiceForm extends Form
{
    public $invoice_id; // ID pour suivre la facture si on veut la modifier

    public $existingFilePath = null;

    public $name;

    public $type;

    public $category;

    public $issuer_name;

    public $issuer_website;

    public $amount;

    public $currency = 'EUR';

    public $paid_by;

    public $associated_members;

    public $issued_date;

    public $payment_due_date;

    public $payment_reminder;

    public $payment_frequency;

    public $payment_status = 'unpaid';

    public $payment_method = 'card';

    public $priority = 'none';

    public $notes;

    public $tags = [];

    public $tagInput = '';

    public $uploadedFile;

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
            'type' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'issuer_name' => 'nullable|string|max:255',
            'issuer_website' => 'nullable|url|max:255',
            // Étape 2
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|size:3', // 3 pour le code ISO
            'paid_by' => 'nullable|string|max:255',
            'associated_members' => 'nullable|string',
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
            'notes' => 'nullable|string',
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
            'uploadedFile.mimes' => 'Le fichier doit être au format PDF, Word, JPEG, PNG ou JPG.',
            'uploadedFile.max' => 'Le fichier ne doit pas dépasser 10 Mo.',
            'uploadedFile.not_in' => 'Les fichiers CSV ne sont pas acceptés. Veuillez utiliser un format comme PDF, Word, JPEG, PNG ou JPG.',
            'name.required' => 'Le nom de la facture est obligatoire.',
            'issuer_website.url' => "L'URL du site web du fournisseur n'est pas valide.",
            'amount.required' => 'Le montant est obligatoire.',
            'amount.numeric' => 'Le montant doit être un nombre.',
            'amount.min' => 'Le montant doit être supérieur ou égal à zéro.',
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

            // Store the file and get its path
            $filePath = $this->uploadedFile->store('invoices', 'public');

            // Get the file size
            $fileSize = Storage::disk('public')->size($filePath);

            // Normaliser le montant avant stockage
            $amount = $this->normalizeAmount($this->amount);

            $invoice = auth()->user()->invoices()->create([
                'user_id' => auth()->user()->id,
                /* Étape d'importation */
                'file_path' => $filePath,
                'file_size' => $fileSize, // Store the file size
                /* Étape 1 */
                'name' => $this->name,
                'type' => $this->type,
                'category' => $this->category,
                'issuer_name' => $this->issuer_name,
                'issuer_website' => $this->issuer_website,
                /* Étape 2 */
                'amount' => $amount,
                'currency' => $this->currency,
                'paid_by' => $this->paid_by,
                'associated_members' => $this->associated_members,
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
                'tags' => $this->tags,
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

    // Récupérer la facture
    public function setInvoice($invoiceId)
    {
        $invoice = auth()->user()->invoices()->findOrFail($invoiceId);

        $this->invoice_id = $invoice->id;

        $this->existingFilePath = $invoice->file_path;
        $this->uploadedFile = null;

        $this->name = $invoice->name;
        $this->type = $invoice->type;
        $this->category = $invoice->category;
        $this->issuer_name = $invoice->issuer_name;
        $this->issuer_website = $invoice->issuer_website;
        $this->amount = is_numeric($invoice->amount)
            ? (float) $invoice->amount
            : null;
        $this->currency = $invoice->currency ?? 'EUR';
        $this->paid_by = $invoice->paid_by;
        $this->associated_members = $invoice->associated_members;
        $this->issued_date = $invoice->issued_date;
        $this->payment_due_date = $invoice->payment_due_date;
        $this->payment_reminder = $invoice->payment_reminder;
        $this->payment_frequency = $invoice->payment_frequency;
        $this->engagement_id = $invoice->engagement_id;
        $this->engagement_name = $invoice->engagement_name;
        $this->payment_status = $invoice->payment_status;
        $this->payment_method = $invoice->payment_method;
        $this->priority = $invoice->priority;
        $this->notes = $invoice->notes;
        $this->tags = $invoice->tags;
        $this->is_archived = $invoice->is_archived;
        $this->is_favorite = $invoice->is_favorite;

        $this->updateAvailableCategories();

        return $invoice;
    }

    // Modifier la facture
    public function update()
    {
        if (empty($this->invoice_id)) {
            throw new \Exception('Impossible de mettre à jour une facture sans son ID');
        }

        $this->validate();

        try {
            DB::beginTransaction();

            $invoice = auth()->user()->invoices()->findOrFail($this->invoice_id);

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
            }// Si l'utilisateur n'a rien changé
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
                'type' => $this->type,
                'category' => $this->category,
                'issuer_name' => $this->issuer_name,
                'issuer_website' => $this->issuer_website,
                /* Étape 2 */
                'amount' => $amount,
                'currency' => $this->currency,
                'paid_by' => $this->paid_by,
                'associated_members' => $this->associated_members,
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
                'tags' => $this->tags,
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

    // Supprimer la facture
    public function delete($invoiceId = null)
    {
        $id = $invoiceId ?? $this->invoice_id;

        if (empty($id)) {
            throw new \Exception('Impossible de supprimer une facture sans ID');
        }

        try {
            DB::beginTransaction();

            $invoice = auth()->user()->invoices()->findOrFail($id);

            // Supprimer le fichier associé s'il existe
            if ($invoice->file_path && Storage::disk('public')->exists($invoice->file_path)) {
                Storage::disk('public')->delete($invoice->file_path);
            }

            // Supprimer la facture
            $result = $invoice->delete();

            DB::commit();

            // Réinitialiser le formulaire après suppression
            if ($id == $this->invoice_id) {
                $this->reset();
            }

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur lors de la suppression de la facture: '.$e->getMessage());

            return false;
        }
    }

    public function getFormattedReminderAttribute()
    {
        if (! $this->payment_reminder) {
            return 'Non spécifié';
        }

        if (str_contains($this->payment_reminder, '_days')) {
            $days = str_replace('_days', '', $this->payment_reminder);

            return $days.' jours avant échéance';
        }

        // Si c'est une date valide
        try {
            return Carbon::parse($this->payment_reminder)->format('d/m/Y');
        } catch (\Exception $e) {
            return $this->payment_reminder;
        }
    }

    private function normalizeAmount($amount)
    {
        // Si le montant est vide ou null, retourner 0 ou null selon votre préférence
        if ($amount === null || $amount === '') {
            return null; // ou return 0; si vous préférez
        }

        // Si le montant est déjà un nombre, le retourner directement
        if (is_numeric($amount)) {
            return (float) $amount;
        }

        // Convertir en chaîne si ce n'est pas déjà le cas
        $amount = (string) $amount;

        // Supprimer les espaces
        $amount = str_replace(' ', '', $amount);

        // Remplacer la virgule française par un point pour la conversion en float
        $amount = str_replace(',', '.', $amount);

        // Gérer le cas où il y aurait plusieurs points
        $parts = explode('.', $amount);
        if (count($parts) > 2) {
            // Garder le premier comme partie entière et le reste comme partie décimale
            $integerPart = $parts[0];
            $decimalPart = implode('', array_slice($parts, 1));
            $amount = $integerPart.'.'.$decimalPart;
        }

        // S'assurer que la valeur est un nombre valide
        $result = (float) $amount;

        // Vérifier si la conversion a produit un nombre valide
        if (is_nan($result) || ! is_finite($result)) {
            return null; // ou une valeur par défaut
        }

        // Convertir en float et arrondir à 2 décimales
        return round($result, 2);
    }
}
