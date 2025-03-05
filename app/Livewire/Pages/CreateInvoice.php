<?php

namespace App\Livewire\Pages;

use App\Enums\InvoiceTypeEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateInvoice extends Component
{
    use WithFileUploads;

    #[Validate]
    public $name;

    public $type;

    public $category;

    public $issuer_name;

    public $issuer_website;

    public $amount;

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

    public $family_members = [];

    public $engagements = [];

    public $availableCategories = [];

    public $is_archived = false;

    public $is_favorite = false;

    protected $rules = [
        // Étape d'importation
        'uploadedFile' => 'required|file|mimes:pdf,docx,jpeg,png,jpg|max:10240',
        // Étape 1
        'name' => 'required|string|max:255',
        'type' => 'nullable|string|max:255',
        'category' => 'nullable|string|max:255',
        'issuer_name' => 'nullable|string|max:255',
        'issuer_website' => 'nullable|url|max:255',
        // Étape 2
        'amount' => 'required|numeric|min:0',
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

    protected $messages = [
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

    public function mount()
    {
        // $this->engagements = Engagement::where('user_id', auth()->id())->get();
        $this->engagements = [
            ['id' => 'abc123', 'name' => 'Abonnement Internet Orange'],
        ];

        // Si un type est déjà sélectionné, mettre à jour les catégories disponibles
        if ($this->type) {
            $this->updateAvailableCategories();
        }
    }

    /**
     * Met à jour les catégories disponibles en fonction du type de facture sélectionné
     */
    public function updatedType()
    {
        $this->updateAvailableCategories();
        $this->category = null; // Réinitialiser la catégorie lorsque le type change
    }

    /**
     * Met à jour la liste des catégories disponibles en trouvant l'énumération correspondant à la valeur du type sélectionné
     */
    private function updateAvailableCategories()
    {
        foreach (InvoiceTypeEnum::cases() as $case) {
            if ($case->value === $this->type) {
                $this->availableCategories = $case->categories();

                return;
            }
        }

        $this->availableCategories = [];
    }

    /**
     * Supprime le fichier uploadé
     */
    public function removeUploadedFile()
    {
        $this->uploadedFile = null;
        $this->resetValidation('uploadedFile');
    }

    /**
     * Permet d'ajouter un tag à la liste des tags
     */
    public function addTag()
    {
        if (! empty($this->tagInput)) {
            $this->tags[] = $this->tagInput;
            $this->tagInput = '';
        }
    }

    /**
     * Permet de supprimer un tag de la liste des tags
     */
    public function removeTag($index)
    {
        unset($this->tags[$index]);
        $this->tags = array_values($this->tags); // Réindexer le tableau
    }

    /**
     * Crée une nouvelle facture
     */
    public function createInvoice()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Store the file and get its path
            $filePath = $this->uploadedFile->store('invoices', 'public');

            // Get the file size
            $fileSize = Storage::disk('public')->size($filePath);

            auth()->user()->invoices()->create([
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
                'amount' => floatval(str_replace(' ', '', $this->amount)),
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
            Redirect::route('invoices');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pages.create-invoice', [
            'invoiceTypes' => InvoiceTypeEnum::getTypesOptions(),
        ])->layout('layouts.app');
    }
}
