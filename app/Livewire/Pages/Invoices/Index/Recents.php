<?php

namespace App\Livewire\Pages\Invoices\Index;

use App\Enums\CurrencyEnum;
use App\Models\Invoice;
use App\Traits\Invoice\ActionsTrait;
use App\Traits\Invoice\FileUrlTrait;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Recents extends Component
{
    use ActionsTrait, FileUrlTrait;

    public function getInvoiceCurrencySymbol(Invoice $invoice): string
    {
        $currency = $invoice->currency ?? 'EUR';

        try {
            return CurrencyEnum::from($currency)->symbol();
        } catch (\ValueError $e) {
            return '€';
        }
    }

    #[Computed]
    public function recentInvoices()
    {
        return auth()->user()->invoices()
            ->with(['file'])
            ->where('is_archived', false)
            ->orderByDesc('updated_at')
            ->take(8)
            ->get();
    }

    #[On('invoice-favorite')]
    #[On('invoice-archived')]
    #[On('invoice-deleted')]
    #[On('invoices-bulk-archived')]
    #[On('invoices-bulk-updated')]
    public function refreshRecentInvoices()
    {
        // Le Computed property se rafraîchira automatiquement
    }

    public function render()
    {
        return view('livewire.pages.invoices.index.recents', [
            'recentInvoices' => $this->recentInvoices,
        ]);
    }
}
