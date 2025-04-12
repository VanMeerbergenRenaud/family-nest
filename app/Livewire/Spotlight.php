<?php

namespace App\Livewire;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\Attributes\Reactive;

class Spotlight extends Component
{
    public Collection $results;
    public string $search = '';
    public bool $spotlightOpen = false;

    // Navigation keyboard
    public int $selectedIndex = -1;
    public array $navigableItems = [];

    public function mount(): void
    {
        $this->results = collect();
        $this->resetNavigation();
    }

    public function updatedSearch(string $value): void
    {
        $this->results = collect();
        $this->resetNavigation();

        if (strlen($value) < 1 || $value === ' ') {
            return;
        }

        $this->fetchResults($value);
        $this->buildNavigableItems();
    }

    protected function fetchResults(string $value): void
    {
        $currentUser = auth()->user();
        $family = $currentUser->family();
        $limitResults = 8;

        if ($family) {
            $this->fetchFamilyResults($value, $currentUser, $family, $limitResults);
        } else {
            $this->fetchIndividualResults($value, $currentUser, $limitResults);
        }
    }

    protected function fetchFamilyResults(string $value, $currentUser, $family, $limitResults): void
    {
        // Search users
        $userResults = User::search($value)
            ->get()
            ->filter(fn ($user) => $user->families()->where('family_id', $family->id)->exists())
            ->take($limitResults);

        // Search invoices
        $invoiceResults = $currentUser->invoices()
            ->search($value)
            ->take($limitResults)
            ->get();

        // Group results by model type
        $this->results = collect()
            ->concat($userResults)
            ->concat($invoiceResults)
            ->groupBy(function ($item) {
                return match (true) {
                    $item instanceof User => 'Utilisateurs',
                    $item instanceof Invoice => 'Factures',
                    default => 'Autres résultats',
                };
            });
    }

    protected function fetchIndividualResults(string $value, $currentUser, $limitResults): void
    {
        // Search invoices
        $invoiceResults = $currentUser->invoices()
            ->search($value)
            ->take($limitResults)
            ->get();

        // Check if current user matches search
        $userResults = collect();
        if (stripos($currentUser->name, $value) !== false || stripos($currentUser->email, $value) !== false) {
            $userResults = collect([$currentUser]);
        }

        // Add results to collection if not empty
        $this->results = collect();

        if ($userResults->isNotEmpty()) {
            $this->results->put('Utilisateurs', $userResults);
        }

        if ($invoiceResults->isNotEmpty()) {
            $this->results->put('Factures', $invoiceResults);
        }
    }

    protected function resetNavigation(): void
    {
        $this->selectedIndex = -1;
        $this->navigableItems = [];
    }

    protected function buildNavigableItems(): void
    {
        $this->navigableItems = [];

        // Add results to navigable items
        foreach ($this->results as $section => $items) {
            foreach ($items as $item) {
                $this->navigableItems[] = [
                    'id' => $item->id,
                    'type' => $item instanceof User ? 'user' : 'invoice',
                    'route' => $this->getRouteForItem($item),
                ];
            }
        }

        // Add suggestions if no results
        if ($this->results->isEmpty() && !empty($this->search)) {
            $this->addSuggestions();
        }
    }

    protected function getRouteForItem($item): string
    {
        return $item instanceof User
            ? route('settings.profile')
            : route('invoices.show', $item);
    }

    protected function addSuggestions(): void
    {
        $this->navigableItems[] = [
            'id' => 'create-invoice',
            'type' => 'suggestion',
            'route' => route('invoices.create'),
        ];

        $this->navigableItems[] = [
            'id' => 'archived-invoices',
            'type' => 'suggestion',
            'route' => route('invoices.archived'),
        ];
    }

    public function navigateUp(): void
    {
        if (empty($this->navigableItems)) {
            return;
        }

        if ($this->selectedIndex <= 0) {
            $this->selectedIndex = count($this->navigableItems) - 1;
        } else {
            $this->selectedIndex--;
        }
    }

    public function navigateDown(): void
    {
        if (empty($this->navigableItems)) {
            return;
        }

        if ($this->selectedIndex >= count($this->navigableItems) - 1) {
            $this->selectedIndex = 0;
        } else {
            $this->selectedIndex++;
        }
    }

    public function selectCurrent(): void
    {
        if (!$this->hasValidSelection()) {
            return;
        }

        $this->redirect($this->navigableItems[$this->selectedIndex]['route']);
    }

    protected function hasValidSelection(): bool
    {
        return $this->selectedIndex >= 0 &&
            $this->selectedIndex < count($this->navigableItems);
    }

    public function setSelectedItem(string $itemId, string $itemType): void
    {
        foreach ($this->navigableItems as $index => $item) {
            if ($item['id'] == $itemId && $item['type'] == $itemType) {
                $this->selectedIndex = $index;
                break;
            }
        }
    }

    public function isItemSelected(string $itemId, string $itemType): bool
    {
        if (!$this->hasValidSelection()) {
            return false;
        }

        $selectedItem = $this->navigableItems[$this->selectedIndex];
        return $selectedItem['id'] == $itemId && $selectedItem['type'] == $itemType;
    }

    public function render()
    {
        return view('livewire.spotlight');
    }
}
