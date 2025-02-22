<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Collection; // Assure-toi d'importer les modèles que tu veux rendre searchable
use Livewire\Component;

class Spotlight extends Component
{
    public bool $spotlightOpen = false;

    public string $search = ''; // Ajoute la propriété $search pour la requête de recherche

    public Collection $results; // Ajoute la propriété $results pour stocker les résultats de recherche

    public array $suggestions = [ // Tes suggestions actuelles (laisse-les comme elles sont)
        [
            'label' => 'Tableau de bord',
            'action' => '/dashboard', // Remplace par tes actions réelles
            'shortcut' => 'D',
        ],
        [
            'label' => 'Gérer les utilisateurs',
            'action' => '/users', // Remplace par tes actions réelles
            'shortcut' => 'U',
        ],
        // ... tes autres suggestions ...
    ];

    public function mount(): void
    {
        $this->results = collect(); // Initialise $results comme une collection vide au montage du composant
    }

    public function updatedSearch(string $value): void // Méthode Livewire pour la recherche en temps réel
    {
        if (strlen($value) < 2) { // Optionnel : Nombre minimum de caractères pour lancer la recherche
            $this->results = collect(); // Vide les résultats si la requête est trop courte

            return;
        }

        $this->results = collect(); // Réinitialise les résultats à chaque nouvelle recherche

        // Recherche dans différents modèles (adapte à tes modèles Searchable)
        $userResults = User::search($value)->take(3)->get(); // Recherche les utilisateurs, limite à 3 résultats

        // Fusionne les résultats dans la collection $results
        $this->results = $this->results->concat($userResults);

        // Optionnel : Grouper les résultats par type de modèle (pour l'affichage dans le Blade)
        $this->results = $this->results->groupBy(function ($item) {
            return match (true) {
                $item instanceof User => 'Utilisateurs',
                default => 'Autres résultats', // Catégorie par défaut si le modèle n'est pas reconnu
            };
        });
    }

    public function render()
    {
        return view('livewire.spotlight', [
            'results' => $this->results, // Passe les résultats à la vue Blade
            'suggestions' => $this->suggestions, // Passe aussi les suggestions (si tu les utilises toujours)
        ]);
    }
}
