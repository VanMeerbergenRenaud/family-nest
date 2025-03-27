<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Livewire\Component;

class Breadcrumb extends Component
{
    public Collection $segments;

    protected array $routes = [
        'dashboard' => 'Tableau de bord',
        'invoices.index' => 'Factures',
        'invoices.create' => 'Créer une facture',
        'invoices.edit' => 'Modifier la facture',
        'invoices.show' => 'Détails de la facture',
        'invoices.archived' => 'Factures archivées',
        'themes' => 'Thèmes',
        'calendar' => 'Calendrier',
        'goals' => 'Objectifs',
        'family' => 'Famille',
        'settings.index' => 'Paramètres',
        'settings.profile' => 'Profil',
        'settings.storage' => 'Stockage',
        'settings.notifications' => 'Notifications',
        'settings.billing' => 'Plan de paiement',
        'settings.appearance' => 'Apparence',
        'settings.danger' => 'Zone de danger',
        'help-center' => 'Centre d\'aide',
    ];

    // Prefixes that should show parent route
    protected array $groupPrefixes = ['invoices.', 'settings.'];

    protected string $homeIcon = '<svg class="text-gray-600 hover:text-indigo-600" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.66667 13.1667H12.3333M8.18141 1.30333L2.52949 5.69927C2.15168 5.99312 1.96278 6.14005 1.82669 6.32405C1.70614 6.48704 1.61633 6.67065 1.56169 6.86588C1.5 7.08627 1.5 7.32558 1.5 7.80421V13.8333C1.5 14.7667 1.5 15.2335 1.68166 15.59C1.84144 15.9036 2.09641 16.1585 2.41002 16.3183C2.76654 16.5 3.23325 16.5 4.16667 16.5H13.8333C14.7668 16.5 15.2335 16.5 15.59 16.3183C15.9036 16.1585 16.1586 15.9036 16.3183 15.59C16.5 15.2335 16.5 14.7667 16.5 13.8333V7.80421C16.5 7.32558 16.5 7.08627 16.4383 6.86588C16.3837 6.67065 16.2939 6.48704 16.1733 6.32405C16.0372 6.14005 15.8483 5.99312 15.4705 5.69927L9.81859 1.30333C9.52582 1.07562 9.37943 0.961766 9.21779 0.918001C9.07516 0.879384 8.92484 0.879384 8.78221 0.918001C8.62057 0.961766 8.47418 1.07562 8.18141 1.30333Z" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    public function mount(): void
    {
        $this->segments = collect();
        $this->buildBreadcrumb();
    }

    protected function buildBreadcrumb(): void
    {
        $currentRoute = Route::currentRouteName();
        if (! $currentRoute) {
            return;
        }

        // Dashboard case
        if ($currentRoute === 'dashboard') {
            $this->segments->push([
                'name' => 'dashboard',
                'label' => '',
                'url' => '#',
                'icon' => '<span class="inline-flex items-center gap-2.5">'.$this->homeIcon.'<span class="font-medium">Tableau de bord</span></span>',
                'current' => true,
            ]);

            return;
        }

        // Add home link for all pages (except for the dashboard)
        $this->segments->push([
            'name' => 'dashboard',
            'label' => '',
            'url' => route('dashboard'),
            'icon' => $this->homeIcon,
            'current' => false,
        ]);

        // Add prefix for grouped routes
        $this->addPrefixSegment($currentRoute);

        // Add last segment => current page
        $this->segments->push([
            'name' => $currentRoute,
            'label' => $this->getRouteLabel($currentRoute),
            'url' => '#',
            'icon' => null,
            'current' => true,
        ]);
    }

    // For grouped routes
    protected function addPrefixSegment(string $currentRoute): void
    {
        // Checks if the current route belongs to a defined group (prefix)
        $prefix = collect($this->groupPrefixes)
            ->first(fn ($prefix) => Str::startsWith($currentRoute, $prefix));

        if (! $prefix) {
            return;
        }

        $indexRoute = $prefix.'index'; // Add the prefix

        // Skip if the current route is already the index
        if ($currentRoute === $indexRoute) {
            return;
        }

        $this->segments->push([
            'name' => $indexRoute,
            'label' => $this->getRouteLabel($indexRoute),
            'url' => route($indexRoute),
            'icon' => null,
            'current' => false,
        ]);
    }

    protected function getRouteLabel(string $routeName): string
    {
        // If a name exists in the list, it uses that name directly; otherwise, it automatically creates a new one
        return $this->routes[$routeName]
            ?? Str::title(str_replace(['.', '-', '_'], ' ', $routeName));
    }

    public function render()
    {
        return view('livewire.breadcrumb');
    }
}
