<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait InvoiceTagManagement
{
    // Propriétés pour stocker les suggestions de tags
    public array $tagSuggestions = [];

    // Indique si le menu d'autocomplétion est visible
    public bool $showTagSuggestions = false;

    /**
     * Initialise les propriétés liées aux tags
     */
    public function initializeTagManagement(): void
    {
        // Initialiser le tableau des tags s'il est null
        if (! is_array($this->form->tags)) {
            $this->form->tags = [] ?? null;
        }
    }

    /**
     * Ajoute un tag à la liste des tags
     */
    public function addTag(): void
    {
        if (! empty($this->form->tagInput)) {
            // Vérifier si le tag n'existe pas déjà
            if (! in_array($this->form->tagInput, $this->form->tags)) {
                $this->form->tags[] = $this->form->tagInput;
            }
            $this->form->tagInput = '';
            $this->tagSuggestions = [];
            $this->showTagSuggestions = false;
        }
    }

    /**
     * Supprime un tag de la liste des tags
     */
    public function removeTag($index): void
    {
        unset($this->form->tags[$index]);
        $this->form->tags = array_values($this->form->tags); // Réindexer le tableau
    }

    /**
     * Recherche les suggestions de tags lors de la saisie
     */
    public function updatedFormTagInput(): void
    {
        $this->tagSuggestions = [];

        // Ne pas chercher si la saisie est trop courte
        if (strlen($this->form->tagInput) < 2) {
            $this->showTagSuggestions = false;

            return;
        }

        // Utiliser directement la recherche en base de données qui est adaptée au format de stockage actuel
        $this->tagSuggestions = $this->searchTagsWithDatabase($this->form->tagInput);

        $this->showTagSuggestions = count($this->tagSuggestions) > 0;
    }

    /**
     * Recherche les tags dans la base de données
     */
    private function searchTagsWithDatabase($query): array
    {
        $tag = DB::table('invoices')
                ->where('user_id', auth()->id())
                ->whereNotNull('tags')
                ->count() === 0;

        // Vérifier si l'utilisateur possède des tags dans sa db
        if ($tag) {
            return [];
        }

        // Vérifier si la requête est vide
        if (empty($query)) {
            return [];
        }

        // Récupérer toutes les factures de l'utilisateur qui ont des tags
        $invoices = DB::table('invoices')
            ->where('user_id', auth()->id())
            ->whereNotNull('tags')
            ->whereRaw("tags != '[]'")
            ->select('tags')
            ->get();

        // Extraire tous les tags
        $allTags = [];
        foreach ($invoices as $invoice) {
            try {
                // Gérer le double encodage JSON
                $tagsJson = $invoice->tags;

                // Si la chaîne commence et se termine par des guillemets, retirer ces guillemets
                if (substr($tagsJson, 0, 1) === '"' && substr($tagsJson, -1) === '"') {
                    $tagsJson = substr($tagsJson, 1, -1);
                }

                // Remplacer les séquences d'échappement
                $tagsJson = str_replace('\\', '', $tagsJson);

                // Maintenant décoder le JSON
                $tagsArray = json_decode($tagsJson, true);

                if (! is_array($tagsArray)) {
                    // Si ce n'est toujours pas un tableau, essayer une autre approche
                    $tagsArray = json_decode($invoice->tags, true);
                }

                if (is_array($tagsArray)) {
                    foreach ($tagsArray as $tag) {
                        // Ne garder que les tags qui contiennent la requête
                        if (is_string($tag) && stripos($tag, $query) !== false) {
                            $allTags[] = $tag;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Si le JSON est invalide, enregistrer l'erreur et continuer
                Log::warning('Erreur de décodage JSON pour les tags: '.$e->getMessage().' - Tags: '.$invoice->tags);

                continue;
            }
        }

        // Filtrer pour avoir des tags uniques
        $uniqueTags = array_unique($allTags);

        // Exclure les tags déjà sélectionnés
        $filteredTags = array_values(array_diff($uniqueTags, $this->form->tags));

        return $filteredTags;
    }

    /**
     * Sélectionne un tag parmi les suggestions
     */
    public function selectTag($tag): void
    {
        if (! in_array($tag, $this->form->tags)) {
            $this->form->tags[] = $tag;
        }
        $this->form->tagInput = '';
        $this->tagSuggestions = [];
        $this->showTagSuggestions = false;
    }
}
