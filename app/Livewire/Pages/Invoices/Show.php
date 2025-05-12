<?php

namespace App\Livewire\Pages\Invoices;

use App\Traits\Invoice\ActionsTrait;
use App\Traits\Invoice\ComponentTrait;
use App\Traits\Invoice\FileUrlTrait;
use App\Traits\Invoice\ShareCalculationTrait;
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

    public bool $showInvoicePreviewModal = false;

    public bool $showDeleteFormModal = false;

    public $family_members = [];

    public function mount($id)
    {
        $this->invoice = auth()->user()->accessibleInvoices()
            ->with(['file', 'sharedUsers'])
            ->findOrFail($id);

        // Initialiser les informations du fichier
        $fileInfo = $this->generateInvoiceFileUrl($this->invoice);

        $this->filePath = $fileInfo['url'];
        $this->fileExtension = $fileInfo['extension'];
        $this->fileName = $this->invoice->file->file_name ?? $this->invoice->name;
        $this->fileExists = $fileInfo['exists'];

        $this->loadFamilyMembers();
        $this->prepareSharesData();

        $this->prepareShares();
        $this->initializeShares();
    }

    #[On('invoice-favorite')]
    #[On('invoice-restore')]
    #[On('invoice-archived')]
    #[On('invoice-deleted')]
    #[On('invoices-bulk-archived')]
    #[On('invoices-bulk-updated')]
    public function refreshShow(): void {}

    private function prepareSharesData(): void
    {
        $this->form = (object) [
            'amount' => $this->invoice->amount ?? 0,
            'currency' => $this->invoice->currency ?? 'EUR',
            'family_id' => $this->invoice->family_id,
            'user_shares' => [],
        ];
    }

    private function prepareShares(): void
    {
        if (! $this->invoice->sharedUsers->isEmpty()) {
            foreach ($this->invoice->sharedUsers as $user) {
                $this->form->user_shares[] = [
                    'id' => $user->id,
                    'amount' => floatval($user->pivot->share_amount ?? 0),
                    'percentage' => floatval($user->pivot->share_percentage ?? 0),
                ];
            }
        }
    }

    public function render()
    {
        return view('livewire.pages.invoices.show')
            ->layout('layouts.app-sidebar');
    }
}
