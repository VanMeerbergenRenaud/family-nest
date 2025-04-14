<?php

namespace App\Livewire\Pages\Invoices;

use App\Enums\PaymentFrequencyEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PriorityEnum;
use App\Enums\TypeEnum;
use App\Livewire\Forms\InvoiceForm;
use App\Models\Invoice;
use App\Services\FileStorageService;
use App\Traits\InvoiceFileUrlTrait;
use App\Traits\InvoiceShareCalculationTrait;
use App\Traits\InvoiceTagManagement;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Masmerise\Toaster\Toaster;

#[Title('Modifier la facture')]
class Edit extends Component
{
    use InvoiceFileUrlTrait;
    use InvoiceShareCalculationTrait;
    use InvoiceTagManagement;
    use WithFileUploads;

    public InvoiceForm $form;

    public Invoice $invoice;

    public $isEditMode = true;

    public $family_members = [];

    public $filePath = null;

    public $fileExtension = null;

    public $fileName = null;

    public $fileExists = false;

    public function mount($id)
    {
        $this->invoice = auth()->user()->invoices()
            ->with(['file', 'sharedUsers'])
            ->findOrFail($id);

        $this->form->setFromInvoice($this->invoice);

        $fileInfo = $this->generateInvoiceFileUrl($this->invoice);

        $this->filePath = $fileInfo['url'];
        $this->fileExtension = $fileInfo['extension'];
        $this->fileExists = $fileInfo['exists'];
        $this->fileName = $this->invoice->file->file_name ?? null;

        $this->prepareFamilyMembers();

        $this->initializeTagManagement();
        $this->calculateRemainingShares();
    }

    private function prepareFamilyMembers(): void
    {
        $family = auth()->user()->family();
        $this->family_members = collect();

        if ($family) {
            $this->family_members = $family->users()
                ->get();
        }

        $this->family_members->prepend(auth()->user());
    }

    public function updatedFormType(): void
    {
        $this->form->updateAvailableCategories();
        $this->form->category = null;
    }

    public function removeUploadedFile(): void
    {
        $this->form->removeFile();
        $this->form->resetErrorBag('uploadedFile');
    }

    public function updatedFormAmount(): void
    {
        $this->calculateRemainingShares();
    }

    public function updatedShareMode(): void
    {
        $this->calculateRemainingShares();
    }

    public function updateInvoice(FileStorageService $fileStorageService): void
    {
        $invoice = $this->form->update($fileStorageService);

        if ($invoice) {
            Toaster::success('Facture mise à jour avec succès !');
            $this->redirectRoute('invoices.index', $invoice);
        } else {
            Toaster::error('Une erreur s\'est produite lors de la mise à jour de la facture.');
        }
    }

    public function render()
    {
        return view('livewire.pages.invoices.edit', [
            'invoiceTypes' => TypeEnum::getTypesOptionsWithEmojis(),
            'paymentStatuses' => PaymentStatusEnum::getStatusOptionsWithEmojis(),
            'paymentMethods' => PaymentMethodEnum::getMethodOptionsWithEmojis(),
            'paymentFrequencies' => PaymentFrequencyEnum::getFrequencyOptionsWithEmojis(),
            'priorities' => PriorityEnum::getPriorityOptionsWithEmojis(),
            'remainingAmount' => $this->remainingAmount,
            'remainingPercentage' => $this->remainingPercentage,
            'shareMode' => $this->shareMode,
        ])->layout('layouts.app');
    }
}
