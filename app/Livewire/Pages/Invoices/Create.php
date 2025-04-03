<?php

namespace App\Livewire\Pages\Invoices;

use App\Enums\InvoiceTypeEnum;
use App\Enums\PaymentFrequencyEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PriorityEnum;
use App\Livewire\Forms\InvoiceForm;
use App\Services\FileStorageService;
use App\Traits\InvoiceShareCalculationTrait;
use App\Traits\InvoiceTagManagement;
use Livewire\Component;
use Livewire\WithFileUploads;
use Masmerise\Toaster\Toaster;

class Create extends Component
{
    use InvoiceShareCalculationTrait, InvoiceTagManagement, WithFileUploads;

    public InvoiceForm $form;

    public $family_members = [];

    public function mount()
    {
        $this->prepareFamilyMembers();

        $this->form->paid_by_user_id = auth()->id();

        $this->form->user_shares = [];

        $this->initializeTagManagement();
        $this->calculateRemainingShares();
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

    public function updatedFormType(): void
    {
        $this->form->updateAvailableCategories();
        $this->form->category = null;
    }

    public function updatedFormAmount(): void
    {
        $this->calculateRemainingShares();
    }

    public function updatedShareMode(): void
    {
        $this->calculateRemainingShares();
    }

    public function removeUploadedFile(): void
    {
        $this->form->removeFile();
        $this->form->resetErrorBag('uploadedFile');
    }

    public function createInvoice(FileStorageService $fileStorageService): void
    {
        $invoice = $this->form->store($fileStorageService);

        if ($invoice) {
            Toaster::success('Facture créée avec succès !');
            $this->redirectRoute('invoices.index', $invoice);
        } else {
            Toaster::error('Une erreur s\'est produite lors de la création de la facture.');
        }
    }

    public function render()
    {
        $this->calculateRemainingShares();

        return view('livewire.pages.invoices.create', [
            'invoiceTypes' => InvoiceTypeEnum::getTypesOptionsWithEmojis(),
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
