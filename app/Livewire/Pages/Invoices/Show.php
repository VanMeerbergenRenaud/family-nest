<?php

namespace App\Livewire\Pages\Invoices;

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

    public function mount($id)
    {
        $this->invoice = auth()->user()->invoices()->findOrFail($id);

        if ($this->invoice->is_archived) {
            $this->redirectRoute('invoices.archived');
        }

        // Générer l'URL du fichier
        $fileInfo = $this->generateInvoiceFileUrl($this->invoice);

        $this->filePath = $fileInfo['url'];
        $this->fileExtension = $fileInfo['extension'];
        $this->fileExists = $fileInfo['exists'];
        $this->fileName = $this->invoice->file->file_name ?? null;
    }

    public function render()
    {
        return view('livewire.pages.invoices.show')
            ->layout('layouts.app-sidebar');
    }
}
