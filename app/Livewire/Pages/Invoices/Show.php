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
use Carbon\Carbon;
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

    public bool $showDeleteFormModal = false;

    public function mount($id)
    {
        $this->invoice = auth()->user()->accessibleInvoices()
            ->with(['file', 'sharedUsers'])
            ->findOrFail($id);

        $this->initializeFileInfo();
        $this->prepareShares();
    }

    private function initializeFileInfo(): void
    {
        $fileInfo = $this->generateInvoiceFileUrl($this->invoice);

        $this->filePath = $fileInfo['url'];
        $this->fileExtension = $fileInfo['extension'];
        $this->fileName = $this->invoice->file->file_name ?? $this->invoice->name;
        $this->fileExists = $fileInfo['exists'];
    }

    private function prepareShares(): void
    {
        $this->form = new \stdClass;
        $this->form->amount = $this->invoice->amount;
        $this->form->currency = $this->invoice->currency;
        $this->form->family_id = $this->invoice->family_id;
        $this->form->user_shares = [];

        $this->loadSharesFromInvoice();
        $this->initializeShares();
    }

    private function loadSharesFromInvoice(): void
    {
        if ($this->invoice->sharedUsers->isEmpty()) {
            return;
        }

        foreach ($this->invoice->sharedUsers as $user) {
            $this->form->user_shares[] = [
                'id' => $user->id,
                'amount' => floatval($user->pivot->share_amount ?? 0),
                'percentage' => floatval($user->pivot->share_percentage ?? 0),
            ];
        }
    }

    public function formatDate($date): string
    {
        if (empty($date)) {
            return 'Non spécifiée';
        }

        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        return $date->format('d/m/Y');
    }

    #[On('invoice-favorite')]
    #[On('invoice-restore')]
    #[On('invoice-archived')]
    public function refreshShow(): void
    {
        $this->prepareShares();
    }

    public function render()
    {
        $findEnum = fn ($value, $enumClass) => $value instanceof $enumClass
            ? $value
            : $enumClass::tryFrom($value ?? '');

        $paymentStatusEnum = $findEnum($this->invoice->payment_status, PaymentStatusEnum::class);
        $paymentMethodEnum = $findEnum($this->invoice->payment_method, PaymentMethodEnum::class);
        $frequencyEnum = $findEnum($this->invoice->payment_frequency, PaymentFrequencyEnum::class);
        $priorityEnum = $findEnum($this->invoice->priority, PriorityEnum::class);

        $currencySymbol = $this->getCurrencySymbol();

        $shareSummary = $this->getShareDetailSummary($this->invoice->sharedUsers);
        $payer = $this->invoice->paidByUser;

        return view('livewire.pages.invoices.show', compact(
            'paymentStatusEnum',
            'paymentMethodEnum',
            'frequencyEnum',
            'priorityEnum',
            'currencySymbol',
            'shareSummary',
            'payer',
        ))->layout('layouts.app-sidebar');
    }
}
