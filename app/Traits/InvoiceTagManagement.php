<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait InvoiceTagManagement
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

        if (! empty($tag)) {
            $this->addTagToForm($tag);
        }
    }

    private function resetTagInput(): void
    {
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
        $this->tagSuggestions = [];
        $input = trim($this->form->tagInput ?? '');

        // Ne pas chercher si la saisie est trop courte
        if (strlen($input) < 1) {
            $this->showTagSuggestions = false;

            return;
        }

        $this->tagSuggestions = $this->searchTagsInDatabase($input);
        $this->showTagSuggestions = count($this->tagSuggestions) > 0;
    }

    private function searchTagsInDatabase(string $query): array
    {
        try {
            if (empty($query)) {
                return [];
            }

            $invoices = DB::table('invoices')
                ->where('user_id', auth()->id())
                ->whereJsonLength('tags', '>', 0)
                ->select('tags')
                ->get();

            if ($invoices->isEmpty()) {
                return [];
            }

            $allTags = $this->extractUniqueMatchingTags($invoices, $query);

            $selectedTags = array_map('strtolower', $this->form->tags ?? []);

            return array_values(array_filter($allTags, function ($tag) use ($selectedTags) {
                return ! in_array(strtolower($tag), $selectedTags);
            }));
        } catch (\Exception $e) {
            Log::error('Erreur lors de la recherche de tags : '.$e->getMessage());

            return [];
        }
    }

    private function extractUniqueMatchingTags(Collection $invoices, string $query): array
    {
        $allTags = [];

        foreach ($invoices as $invoice) {
            try {
                $tags = $this->parseJsonTags($invoice->tags);

                foreach ($tags as $tag) {
                    if (is_string($tag) && stripos($tag, $query) !== false) {
                        $allTags[] = $tag;
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Erreur de décodage JSON pour les tags: '.$e->getMessage());

                continue;
            }
        }

        return array_unique($allTags);
    }

    private function parseJsonTags(?string $tagsJson): array
    {
        if (empty($tagsJson)) {
            return [];
        }

        if (str_starts_with($tagsJson, '"') && str_ends_with($tagsJson, '"')) {
            $tagsJson = substr($tagsJson, 1, -1);
        }

        $tagsJson = str_replace('\\', '', $tagsJson);

        $tagsArray = json_decode($tagsJson, true);

        if (! is_array($tagsArray)) {
            $tagsArray = json_decode($tagsJson, true);
        }

        return is_array($tagsArray) ? $tagsArray : [];
    }

    public function selectTag($tag): void
    {
        $this->addTagToForm($tag);
    }

    private function addTagToForm(string $tag): void
    {
        $lowerTag = strtolower($tag);
        $exists = false;

        foreach ($this->form->tags as $existingTag) {
            if (strtolower($existingTag) === $lowerTag) {
                $exists = true;
                break;
            }
        }

        if (! $exists) {
            $this->form->tags[] = $tag;
        }

        $this->resetTagInput();
    }
}
