<?php

namespace App\Livewire;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Component;

class Spotlight extends Component
{
    public bool $spotlightOpen = false;

    public string $search = '';

    public Collection $results;

    public function mount(): void
    {
        $this->results = collect();
    }

    public function updatedSearch(string $value): void
    {
        if (strlen($value) < 2) {
            $this->results = collect();

            return;
        }

        // Reset results each time the search query is updated
        $this->results = collect();

        // Search for users and invoices
        $userResults = User::search($value)->take(3)->get();
        $invoiceResults = Invoice::search($value)->take(3)->get();

        // Concat results into a single collection
        $this->results = $this->results->concat($userResults)->concat($invoiceResults);

        // Group results by model type
        $this->results = $this->results->groupBy(function ($item) {
            return match (true) {
                $item instanceof User => 'Utilisateurs',
                $item instanceof Invoice => 'Factures',
                default => 'Autres résultats',
            };
        });
    }

    public function render()
    {
        return view('livewire.spotlight', [
            'results' => $this->results,
        ]);
    }
}
