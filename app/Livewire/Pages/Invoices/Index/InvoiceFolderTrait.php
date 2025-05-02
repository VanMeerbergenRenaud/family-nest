<?php

namespace App\Livewire\Pages\Invoices\Index;

use App\Models\Invoice;
use App\Services\InvoiceFolderService;

trait InvoiceFolderTrait
{
    public bool $showFolderModal = false;

    public string $currentFolder = '';

    public string $folderTitle = '';

    public $folderInvoices = [];

    public function openFolder(string $folder, string $title): void
    {
        $this->currentFolder = $folder;
        $this->folderTitle = $title;

        $folderService = app(InvoiceFolderService::class);
        $this->folderInvoices = $folderService->getFolderInvoices(
            $folder,
            auth()->id(),
            $this->sortField ?? $this->sortCol ?? 'name',
            $this->sortDirection ?? ($this->sortAsc ? 'asc' : 'desc')
        );

        $this->showFolderModal = true;
    }

    public function getFolderStats(): array
    {
        $invoices = auth()->user()->invoices;
        $folderService = app(InvoiceFolderService::class);

        return $folderService->getFolderStats($invoices);
    }

    public function getInvoiceCurrencySymbol(Invoice $invoice): string
    {
        $this->form = (object) [
            'currency' => $invoice->currency ?? 'EUR',
        ];

        return $this->getCurrencySymbol();
    }

    public function formatAmount(float $amount, string $currency = 'EUR'): string
    {
        $this->form = (object) [
            'currency' => $currency,
        ];

        $symbol = $this->getCurrencySymbol();

        return number_format($amount, 2, ',', ' ').' '.$symbol;
    }
}
