<?php

namespace App\Livewire\Pages\Invoices;

use App\Enums\PaymentFrequencyEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PriorityEnum;
use App\Traits\Invoice\ActionsTrait;
use App\Traits\Invoice\ComponentTrait;
use App\Traits\Invoice\FileUrlTrait;
use App\Traits\Invoice\ShareCalculationTrait;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Détails de facture')]
class Show extends Component
{
    use ActionsTrait;
    use ComponentTrait;
    use FileUrlTrait;
    use ShareCalculationTrait;

    public $family_members = [];

    public $form;

    // Ajout des propriétés nécessaires pour les modales
    public bool $showInvoicePreviewModal = false;

    public bool $showDeleteFormModal = false;

    public function mount($id)
    {
        // Récupérer la facture avec ses relations
        $this->invoice = auth()->user()->accessibleInvoices()
            ->with(['file', 'sharedUsers'])
            ->findOrFail($id);

        // Reste du code inchangé...
        $this->loadFamilyMembers();
        $this->prepareFormData();

        // Récupérer les informations du fichier
        $fileInfo = $this->generateInvoiceFileUrl($this->invoice);
        $this->filePath = $fileInfo['url'];
        $this->fileExtension = $fileInfo['extension'];
        $this->fileExists = $fileInfo['exists'];
        $this->fileName = $this->invoice->file->file_name ?? null;
    }

    private function prepareFormData(): void
    {
        $this->form = (object) [
            // Informations générales
            'name' => $this->invoice->name,
            'reference' => $this->invoice->reference,
            'type' => $this->invoice->type,
            'category' => $this->invoice->category,
            'issuer_name' => $this->invoice->issuer_name,
            'issuer_website' => $this->invoice->issuer_website,

            // Détails financiers
            'amount' => $this->invoice->amount ?? 0,
            'currency' => $this->invoice->currency ?? 'EUR',
            'paid_by_user_id' => $this->invoice->paid_by_user_id,
            'family_id' => $this->invoice->family_id,

            // Dates
            'issued_date' => $this->invoice->issued_date,
            'payment_due_date' => $this->invoice->payment_due_date,
            'payment_reminder' => $this->invoice->payment_reminder,
            'payment_frequency' => $this->invoice->payment_frequency,

            // Statut de paiement
            'payment_status' => $this->invoice->payment_status,
            'payment_method' => $this->invoice->payment_method,
            'priority' => $this->invoice->priority,

            // Notes et tags
            'notes' => $this->invoice->notes,
            'tags' => $this->invoice->tags,

            // États
            'is_favorite' => $this->invoice->is_favorite,
            'is_archived' => $this->invoice->is_archived,

            // Parts utilisateur
            'user_shares' => [],
        ];

        // Initialiser les parts utilisateur
        $this->prepareShares();
        $this->initializeShares();
    }

    #[On('invoice-favorite')]
    #[On('invoice-restore')]
    #[On('invoice-archived')]
    #[On('invoice-deleted')]
    #[On('invoices-bulk-archived')]
    #[On('invoices-bulk-updated')]
    public function refreshShow(): void {}

    private function prepareShares(): void
    {
        // Si aucune part n'est définie, laisser le tableau vide
        // La méthode initializeShares() se chargera de créer une part pour le payeur si nécessaire
        if (! $this->invoice->sharedUsers->isEmpty()) {
            foreach ($this->invoice->sharedUsers as $user) {
                $this->form->user_shares[] = [
                    'id' => $user->id,
                    'amount' => floatval($user->pivot->share_amount ?? 0),
                    'percentage' => floatval($user->pivot->share_percentage ?? 0),
                ];
            }
        }
    }

    public function render()
    {
        return view('livewire.pages.invoices.show', [
            'paymentStatusOptions' => PaymentStatusEnum::getStatusOptionsWithEmojis(),
            'paymentMethodOptions' => PaymentMethodEnum::getMethodOptionsWithEmojis(),
            'paymentFrequencyOptions' => PaymentFrequencyEnum::getFrequencyOptionsWithEmojis(),
            'priorityOptions' => PriorityEnum::getPriorityOptionsWithEmojis(),
        ])->layout('layouts.app-sidebar');
    }
}
