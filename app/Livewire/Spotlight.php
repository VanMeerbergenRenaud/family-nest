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
            // Logique existante pour les utilisateurs avec famille
            $userResults = User::search($value)->get();

            // Filter users by family
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
            // Cas où l'utilisateur n'a pas de famille
            $invoiceResults = $currentUser->invoices()
                ->search($value)
                ->take($limitResults)
                ->get();

            // Recherche l'utilisateur courant si son nom ou email correspond
            $userResults = collect();
            if (stripos($currentUser->name, $value) !== false || stripos($currentUser->email, $value) !== false) {
                $userResults = collect([$currentUser]);
            }

            // Combine les résultats
            $this->results = collect();

            if ($userResults->isNotEmpty()) {
                $this->results->put('Utilisateurs', $userResults);
            }

            if ($invoiceResults->isNotEmpty()) {
                $this->results->put('Factures', $invoiceResults);
            }
        }
    }

    public function render()
    {
        return view('livewire.spotlight');
    }
}
