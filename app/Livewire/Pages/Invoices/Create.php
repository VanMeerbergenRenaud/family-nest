<?php

namespace App\Livewire\Pages\Invoices;

use App\Enums\PaymentFrequencyEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PriorityEnum;
use App\Enums\TypeEnum;
use App\Livewire\Forms\InvoiceForm;
use App\Services\FileStorageService;
use App\Traits\Invoice\ComponentTrait;
use App\Traits\Invoice\OcrProcessingTrait;
use App\Traits\Invoice\ShareCalculationTrait;
use App\Traits\Invoice\TagManagement;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Masmerise\Toaster\Toaster;

#[Title('Ajouter la facture')]
class Create extends Component
{
    use ComponentTrait;
    use OcrProcessingTrait;
    use ShareCalculationTrait;
    use TagManagement;
    use WithFileUploads;

    public InvoiceForm $form;

    public $family_members = [];

    public bool $enableSharing = false;

    public function mount()
    {
        $this->loadFamilyMembers();
        $this->form->paid_by_user_id = auth()->id();
        $this->form->user_shares = [];
        $this->initializeTagManagement();
        $this->calculateRemainingShares();
    }

    public function hydrate(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function updatedFormUploadedFile(): void
    {
        $this->showOcrButton = true;
    }

    public function createInvoice(FileStorageService $fileStorageService): void
    {
        $invoice = $this->form->save($fileStorageService, $this->enableSharing);

        if ($invoice) {
            Toaster::success('Facture créée avec succès !');
            $this->redirectRoute('dashboard', $invoice);
        } else {
            Toaster::error('Une erreur s\'est produite lors de la création de la facture.');
        }
    }

    public function render()
    {
        return view('livewire.pages.invoices.create', [
            'invoiceTypes' => TypeEnum::getTypesOptionsWithEmojis(),
            'paymentStatuses' => PaymentStatusEnum::getStatusOptionsWithEmojis(),
            'paymentMethods' => PaymentMethodEnum::getMethodOptionsWithEmojis(),
            'paymentFrequencies' => PaymentFrequencyEnum::getFrequencyOptionsWithEmojis(),
            'priorities' => PriorityEnum::getPriorityOptionsWithEmojis(),
        ])->layout('layouts.app');
    }
}
