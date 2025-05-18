<?php

namespace App\Livewire\Pages\Invoices;

use App\Enums\CurrencyEnum;
use App\Enums\PaymentFrequencyEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PriorityEnum;
use App\Traits\Invoice\ActionsTrait;
use App\Traits\Invoice\ComponentTrait;
use App\Traits\Invoice\FileUrlTrait;
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

    public bool $showDeleteFormModal = false;

    public function mount($id)
    {
        $this->invoice = auth()->user()->accessibleInvoices()
            ->with(['file', 'family', 'sharings'])
            ->findOrFail($id);

        $this->initializeFileInfo();
    }

    private function initializeFileInfo(): void
    {
        $fileInfo = $this->generateInvoiceFileUrl($this->invoice);

        $this->filePath = $fileInfo['url'];
        $this->fileExtension = $fileInfo['extension'];
        $this->fileName = $this->invoice->file->file_name ?? $this->invoice->name;
        $this->fileExists = $fileInfo['exists'];
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

    /**
     * Obtient le symbole de la devise pour l'affichage
     */
    public function getCurrencySymbol(): string
    {
        if (! isset($this->invoice->currency)) {
            return '€';
        }

        try {
            return CurrencyEnum::tryFrom($this->invoice->currency)?->symbol() ?? '€';
        } catch (\Exception) {
            return '€';
        }
    }

    #[On('invoice-favorite')]
    #[On('invoice-restore')]
    #[On('invoice-archived')]
    public function refreshShow(): void
    {
        $this->invoice->refresh();
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

        $totalPercentage = $this->invoice->total_percentage;
        $totalSharedAmount = $this->invoice->total_shared_amount;
        $hasShares = $this->invoice->has_shares;
        $isFullyShared = $this->invoice->is_fully_shared;

        $payer = $this->invoice->paidByUser;

        return view('livewire.pages.invoices.show', compact(
            'paymentStatusEnum',
            'paymentMethodEnum',
            'frequencyEnum',
            'priorityEnum',
            'currencySymbol',
            'totalPercentage',
            'totalSharedAmount',
            'hasShares',
            'isFullyShared',
            'payer',
        ))->layout('layouts.app-sidebar');
    }
}
