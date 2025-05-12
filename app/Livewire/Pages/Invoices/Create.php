<?php

namespace App\Livewire\Pages\Invoices;

use App\Enums\PaymentFrequencyEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PriorityEnum;
use App\Enums\TypeEnum;
use App\Livewire\Forms\InvoiceForm;
use App\Services\FileStorageService;
use App\Services\TextractService;
use App\Traits\Invoice\ComponentTrait;
use App\Traits\Invoice\ShareCalculationTrait;
use App\Traits\Invoice\TagManagement;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Masmerise\Toaster\Toaster;

#[Title('Ajouter la facture')]
class Create extends Component
{
    use ComponentTrait;
    use ShareCalculationTrait;
    use TagManagement;
    use WithFileUploads;

    public InvoiceForm $form;

    public $family_members = [];

    // OCR properties
    public bool $isOcrProcessing = false;

    public $ocrData = null;

    public bool $showOcrButton = false;

    public function mount()
    {
        $this->loadFamilyMembers();
        $this->form->paid_by_user_id = auth()->id();
        $this->form->user_shares = [];
        $this->initializeShares();
        $this->initializeTagManagement();
    }

    public function hydrate()
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
        $this->validateShares();

        $invoice = $this->form->store($fileStorageService);

        if ($invoice) {
            Toaster::success('Facture créée avec succès !');
            $this->redirectRoute('dashboard', $invoice);
        } else {
            Toaster::error('Une erreur s\'est produite lors de la création de la facture.');
        }
    }

    public function processOcr(TextractService $textractService): void
    {
        if (! $this->form->uploadedFile) {
            Toaster::error('Veuillez d\'abord télécharger un fichier.');

            return;
        }

        $this->isOcrProcessing = true;

        try {
            // Notifier le front-end que le traitement commence
            $this->dispatch('ocr-processing-started');

            // Stockage temporaire du fichier pour l'OCR
            $path = $this->form->uploadedFile->storeAs('temp', $this->form->uploadedFile->getClientOriginalName(), 'local');
            $fullPath = storage_path('app/'.$path);

            // Analyse OCR
            $result = $textractService->analyzeInvoice($fullPath);

            // Nettoyage du fichier temporaire
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            if ($result['success']) {
                $this->ocrData = $result['data'];
                $this->applyOcrDataToForm();
                Toaster::success('Analyse OCR terminée avec succès.');
            } else {
                Toaster::error('Échec de l\'analyse OCR::Vérifiez le fichier ou le format.');
                Log::error('Échec de l\'analyse OCR: '.$result['message']);
            }
        } catch (\Exception $e) {
            Toaster::error('Une erreur est survenue::Vérifiez le fichier ou le format du document.');
            Log::error('Exception lors du traitement OCR: '.$e->getMessage());
        } finally {
            $this->isOcrProcessing = false;
            // Notifier le front-end que le traitement est terminé
            $this->dispatch('ocr-processing-completed');
        }
    }

    protected function applyOcrDataToForm(): void
    {
        if (! $this->ocrData) {
            return;
        }

        // Appliquer les données OCR aux champs du formulaire
        if (! empty($this->ocrData['name'])) {
            $this->form->name = $this->ocrData['name'];
        }

        if (! empty($this->ocrData['reference'])) {
            $this->form->reference = $this->ocrData['reference'];
        }

        if (! empty($this->ocrData['issuer_name'])) {
            $this->form->issuer_name = $this->ocrData['issuer_name'];
        }

        if (! empty($this->ocrData['issuer_website'])) {
            $this->form->issuer_website = $this->ocrData['issuer_website'];
        }

        if (! empty($this->ocrData['amount'])) {
            $this->form->amount = $this->form->normalizeAmount($this->ocrData['amount']);
            $this->calculateRemainingShares();
        }

        if (! empty($this->ocrData['issued_date'])) {
            $this->form->issued_date = $this->ocrData['issued_date'];
        }

        if (! empty($this->ocrData['payment_due_date'])) {
            $this->form->payment_due_date = $this->ocrData['payment_due_date'];
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
