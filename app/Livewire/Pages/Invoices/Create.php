<?php

namespace App\Livewire\Pages\Invoices;

use App\Enums\InvoiceTypeEnum;
use App\Livewire\Forms\InvoiceForm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public InvoiceForm $form;

    public $family_members = [];

    // Propriété pour stocker les suggestions de tags
    public $tagSuggestions = [];

    // Indique si le menu d'autocomplétion est visible
    public $showTagSuggestions = false;

    public function mount()
    {
        $this->family_members = auth()->user()->get();

        // Initialiser le tableau des tags s'il est null
        if (! is_array($this->form->tags)) {
            $this->form->tags = [];
        }

        if (! is_array($this->form->associated_members)) {
            $this->form->associated_members = [];
        }
    }

    public function updatedFormType()
    {
        $this->form->updateAvailableCategories();
        $this->form->category = null; // Réinitialiser la catégorie lorsque le type change
    }

    // Méthode pour mettre à jour les montants partagés
    public function updateShareAmounts()
    {
        // Si vous avez une méthode pour mettre à jour les montants partagés
    }

    // Méthode pour mettre à jour les pourcentages
    public function updateShareFromPercentage($percentage, $member)
    {
        // Si vous avez une méthode pour mettre à jour les pourcentages
    }

    public function addTag()
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

    public function removeTag($index)
    {
        unset($this->form->tags[$index]);
        $this->form->tags = array_values($this->form->tags); // Réindexer le tableau
    }

    // Méthode pour rechercher des suggestions de tags
    public function updatedFormTagInput()
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

    // Méthode de recherche en base de données adaptée au double encodage JSON
    private function searchTagsWithDatabase($query)
    {
        if (empty($query)) {
            return [];
        }

        // Récupérer toutes les factures de l'utilisateur qui ont des tags
        $invoices = DB::table('invoices')
            ->where('user_id', auth()->id())
            ->whereNotNull('tags')
            ->where('tags', '<>', '[]')
            ->where('tags', '<>', '""[]""')
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

    // Sélectionner un tag suggéré
    public function selectTag($tag)
    {
        if (! in_array($tag, $this->form->tags)) {
            $this->form->tags[] = $tag;
        }
        $this->form->tagInput = '';
        $this->tagSuggestions = [];
        $this->showTagSuggestions = false;
    }

    public function createInvoice()
    {
        $invoice = $this->form->store();

        if ($invoice) {
            $this->redirectRoute('invoices', $invoice);
        } else {
            session()->flash('error', 'Une erreur est survenue lors de la création de la facture');
        }
    }

    public function removeUploadedFile()
    {
        $this->form->removeFile();
        $this->form->resetErrorBag('uploadedFile');
    }

    public function render()
    {
        return view('livewire.pages.invoices.create', [
            'invoiceTypes' => InvoiceTypeEnum::getTypesOptions(),
        ])->layout('layouts.app');
    }
}
