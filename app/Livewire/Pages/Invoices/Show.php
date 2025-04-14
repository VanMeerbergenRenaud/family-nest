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

    // Objet nécessaire pour utiliser le trait InvoiceShareCalculationTrait
    public $form;

    public function mount($id)
    {
        $this->invoice = auth()->user()->invoices()
            ->with(['sharedUsers', 'file'])
            ->findOrFail($id);

        $this->prepareFamilyMembers();
        $this->prepareFormData();

        $fileInfo = $this->generateInvoiceFileUrl($this->invoice);
        $this->filePath = $fileInfo['url'];
        $this->fileExtension = $fileInfo['extension'];
        $this->fileExists = $fileInfo['exists'];
        $this->fileName = $this->invoice->file->file_name ?? null;
    }

    /**
     * Récupère les membres de la famille
     */
    private function prepareFamilyMembers(): void
    {
        $family = auth()->user()->family();
        $this->family_members = collect();

        if ($family) {
            $this->family_members = $family->users()
                ->where('users.id', '!=', auth()->id())
                ->get();
        }

        $this->family_members->prepend(auth()->user());
    }

    public function getUserName()
    {
        // Récupérer le nom de l'utilisateur qui a payé la facture
        return $this->invoice->paidByUser;
    }

    /**
     * Prépare les données de formulaire pour utiliser le trait InvoiceShareCalculationTrait
     */
    private function prepareFormData(): void
    {
        $this->form = (object) [
            'amount' => $this->invoice->amount,
            'currency' => $this->invoice->currency,
            'paid_by_user_id' => $this->invoice->paid_by_user_id,
            'user_shares' => [],
        ];

        // S'assurer que l'ID du payeur est bien défini
        if (! $this->form->paid_by_user_id && $this->invoice->paid_by_user_id) {
            $this->form->paid_by_user_id = $this->invoice->paid_by_user_id;
        }

        foreach ($this->invoice->sharedUsers as $user) {
            $this->form->user_shares[] = [
                'id' => $user->id,
                'amount' => $user->pivot->share_amount ?? 0,
                'percentage' => $user->pivot->share_percentage ?? 0,
            ];
        }

        // Calculer les parts restantes
        $this->calculateRemainingShares();
    }

    public function render()
    {
        return view('livewire.pages.invoices.show', [
            'paymentStatusOptions' => PaymentStatusEnum::getStatusOptionsWithEmojis(),
            'paymentMethodOptions' => PaymentMethodEnum::getMethodOptionsWithEmojis(),
            'paymentFrequencyOptions' => PaymentFrequencyEnum::getFrequencyOptionsWithEmojis(),
            'priorityOptions' => PriorityEnum::getPriorityOptionsWithEmojis(),
            'shareSummary' => $this->getShareDetailSummary($this->family_members),
        ])->layout('layouts.app-sidebar');
    }
}
