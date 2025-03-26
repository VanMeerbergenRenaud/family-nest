<?php

namespace App\Livewire;

use App\Models\Invoice;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class Breadcrumb extends Component
{
    // Les segments de l'URL actuelle
    public $segments = [];

    // ID de la ressource actuelle (le cas échéant)
    public $resourceId = null;

    // La configuration des routes et de leurs libellés
    protected $routeLabels = [
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

    /**
     * Configuration des groupes de routes et leurs routes parentes
     */
    protected $routeGroups = [
        'invoices.' => ['dashboard'],
        'settings.' => ['dashboard'],
    ];

    /**
     * Icône de la maison pour le fil d'Ariane
     */
    protected $homeIcon = '<svg class="text-gray-600 hover:text-indigo-600" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5.66667 13.1667H12.3333M8.18141 1.30333L2.52949 5.69927C2.15168 5.99312 1.96278 6.14005 1.82669 6.32405C1.70614 6.48704 1.61633 6.67065 1.56169 6.86588C1.5 7.08627 1.5 7.32558 1.5 7.80421V13.8333C1.5 14.7667 1.5 15.2335 1.68166 15.59C1.84144 15.9036 2.09641 16.1585 2.41002 16.3183C2.76654 16.5 3.23325 16.5 4.16667 16.5H13.8333C14.7668 16.5 15.2335 16.5 15.59 16.3183C15.9036 16.1585 16.1586 15.9036 16.3183 15.59C16.5 15.2335 16.5 14.7667 16.5 13.8333V7.80421C16.5 7.32558 16.5 7.08627 16.4383 6.86588C16.3837 6.67065 16.2939 6.48704 16.1733 6.32405C16.0372 6.14005 15.8483 5.99312 15.4705 5.69927L9.81859 1.30333C9.52582 1.07562 9.37943 0.961766 9.21779 0.918001C9.07516 0.879384 8.92484 0.879384 8.78221 0.918001C8.62057 0.961766 8.47418 1.07562 8.18141 1.30333Z" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>';

    /**
     * Monter le composant et initialiser les segments
     */
    public function mount($resourceId = null)
    {
        $this->resourceId = $resourceId;
        $this->buildBreadcrumbSegments();
    }

    /**
     * Construire les segments du fil d'Ariane à partir de la route actuelle
     */
    protected function buildBreadcrumbSegments(): void
    {
        $currentRoute = Route::currentRouteName();
        $routeParameters = Route::current()->parameters();

        // Si la route n'est pas trouvée, ne rien faire
        if (! $currentRoute) {
            return;
        }

        $this->segments = [];

        // Pour la page d'accueil (dashboard), afficher uniquement l'icône
        if ($currentRoute === 'dashboard') {
            $this->segments[] = [
                'name' => 'dashboard',
                'label' => '', // Si vide => seulement l'icône
                'url' => route('dashboard'),
                'icon' => '<span class="inline-flex items-center gap-2.5">
<svg class="text-gray-600 hover:text-indigo-600" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5.66667 13.1667H12.3333M8.18141 1.30333L2.52949 5.69927C2.15168 5.99312 1.96278 6.14005 1.82669 6.32405C1.70614 6.48704 1.61633 6.67065 1.56169 6.86588C1.5 7.08627 1.5 7.32558 1.5 7.80421V13.8333C1.5 14.7667 1.5 15.2335 1.68166 15.59C1.84144 15.9036 2.09641 16.1585 2.41002 16.3183C2.76654 16.5 3.23325 16.5 4.16667 16.5H13.8333C14.7668 16.5 15.2335 16.5 15.59 16.3183C15.9036 16.1585 16.1586 15.9036 16.3183 15.59C16.5 15.2335 16.5 14.7667 16.5 13.8333V7.80421C16.5 7.32558 16.5 7.08627 16.4383 6.86588C16.3837 6.67065 16.2939 6.48704 16.1733 6.32405C16.0372 6.14005 15.8483 5.99312 15.4705 5.69927L9.81859 1.30333C9.52582 1.07562 9.37943 0.961766 9.21779 0.918001C9.07516 0.879384 8.92484 0.879384 8.78221 0.918001C8.62057 0.961766 8.47418 1.07562 8.18141 1.30333Z" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg><span class="font-medium">Tableau de bord</span>
</span>',
                'current' => true,
            ];

            return;
        }

        // Pour toutes les autres pages, le premier segment est l'icône de maison (sans texte)
        $this->segments[] = [
            'name' => 'dashboard',
            'label' => '',  // Pas de texte, seulement l'icône
            'url' => route('dashboard'),
            'icon' => $this->homeIcon,
            'current' => false,
        ];

        // Pour les routes groupées (comme invoices.* ou settings.*)
        foreach ($this->routeGroups as $groupPrefix => $parentRoutes) {
            if (strpos($currentRoute, $groupPrefix) === 0) {
                $parentRoute = $groupPrefix.'index';

                // Si la route actuelle n'est pas la route parent du groupe
                if ($currentRoute !== $parentRoute) {
                    $this->segments[] = [
                        'name' => $parentRoute,
                        'label' => $this->getRouteLabel($parentRoute),
                        'url' => route($parentRoute),
                        'icon' => null,
                        'current' => false,
                    ];
                } else {
                    // Si c'est la route parent (ex: invoices.index), c'est la route actuelle
                    $this->segments[] = [
                        'name' => $parentRoute,
                        'label' => $this->getRouteLabel($parentRoute),
                        'url' => '#',
                        'icon' => null,
                        'current' => true,
                    ];

                    return; // On s'arrête ici
                }
            }
        }

        // Pour les routes simples sans groupe (comme calendar, goals, etc.)
        if (! strpos($currentRoute, '.')) {
            $this->segments[] = [
                'name' => $currentRoute,
                'label' => $this->getRouteLabel($currentRoute),
                'url' => '#',
                'icon' => null,
                'current' => true,
            ];

            return;
        }

        // Enrichir avec des détails de ressource spécifiques (comme le nom de la facture)
        $resourceName = $this->getResourceName($currentRoute, $routeParameters);

        // Ajouter la route actuelle comme dernier segment
        $this->segments[] = [
            'name' => $currentRoute,
            'label' => $resourceName ?: $this->getRouteLabel($currentRoute),
            'url' => '#',
            'icon' => null,
            'current' => true,
        ];
    }

    /**
     * Obtenir le nom de la ressource (pour les routes de détail)
     */
    protected function getResourceName($routeName, $parameters): ?string
    {
        // Si nous avons un ID de ressource
        if (isset($parameters['id']) || $this->resourceId) {
            $id = $parameters['id'] ?? $this->resourceId;

            // Spécifique aux factures
            if ($routeName === 'invoices.edit') {
                $invoice = Invoice::find($id);
                if ($invoice) {
                    return "Modifier : {$invoice->name}";
                }
            } elseif ($routeName === 'invoices.show') {
                $invoice = Invoice::find($id);
                if ($invoice) {
                    return $invoice->name;
                }
            }
        }

        return null;
    }

    /**
     * Obtenir le libellé d'une route
     */
    protected function getRouteLabel($routeName)
    {
        return $this->routeLabels[$routeName] ?? ucfirst(str_replace(['.', '-', '_'], ' ', $routeName));
    }

    /**
     * Rendu du composant
     */
    public function render()
    {
        return view('livewire.breadcrumb');
    }
}
