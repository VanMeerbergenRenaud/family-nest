<?php

namespace App\Livewire\Pages\Invoices\Index;

use App\Enums\CurrencyEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PriorityEnum;
use App\Models\Invoice;
use App\Traits\Invoice\ActionsTrait;
use App\Traits\Invoice\FileUrlTrait;
use App\Traits\Invoice\ShareCalculationTrait;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Folders extends Component
{
    use ActionsTrait, FileUrlTrait, ShareCalculationTrait;

    public $showFolderModal = false;

    public $currentFolder = '';

    public $folderTitle = '';

    public $folderInvoices = [];

    // Méthode principale pour ouvrir un dossier
    public function openFolder(string $folder, string $title): void
    {
        $this->currentFolder = $folder;
        $this->folderTitle = $title;
        $this->folderInvoices = $this->getFilteredInvoices($folder);
        $this->showFolderModal = true;
    }

    // Fonction simplifiée pour récupérer les factures filtrées
    private function getFilteredInvoices(string $folder): Collection
    {
        $query = auth()->user()->invoices()
            ->with('file')
            ->where('is_archived', false);

        switch ($folder) {
            case 'favorites':
                $query->where('is_favorite', true);
                break;
            case 'paid':
                $query->where('payment_status', PaymentStatusEnum::Paid->value);
                break;
            case 'unpaid':
                $query->where('payment_status', PaymentStatusEnum::Unpaid->value);
                break;
            case 'late':
                $query->where('payment_status', PaymentStatusEnum::Late->value);
                break;
            case 'high_priority':
                $query->where('priority', PriorityEnum::High->value);
                break;
            case 'last_week':
                $query->where('issued_date', '>=', now()->subWeek());
                break;
            default:
                return collect();
        }

        return $query->orderBy('name', 'desc')->get();
    }

    #[Computed]
    public function activeInvoices()
    {
        return auth()->user()->invoices()
            ->with(['file', 'family', 'sharedUsers'])
            ->where('is_archived', false)
            ->get();
    }

    #[Computed]
    public function archivedInvoices()
    {
        return auth()->user()->invoices()
            ->where('is_archived', true)
            ->get();
    }

    #[Computed]
    public function folderStats(): array
    {
        $invoices = $this->activeInvoices;

        return [
            'favorites' => $this->getFolderStats($invoices->where('is_favorite', true)),
            'paid' => $this->getFolderStats($invoices->where('payment_status', PaymentStatusEnum::Paid->value)),
            'unpaid' => $this->getFolderStats($invoices->where('payment_status', PaymentStatusEnum::Unpaid->value)),
            'late' => $this->getFolderStats($invoices->where('payment_status', PaymentStatusEnum::Late->value)),
            'high_priority' => $this->getFolderStats($invoices->where('priority', PriorityEnum::High->value)),
            'last_week' => $this->getFolderStats($invoices->filter(function ($invoice) {
                return $invoice->issued_date >= now()->subWeek();
            })),
        ];
    }

    // Simplifie les deux méthodes en une seule
    private function getFolderStats(Collection $invoices): array
    {
        $currency = $invoices->isEmpty() ? 'EUR' : $this->getMostCommonCurrency($invoices);

        return [
            'count' => $invoices->count(),
            'amount' => $invoices->sum('amount'),
            'currency' => $currency,
        ];
    }

    private function getMostCommonCurrency(Collection $invoices): string
    {
        $currencies = $invoices->pluck('currency')
            ->filter()
            ->countBy();

        return $currencies->sortDesc()->keys()->first() ?? 'EUR';
    }

    public function formatAmount(float $amount, string $currency = 'EUR'): string
    {
        try {
            return CurrencyEnum::from($currency)->format($amount);
        } catch (\ValueError $e) {
            return number_format($amount, 2, ',', ' ').' €';
        }
    }

    public function getInvoiceCurrencySymbol(Invoice $invoice): string
    {
        $currency = $invoice->currency ?? 'EUR';

        try {
            return CurrencyEnum::from($currency)->symbol();
        } catch (\ValueError $e) {
            return '€';
        }
    }

    #[On('invoice-favorite')]
    #[On('invoice-archived')]
    #[On('invoice-deleted')]
    #[On('invoices-bulk-archived')]
    #[On('invoices-bulk-updated')]
    public function refreshFolder(): void
    {
        if ($this->showFolderModal && $this->currentFolder) {
            $this->folderInvoices = $this->getFilteredInvoices($this->currentFolder);
        }
    }

    public function render()
    {
        return view('livewire.pages.invoices.index.folders', [
            'folderStats' => $this->folderStats,
            'archivedInvoices' => $this->archivedInvoices,
        ]);
    }
}
