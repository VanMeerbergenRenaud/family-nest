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

    public int $selectedIndex = -1;

    public array $navigableItems = [];

    public bool $showAdvancedSearch = false;

    public int $totalResultsCount = 0;

    // Pagination pour la recherche avancée
    public int $limit = 8;

    public bool $hasMoreResults = false;

    protected const STANDARD_LIMIT = 8;

    protected const ADVANCED_LIMIT = 50;

    protected const TYPES = [
        User::class => 'Utilisateurs',
        Invoice::class => 'Factures',
    ];

    public function mount(): void
    {
        $this->resetResults();
    }

    public function updatedSearch(string $value): void
    {
        $this->resetResults();
        $this->showAdvancedSearch = false;

        if (strlen(trim($value)) < 1) {
            return;
        }

        $this->fetchResults($value);
        $this->buildNavigableItems();
    }

    protected function fetchResults(string $value): void
    {
        $currentUser = auth()->user();
        $family = $currentUser->family();

        $limit = $this->showAdvancedSearch ? self::ADVANCED_LIMIT : self::STANDARD_LIMIT;

        $this->results = $family
            ? $this->getFamilyResults($value, $family, $limit)
            : $this->getIndividualResults($value, $currentUser, $limit);

        // Compter le nombre total de résultats pour savoir s'il y a plus que la limite
        $this->calculateTotalResults($value, $currentUser, $family);
    }

    protected function calculateTotalResults(string $value, $currentUser, $family): void
    {
        $familyMemberIds = $family->users()->pluck('users.id')->toArray();

        // Utiliser une seule requête pour compter
        $userCount = User::whereIn('id', $familyMemberIds)
            ->search($value)
            ->count();

        // Compter les factures
        $invoiceCount = Invoice::whereIn('user_id', $familyMemberIds)
            ->search($value)
            ->count();

        $count = $userCount + $invoiceCount;

        $this->totalResultsCount = $count;
        $this->hasMoreResults = $count > self::STANDARD_LIMIT && ! $this->showAdvancedSearch;
    }

    protected function getFamilyResults(string $value, $family, int $limit): Collection
    {
        // Get family member IDs
        $familyMemberIds = $family->users()->pluck('users.id')->toArray();

        // Search users in family with eager loading
        $userResults = User::search($value)
            ->get()
            ->filter(fn ($user) => $user->families()->where('family_id', $family->id)->exists())
            ->take($limit);

        // Search invoices for all family members with eager loading
        $invoiceResults = Invoice::whereIn('user_id', $familyMemberIds)
            ->search($value)
            ->with(['file', 'family'])
            ->take($limit)
            ->get();

        return $this->groupResultsByType($userResults, $invoiceResults);
    }

    protected function getIndividualResults(string $value, $currentUser, int $limit): Collection
    {
        // Search user's invoices
        $invoiceResults = $currentUser->invoices()
            ->search($value)
            ->take($limit)
            ->get();

        // Check if current user matches search
        $userResults = collect();
        if (str_contains(strtolower($currentUser->name), strtolower($value)) ||
            str_contains(strtolower($currentUser->email), strtolower($value))) {
            $userResults = collect([$currentUser]);
        }

        return $this->groupResultsByType($userResults, $invoiceResults);
    }

    protected function groupResultsByType(Collection $userResults, Collection $invoiceResults): Collection
    {
        $results = collect();

        if ($userResults->isNotEmpty()) {
            $results->put(self::TYPES[User::class], $userResults);
        }

        if ($invoiceResults->isNotEmpty()) {
            $results->put(self::TYPES[Invoice::class], $invoiceResults);
        }

        return $results;
    }

    protected function resetResults(): void
    {
        $this->results = collect();
        $this->selectedIndex = -1;
        $this->navigableItems = [];
        $this->totalResultsCount = 0;
        $this->hasMoreResults = false;
    }

    protected function buildNavigableItems(): void
    {
        $this->navigableItems = [];

        // Add results to navigable items
        foreach ($this->results as $items) {
            foreach ($items as $item) {
                $type = $item instanceof User ? 'user' : 'invoice';
                $this->navigableItems[] = [
                    'id' => $item->id,
                    'type' => $type,
                    'route' => $item instanceof User ? route('settings.profile') : route('invoices.show', $item),
                ];
            }
        }

        // Ajout du bouton "Voir plus" si nécessaire
        if ($this->hasMoreResults) {
            $this->navigableItems[] = [
                'id' => 'show-more',
                'type' => 'action',
                'route' => '#', // Action JavaScript, pas de route
            ];
        }

        // Add suggestions if no results with search
        if ($this->results->isEmpty() && ! empty($this->search)) {
            $this->addSuggestions();
        }
    }

    protected function addSuggestions(): void
    {
        $this->navigableItems = [
            [
                'id' => 'create-invoice',
                'type' => 'suggestion',
                'route' => route('invoices.create'),
            ],
            [
                'id' => 'archived-invoices',
                'type' => 'suggestion',
                'route' => route('invoices.archived'),
            ],
        ];
    }

    public function navigateUp(): void
    {
        if (empty($this->navigableItems)) {
            return;
        }

        $this->selectedIndex = $this->selectedIndex <= 0
            ? count($this->navigableItems) - 1
            : $this->selectedIndex - 1;
    }

    public function navigateDown(): void
    {
        if (empty($this->navigableItems)) {
            return;
        }

        $this->selectedIndex = $this->selectedIndex >= count($this->navigableItems) - 1
            ? 0
            : $this->selectedIndex + 1;
    }

    public function selectCurrent(): void
    {
        if (! $this->hasValidSelection()) {
            return;
        }

        $selectedItem = $this->navigableItems[$this->selectedIndex];

        // Cas spécial pour le bouton "Voir plus"
        if ($selectedItem['id'] === 'show-more' && $selectedItem['type'] === 'action') {
            $this->toggleAdvancedSearch();

            return;
        }

        $this->redirect($selectedItem['route']);
    }

    public function toggleAdvancedSearch(): void
    {
        $this->showAdvancedSearch = ! $this->showAdvancedSearch;
        $this->fetchResults($this->search);
        $this->buildNavigableItems();
    }

    public function setSelectedItem(string $itemId, string $itemType): void
    {
        $index = collect($this->navigableItems)->search(
            fn ($item) => $item['id'] == $itemId && $item['type'] == $itemType
        );

        if ($index !== false) {
            $this->selectedIndex = $index;
        }
    }

    public function isItemSelected(string $itemId, string $itemType): bool
    {
        if (! $this->hasValidSelection()) {
            return false;
        }

        $selectedItem = $this->navigableItems[$this->selectedIndex];

        return $selectedItem['id'] == $itemId && $selectedItem['type'] == $itemType;
    }

    protected function hasValidSelection(): bool
    {
        return $this->selectedIndex >= 0 && $this->selectedIndex < count($this->navigableItems);
    }

    public function render()
    {
        return view('livewire.spotlight');
    }
}
