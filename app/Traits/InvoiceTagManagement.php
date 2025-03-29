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
            $this->form->tags = [];
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
        if (strlen($this->form->tagInput) < 1) {
            $this->showTagSuggestions = false;

            return;
        }

        // Utiliser directement la recherche en base de données
        $this->tagSuggestions = $this->searchTagsInDatabase($this->form->tagInput);
        $this->showTagSuggestions = count($this->tagSuggestions) > 0;
    }

    /**
     * Recherche les tags dans la base de données
     *
     * @param  string  $query  Texte à rechercher
     * @return array Liste des tags correspondants
     */
    private function searchTagsInDatabase(string $query): array
    {
        try {
            // Vérifier si l'utilisateur a des factures avec des tags
            $hasInvoicesWithTags = DB::table('invoices')
                ->where('user_id', auth()->id())
                ->whereNotNull('tags')
                ->whereRaw("tags::text != '[]'")
                ->exists();

            // Si aucune facture avec des tags, retourner un tableau vide
            if (! $hasInvoicesWithTags || empty($query)) {
                return [];
            }

            // Récupérer toutes les factures de l'utilisateur qui ont des tags
            $invoices = DB::table('invoices')
                ->where('user_id', auth()->id())
                ->whereNotNull('tags')
                ->whereRaw("tags::text != '[]'")
                ->select('tags')
                ->get();

            // Extraire tous les tags uniques
            $allTags = $this->extractTagsFromInvoices($invoices, $query);

            // Exclure les tags déjà sélectionnés
            return array_values(array_diff($allTags, $this->form->tags));
        } catch (\Exception $e) {
            Log::error('Erreur lors de la recherche de tags : '.$e->getMessage());

            return [];
        }
    }

    /**
     * Extrait les tags des factures qui correspondent à la requête
     *
     * @param  \Illuminate\Support\Collection  $invoices  Collection de factures
     * @param  string  $query  Texte à rechercher
     * @return array Liste des tags uniques
     */
    private function extractTagsFromInvoices($invoices, string $query): array
    {
        $allTags = [];

        foreach ($invoices as $invoice) {
            try {
                // Gérer le double encodage JSON possible
                $tagsJson = $invoice->tags;

                // Si la chaîne commence et se termine par des guillemets, retirer ces guillemets
                if (substr($tagsJson, 0, 1) === '"' && substr($tagsJson, -1) === '"') {
                    $tagsJson = substr($tagsJson, 1, -1);
                }

                // Remplacer les séquences d'échappement
                $tagsJson = str_replace('\\', '', $tagsJson);

                // Décoder le JSON
                $tagsArray = json_decode($tagsJson, true);

                // Si le décodage a échoué, essayer une autre approche
                if (! is_array($tagsArray)) {
                    $tagsArray = json_decode($invoice->tags, true);
                }

                // Ajouter les tags qui contiennent la requête
                if (is_array($tagsArray)) {
                    foreach ($tagsArray as $tag) {
                        if (is_string($tag) && stripos($tag, $query) !== false) {
                            $allTags[] = $tag;
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Erreur de décodage JSON pour les tags: '.$e->getMessage());

                continue;
            }
        }

        // Retourner les tags uniques
        return array_unique($allTags);
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
