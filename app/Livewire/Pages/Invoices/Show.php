<?php

namespace App\Livewire\Pages\Invoices;

use App\Enums\PaymentFrequencyEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PriorityEnum;
use App\Models\Invoice;
use App\Traits\InvoiceFileUrlTrait;
use App\Traits\InvoiceShareCalculationTrait;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Détails de facture')]
class Show extends Component
{
    use InvoiceFileUrlTrait;
    use InvoiceShareCalculationTrait;

    public Invoice $invoice;

    public $filePath = null;

    public $fileExtension = null;

    public $fileName = null;

    public $fileExists = false;

    public $family_members = [];

    public $form;

    public function mount($id)
    {
        $this->invoice = auth()->user()->invoices()
            ->with(['file', 'sharedUsers'])
            ->findOrFail($id);

        // Préparation des membres de la famille d'abord
        $this->prepareFamilyMembers();
        $this->prepareFormData();

        // Génération des informations du fichier
        $fileInfo = $this->generateInvoiceFileUrl($this->invoice);
        $this->filePath = $fileInfo['url'];
        $this->fileExtension = $fileInfo['extension'];
        $this->fileExists = $fileInfo['exists'];
        $this->fileName = $this->invoice->file->file_name ?? null;
    }

    private function prepareFamilyMembers(): void
    {
        $family = auth()->user()->family();

        // Si l'utilisateur a une famille
        if ($family) {
            // Récupérer les membres de la famille, incluant l'utilisateur authentifié
            $this->family_members = $family->users()->get();
        } else {
            // Si pas de famille, n'inclure que l'utilisateur authentifié
            $this->family_members = collect([auth()->user()]);
        }

        // S'assurer que l'utilisateur authentifié est dans la liste
        if (! $this->family_members->contains('id', auth()->id())) {
            $this->family_members->prepend(auth()->user());
        }
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

        if (! $this->form->paid_by_user_id) {
            $this->form->paid_by_user_id = auth()->id();
        }

        if ($this->invoice->sharedUsers->isEmpty()) {
            if ($this->invoice->amount > 0) {
                $payerId = $this->form->paid_by_user_id;
                $this->form->user_shares[] = [
                    'id' => $payerId,
                    'amount' => $this->invoice->amount,
                    'percentage' => 100,
                ];
            }
        } else {
            foreach ($this->invoice->sharedUsers as $user) {
                $this->form->user_shares[] = [
                    'id' => $user->id,
                    'amount' => $user->pivot->share_amount ?? 0,
                    'percentage' => $user->pivot->share_percentage ?? 0,
                ];
            }
        }

        $this->initializeUserShares();
        $this->calculateRemainingShares();
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
