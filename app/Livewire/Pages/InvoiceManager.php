<?php

namespace App\Livewire\Pages;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class InvoiceManager extends Component
{
    use WithFileUploads;

    #[Validate]
    public $name;

    public $file_path;

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

    public $invoices;

    public $invoiceId;

    public bool $showEditFormModal = false;

    public bool $showDeleteFormModal = false;

    public $fileUrl;

    public bool $showFileModal = false;

    /* Notifications */
    public bool $addedWithSuccess = false;

    public bool $editWithSuccess = false;

    public bool $deleteWithSuccess = false;

    protected $rules = [
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
        'status' => 'required|in:unpaid,paid,late,partially_paid',
        'payment_method' => 'nullable|in:cash,card,mastercard',
        'priority' => 'required|in:high,medium,low',
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
    ];

    public function mount()
    {
        $this->amount = '0.00';
        $this->loadInvoices();
    }

    public function loadInvoices()
    {
        $this->invoices = auth()->user()->invoices()->get();
    }

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

    // Show the invoice in a modal
    public function showFile($id)
    {
        $invoice = auth()->user()->invoices()->findOrFail($id);
        $this->fileUrl = $invoice->file_path;
        $this->showFileModal = true;
    }

    // Download the invoice into the user's computer
    public function downloadInvoice($id)
    {
        $invoice = auth()->user()->invoices()->findOrFail($id);

        dd($invoice->file_path);
    }

    /* ----------- CRUD ----------- */

    public function create(): RedirectResponse
    {
        return Redirect::route('invoices.create');
    }

    public function showEditForm($id)
    {
        $invoice = auth()->user()->invoices()->findOrFail($id);

        $this->invoiceId = $invoice->id;

        $this->file_path = $invoice->file_path;
        $this->name = $invoice->name;
        $this->issuer = $invoice->issuer;
        $this->type = $invoice->type;
        $this->category = $invoice->category;
        $this->website = $invoice->website;
        $this->amount = $invoice->amount;
        $this->is_variable = $invoice->is_variable;
        $this->is_family_related = $invoice->is_family_related;
        $this->issued_date = $invoice->issued_date->format('Y-m-d');
        $this->payment_reminder = $invoice->payment_reminder;
        $this->payment_frequency = $invoice->payment_frequency;
        $this->status = $invoice->status;
        $this->payment_method = $invoice->payment_method;
        $this->priority = $invoice->priority;
        $this->notes = $invoice->notes;
        $this->tags = $invoice->tags;

        $this->showEditFormModal = true;
    }

    public function updateInvoice()
    {
        $this->validate();

        if (! $this->invoiceId) {
            return;
        }

        try {
            DB::beginTransaction();

            auth()->user()->invoices()
                ->where('id', $this->invoiceId)
                ->update([
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

            $this->editWithSuccess = true;
            $this->reset('showEditFormModal');

            sleep(1);

            $this->loadInvoices();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage());
        }
    }

    public function showDeleteForm($id)
    {
        $invoice = auth()->user()->invoices()->findOrFail($id);
        $this->invoiceId = $invoice->id;
        $this->showDeleteFormModal = true;
    }

    public function deleteInvoice()
    {
        $invoice = auth()->user()->invoices()->findOrFail($this->invoiceId);

        try {
            DB::beginTransaction();

            $invoice->delete();

            DB::commit();

            $this->deleteWithSuccess = true;
            $this->showDeleteFormModal = false;

            sleep(1);

            $this->loadInvoices();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pages.invoice-manager')
            ->layout('layouts.app-sidebar');
    }
}
