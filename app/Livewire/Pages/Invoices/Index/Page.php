<?php

namespace App\Livewire\Pages\Invoices\Index;

use App\Livewire\Pages\Dashboard\Filters;
use App\Traits\Invoice\ActionsTrait;
use App\Traits\Invoice\BulkActionsTrait;
use App\Traits\Invoice\StateCheckTrait;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Factures')]
class Page extends Component
{
    use ActionsTrait, BulkActionsTrait, StateCheckTrait;

    public Filters $filters;

    public function mount()
    {
        if (! isset($this->filters)) {
            $this->filters->init(auth()->user());
        }
    }

    #[On('invoice-favorite')]
    #[On('invoice-archived')]
    #[On('invoice-deleted')]
    #[On('invoices-bulk-archived')]
    #[On('invoices-bulk-updated')]
    public function refreshPage(): void
    {
    }

    public function render()
    {
        return view('livewire.pages.invoices.index.page')
            ->layout('layouts.app-sidebar');
    }
}
