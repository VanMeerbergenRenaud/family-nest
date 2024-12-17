<?php

namespace App\Livewire\Pages;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateInvoice extends Component
{
    use WithFileUploads;

    #[Validate]
    public $name;

    public $issuer;

    public $type;

    public $category;

    public $website;

    public $amount;

    public $is_variable = false;

    public $is_family_related = false;

    public $issued_date;

    public $payment_reminder;

    public $payment_frequency;

    public $status = 'unpaid';

    public $payment_method;

    public $priority = 'medium';

    public $notes;

    public $tags = [];

    public $tagInput = '';

    public $uploadedFile;

    protected $rules = [
        'uploadedFile' => 'required|file|mimes:pdf,docx,jpeg,png,jpg|max:10240', // 10MB max
        'name' => 'required|string|max:255',
        'issuer' => 'required|string|max:255',
        'type' => 'required|string|max:255',
        'category' => 'nullable|string|max:255',
        'website' => 'nullable|url|max:255',
        'amount' => 'required|numeric|min:0',
        'is_variable' => 'boolean',
        'is_family_related' => 'boolean',
        'issued_date' => 'required|date',
        'payment_reminder' => 'nullable|string|max:255',
        'payment_frequency' => 'nullable|string|max:255',
        'status' => 'nullable|in:unpaid,paid,late,partially_paid',
        'payment_method' => 'nullable|in:cash,card,mastercard',
        'priority' => 'nullable|in:high,medium,low',
        'notes' => 'nullable|string',
        'tags' => 'nullable|array',
        'tagInput' => 'nullable|string',
    ];

    protected $messages = [
        'name.required' => 'Le nom de la facture est obligatoire.',
        'issuer.required' => 'Le nom du fournisseur est obligatoire.',
        'type.required' => 'Le type de facture est obligatoire.',
        'amount.required' => 'Le montant est obligatoire.',
        'amount.numeric' => 'Le montant doit être un nombre.',
        'amount.min' => 'Le montant doit être supérieur ou égal à zéro.',
        'issued_date.required' => "La date d'émission est obligatoire.",
        'issued_date.date' => "La date d'émission doit être une date valide.",
        'status.required' => 'Le statut de la facture est obligatoire.',
        'status.in' => 'Le statut de la facture doit être parmi : non-payée, payée, en retard, ou partiellement payée.',
        'priority.required' => 'La priorité est obligatoire.',
        'priority.in' => 'La priorité doit être parmi : haute, moyenne, basse.',
        'website.url' => "L'URL du site web du fournisseur n'est pas valide.",
        'uploadedFile.required' => 'Veuillez sélectionner un fichier.',
        'uploadedFile.file' => 'Le fichier doit être un fichier valide.',
        'uploadedFile.mimes' => 'Le fichier doit être au format PDF, Word, JPEG, PNG ou JPG.',
        'uploadedFile.max' => 'Le fichier ne doit pas dépasser 10 Mo.',
    ];

    public function addTag()
    {
        if (! empty($this->tagInput)) {
            $this->tags[] = $this->tagInput;
            $this->tagInput = '';
        }
    }

    public function removeTag($index)
    {
        unset($this->tags[$index]);
        $this->tags = array_values($this->tags); // Réindexer le tableau
    }

    public function createInvoice()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            auth()->user()->invoices()->create([
                'user_id' => auth()->user()->id,
                'file_path' => $this->uploadedFile->store('invoices', 'public'),
                'name' => $this->name,
                'issuer' => $this->issuer,
                'type' => $this->type,
                'category' => $this->category,
                'website' => $this->website,
                'amount' => floatval(str_replace(' ', '', $this->amount)), // Convertit en float pour la BDD
                'is_variable' => $this->is_variable,
                'is_family_related' => $this->is_family_related,
                'issued_date' => $this->issued_date,
                'payment_reminder' => $this->payment_reminder,
                'payment_frequency' => $this->payment_frequency,
                'status' => $this->status,
                'payment_method' => $this->payment_method,
                'priority' => $this->priority,
                'notes' => $this->notes,
                'tags' => $this->tags,
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
        return view('livewire.pages.create-invoice');
    }
}
