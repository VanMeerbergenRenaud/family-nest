<?php

namespace App\Livewire\Pages\Invoices;

use App\Enums\PaymentFrequencyEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PriorityEnum;
use App\Models\Invoice;
use App\Traits\InvoiceFileUrlTrait;
use Livewire\Component;

class Show extends Component
{
    use InvoiceFileUrlTrait;

    public Invoice $invoice;

    public $filePath = null;

    public $fileExtension = null;

    public $fileName = null;

    public $fileExists = false;

    public $family_members = [];

    public function mount($id)
    {
        $this->invoice = auth()->user()->invoices()->findOrFail($id);
        $this->prepareFamilyMembers();

        if ($this->invoice->is_archived) {
            $this->redirectRoute('invoices.archived');
        }

        $fileInfo = $this->generateInvoiceFileUrl($this->invoice);

        $this->filePath = $fileInfo['url'];
        $this->fileExtension = $fileInfo['extension'];
        $this->fileExists = $fileInfo['exists'];
        $this->fileName = $this->invoice->file->file_name ?? null;
    }

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

    public function render()
    {
        return view('livewire.pages.invoices.show', [
            'paymentStatusOptions' => PaymentStatusEnum::getStatusOptions(),
            'paymentMethodOptions' => PaymentMethodEnum::getMethodOptions(),
            'paymentFrequencyOptions' => PaymentFrequencyEnum::getFrequencyOptions(),
            'priorityOptions' => PriorityEnum::getPriorityOptions(),
        ])->layout('layouts.app-sidebar');
    }
}
