<?php

namespace App\Livewire\Pages\Invoices;

use App\Models\Invoice;
use Livewire\Component;

class Show extends Component
{
    public Invoice $invoice;

    public string $file_path;

    public function mount($id)
    {
        $this->invoice = auth()->user()->invoices()->findOrFail($id);

        $this->file_path = $this->invoice->file->file_path;

        if ($this->invoice->is_archived) {
            $this->redirectRoute('invoices.archived');
        }
    }

    public function render()
    {
        return view('livewire.pages.invoices.show')
            ->layout('layouts.app');
    }
}
