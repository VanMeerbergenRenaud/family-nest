<?php

namespace App\Traits\Invoice;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Masmerise\Toaster\Toaster;

trait TagManagement
{
    public array $tagSuggestions = [];

    public bool $showTagSuggestions = false;

    public function initializeTagManagement(): void
    {
        if (! is_array($this->form->tags)) {
            $this->form->tags = [];
        }
    }

    public function addTag(): void
    {
        $tag = trim($this->form->tagInput ?? '');

        if (empty($tag)) {
            return;
        }

        // Vérifier que le tag contient uniquement des lettres
        if (! preg_match('/^[a-zA-Z]+$/', $tag)) {
            Toaster::error('Le tag ne peut contenir que des lettres.');

            return;
        }

        // Vérifier la longueur maximale (15 caractères)
        if (strlen($tag) > 15) {
            Toaster::error('Le tag ne peut pas dépasser 15 caractères.');

            return;
        }

        // Vérifier le nombre maximum de tags (10)
        if (count($this->form->tags) >= 10) {
            Toaster::error('Vous ne pouvez pas ajouter plus de 10 tags.');

            return;
        }

        // Vérifier si le tag existe déjà (insensible à la casse)
        if (! in_array(strtolower($tag), array_map('strtolower', $this->form->tags))) {
            $this->form->tags[] = $tag;
        }

        // Réinitialiser l'input
        $this->form->tagInput = '';
        $this->tagSuggestions = [];
        $this->showTagSuggestions = false;
    }

    public function removeTag($index): void
    {
        if (isset($this->form->tags[$index])) {
            unset($this->form->tags[$index]);
            $this->form->tags = array_values($this->form->tags); // Réindexer le tableau
        }
    }

    public function updatedFormTagInput(): void
    {
        $input = trim($this->form->tagInput ?? '');

        if (strlen($input) < 1) {
            $this->showTagSuggestions = false;
            $this->tagSuggestions = [];

            return;
        }

        $this->tagSuggestions = $this->searchTagsInDatabase($input);
        $this->showTagSuggestions = count($this->tagSuggestions) > 0;
    }

    public function selectTag($tag): void
    {
        $this->form->tagInput = $tag;
        $this->addTag();
    }

    private function searchTagsInDatabase(string $query): array
    {
        try {
            // Rechercher les tags qui correspondent à la requête
            $invoices = DB::table('invoices')
                ->where('user_id', auth()->id())
                ->whereJsonLength('tags', '>', 0)
                ->select('tags')
                ->get();

            if ($invoices->isEmpty()) {
                return [];
            }

            // Extraire tous les tags correspondants
            $allTags = [];
            $userTags = array_map('strtolower', $this->form->tags ?? []);

            foreach ($invoices as $invoice) {
                try {
                    $tags = json_decode($invoice->tags, true);

                    if (! is_array($tags)) {
                        continue;
                    }

                    foreach ($tags as $tag) {
                        // Filtrer les tags valides qui contiennent la requête et qui ne sont pas déjà sélectionnés
                        if (is_string($tag) &&
                            stripos($tag, $query) !== false &&
                            preg_match('/^[a-zA-Z]+$/', $tag) &&
                            strlen($tag) <= 15 &&  // S'assurer que le tag ne dépasse pas 15 caractères
                            ! in_array(strtolower($tag), $userTags)) {
                            $allTags[] = $tag;
                        }
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            return array_values(array_unique($allTags));
        } catch (\Exception $e) {
            Log::error('Erreur lors de la recherche de tags : '.$e->getMessage());

            return [];
        }
    }
}
