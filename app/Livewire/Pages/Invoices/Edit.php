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
use App\Traits\Invoice\ComponentTrait;
use App\Traits\Invoice\FileUrlTrait;
use App\Traits\Invoice\ShareCalculationTrait;
use App\Traits\Invoice\TagManagement;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Masmerise\Toaster\Toaster;

#[Title('Modifier la facture')]
class Edit extends Component
{
    use ComponentTrait;
    use FileUrlTrait;
    use ShareCalculationTrait;
    use TagManagement;
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
        // Récupérer la facture avec ses relations
        $this->invoice = auth()->user()->accessibleInvoices()
            ->with(['file', 'sharedUsers'])
            ->findOrFail($id);

        // Vérifier les droits de modification
        if (! auth()->user()->can('update', $this->invoice)) {
            Toaster::warning('Vous n\'avez pas les droits pour modifier cette facture.');

            return redirect()->route('invoices.show', $this->invoice);
        }

        // Reste du code inchangé...
        $this->form->setFromInvoice($this->invoice);

        // Récupérer les informations du fichier
        $fileInfo = $this->generateInvoiceFileUrl($this->invoice);
        $this->filePath = $fileInfo['url'];
        $this->fileExtension = $fileInfo['extension'];
        $this->fileExists = $fileInfo['exists'];
        $this->fileName = $this->invoice->file->file_name ?? null;

        // Préparer les données de partage de la facture
        $this->loadFamilyMembers();
        $this->initializeShares();

        // Initialiser les tags de la facture
        $this->initializeTagManagement();
    }

    public function updateInvoice(FileStorageService $fileStorageService): void
    {
        // Valider les parts avant la sauvegarde
        $this->validateShares();

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
        ])->layout('layouts.app');
    }
}
