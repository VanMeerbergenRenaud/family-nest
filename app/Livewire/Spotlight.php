<?php

namespace App\Livewire;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Component;

class Spotlight extends Component
{
    public Collection $results;

    public string $search = '';

    public bool $spotlightOpen = false;

    public function mount(): void
    {
        $this->results = collect();
    }

    public function updatedSearch(string $value): void
    {
        $this->results = collect();

        if (strlen($value) < 1 || $value === ' ') {
            return;
        }

        $currentUser = auth()->user();
        $family = $currentUser->family();
        $limitResults = 8;

        if ($family) {
            $userResults = User::search($value)->get();

            // Puis filtrer pour ne garder que ceux qui sont dans la même famille
            $userResults = $userResults->filter(function ($user) use ($family) {
                return $user->families()
                    ->where('family_id', $family->id)
                    ->exists();
            })->take($limitResults);

            // Search user invoices
            $invoiceResults = $currentUser->invoices()
                ->search($value)
                ->take($limitResults)
                ->get();

            // Concatenate the results into a single collection
            $this->results = $this->results
                ->concat($userResults)
                ->concat($invoiceResults);

            // Group results by model
            $this->results = $this->results->groupBy(function ($item) {
                return match (true) {
                    $item instanceof User => 'Utilisateurs',
                    $item instanceof Invoice => 'Factures',
                    default => 'Autres résultats',
                };
            });
        } else {
            // If user has no family -> search only invoices
            $invoiceResults = $currentUser->invoices()
                ->search($value)
                ->take($limitResults)
                ->get();

            $this->results = collect([
                'Factures' => $invoiceResults,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.spotlight');
    }
}
