<?php

namespace App\Livewire\Pages;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class InvoiceManager extends Component
{
    use WithFileUploads, WithPagination;

    #[Validate]
    public $name;

    public $file_path;

    public $file_size;

    public $type;

    public $category;

    public $issuer_name;

    public $issuer_website;

    public $amount;

    public $paid_by;

    public $associated_members;

    public $issued_date;

    public $payment_due_date;

    public $payment_reminder;

    public $payment_frequency;

    public $payment_status = 'unpaid';

    public $payment_method = 'card';

    public $priority = 'none';

    public $notes;

    public $tags = [];

    public $tagInput = '';

    public $uploadedFile;

    public $amount_distribution = [];

    public $engagement_id;

    public $engagement_name;

    public $family_members = [];

    public $engagements = [];

    public $is_archived = false;

    public $availableCategories = [];

    public $invoiceId;

    public bool $showEditFormModal = false;

    public bool $showDeleteFormModal = false;

    public $fileUrl;

    public bool $showFileModal = false;

    /* Notifications */
    public bool $addedWithSuccess = false;

    public bool $editWithSuccess = false;

    public bool $deleteWithSuccess = false;

    public bool $downloadNotWorking = false;

    /* Formulaires */
    protected $rules = [
        // Étape d'importation
        'uploadedFile' => 'required|file|mimes:pdf,docx,jpeg,png,jpg|max:10240',
        'file_path' => 'required|string|max:255',
        'file_size' => 'nullable|integer',
        // Étape 1
        'name' => 'required|string|max:255',
        'type' => 'nullable|string|max:255',
        'category' => 'nullable|string|max:255',
        'issuer_name' => 'nullable|string|max:255',
        'issuer_website' => 'nullable|url|max:255',
        // Étape 2
        'amount' => 'required|numeric|min:0',
        'paid_by' => 'nullable|string|max:255',
        'associated_members' => 'nullable|string',
        'amount_distribution' => 'nullable|array',
        // Étape 3
        'issued_date' => 'nullable|date',
        'payment_due_date' => 'nullable|date',
        'payment_reminder' => 'nullable|string|max:255',
        'payment_frequency' => 'nullable|string|max:255',
        // Étape 4
        'engagement_id' => 'nullable|string|max:255',
        'engagement_name' => 'nullable|string|max:255',
        // Étape 5
        'payment_status' => 'nullable|string|in:unpaid,paid,late,partially_paid',
        'payment_method' => 'nullable|in:card,cash,transfer',
        'priority' => 'nullable|in:high,medium,low,none',
        // Étape 6
        'notes' => 'nullable|string',
        'tags' => 'nullable|array',
        'tagInput' => 'nullable|string',
        // Archives
        'is_archived' => 'boolean',
    ];

    protected $messages = [
        'uploadedFile.required' => 'Veuillez sélectionner un fichier.',
        'uploadedFile.file' => 'Le fichier doit être un fichier valide.',
        'uploadedFile.mimes' => 'Le fichier doit être au format PDF, Word, JPEG, PNG ou JPG.',
        'uploadedFile.max' => 'Le fichier ne doit pas dépasser 10 Mo.',
        'uploadedFile.not_in' => 'Les fichiers CSV ne sont pas acceptés. Veuillez utiliser un format comme PDF, Word, JPEG, PNG ou JPG.',
        'name.required' => 'Le nom de la facture est obligatoire.',
        'issuer_website.url' => "L'URL du site web du fournisseur n'est pas valide.",
        'amount.required' => 'Le montant est obligatoire.',
        'amount.numeric' => 'Le montant doit être un nombre.',
        'amount.min' => 'Le montant doit être supérieur ou égal à zéro.',
        'amount_distribution.array' => 'La distribution du montant doit être un tableau.',
        'issued_date.date' => "La date d'émission doit être une date valide.",
        'payment_due_date.date' => "La date d'échéance doit être une date valide.",
        'payment_status.in' => 'Le statut de paiement doit être parmi : non-payée, payée, en retard, ou partiellement payée.',
        'payment_method.in' => 'La méthode de paiement doit être parmi : carte, espèces ou virement.',
        'priority.in' => 'La priorité doit être parmi : haute, moyenne, basse.',
        'tags.array' => 'Les tags doivent être un tableau.',
    ];

    /* Filtres et colonnes */
    public $sortField = 'name';

    public $sortDirection = 'desc';

    public $activeFilter = null;

    public array $visibleColumns = [
        'name' => true,
        'file_size' => true,
        'issued_date' => true,
        'tags' => true,
        'payment_status' => false,
        'payment_due_date' => false,
        'amount' => false,
        'issuer_name' => false,
        'type' => false,
        'category' => false,
    ];

    public $availableFilters = [
        'name_asc' => 'Par ordre alphabétique (A-Z)',
        'name_desc' => 'Par ordre alphabétique (Z-A)',
        'issued_date_asc' => 'Date d\'ajout (plus ancien)',
        'issued_date_desc' => 'Date d\'ajout (plus récent)',
        'payment_due_date_asc' => 'Date de paiement (plus ancien)',
        'payment_due_date_desc' => 'Date de paiement (plus récent)',
        'amount_asc' => 'Montant (du moins cher au plus cher)',
        'amount_desc' => 'Montant (du plus cher au moins cher)',
        'file_size_asc' => 'Taille du fichier (du petit au grand)',
        'file_size_desc' => 'Taille du fichier (du grand au petit)',
        'payment_status_paid' => 'Status: Payé',
        'payment_status_unpaid' => 'Status: Impayé',
    ];

    // Définir les paramètres qui doivent être préservés pendant la pagination
    protected $queryString = [
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'desc'],
        'activeFilter' => ['except' => ''],
    ];

    /* Factures récentes */
    public $recentInvoices = [];

    // Ajouter ces propriétés à ta classe InvoiceManager
    public bool $showFolderModal = false;

    public string $currentFolder = '';

    public string $folderTitle = '';

    public $folderInvoices = [];

    // Ajouter ces méthodes à ta classe InvoiceManager
    public function openFolder($folder, $title)
    {
        $this->currentFolder = $folder;
        $this->folderTitle = $title;

        // Récupération des factures selon le dossier sélectionné
        switch ($folder) {
            case 'favorites':
                // Exemple: Factures marquées comme favorites (tu devras adapter selon ta structure de données)
                $this->folderInvoices = auth()->user()->invoices()
                    ->where('is_favorite', true)
                    ->orderBy($this->sortField, $this->sortDirection)
                    ->get();
                break;

            case 'paid':
                $this->folderInvoices = auth()->user()->invoices()
                    ->where('payment_status', 'paid')
                    ->orderBy($this->sortField, $this->sortDirection)
                    ->get();
                break;

            case 'unpaid':
                $this->folderInvoices = auth()->user()->invoices()
                    ->where('payment_status', 'unpaid')
                    ->orderBy($this->sortField, $this->sortDirection)
                    ->get();
                break;

            case 'late':
                $this->folderInvoices = auth()->user()->invoices()
                    ->where('payment_status', 'late')
                    ->orderBy($this->sortField, $this->sortDirection)
                    ->get();
                break;

            case 'last_week':
                $lastWeek = now()->subWeek();
                $this->folderInvoices = auth()->user()->invoices()
                    ->where('issued_date', '>=', $lastWeek)
                    ->orderBy($this->sortField, $this->sortDirection)
                    ->get();
                break;

            case 'high_amount':
                // Par exemple, montant supérieur à 1000
                $this->folderInvoices = auth()->user()->invoices()
                    ->where('amount', '>=', 1000)
                    ->orderBy($this->sortField, $this->sortDirection)
                    ->get();
                break;

            case 'high_priority':
                $this->folderInvoices = auth()->user()->invoices()
                    ->where('priority', 'high')
                    ->orderBy($this->sortField, $this->sortDirection)
                    ->get();
                break;

            default:
                $this->folderInvoices = collect();
                break;
        }

        $this->showFolderModal = true;
    }

    // Méthode pour obtenir les statistiques des dossiers
    public function getFolderStats()
    {
        return [
            'favorites' => [
                'count' => auth()->user()->invoices()->where('is_favorite', true)->count(),
                'size' => $this->formatFileSize(auth()->user()->invoices()->where('is_favorite', true)->sum('file_size')),
            ],
            'paid' => [
                'count' => auth()->user()->invoices()->where('payment_status', 'paid')->count(),
                'size' => $this->formatFileSize(auth()->user()->invoices()->where('payment_status', 'paid')->sum('file_size')),
            ],
            'unpaid' => [
                'count' => auth()->user()->invoices()->where('payment_status', 'unpaid')->count(),
                'size' => $this->formatFileSize(auth()->user()->invoices()->where('payment_status', 'unpaid')->sum('file_size')),
            ],
            'late' => [
                'count' => auth()->user()->invoices()->where('payment_status', 'late')->count(),
                'size' => $this->formatFileSize(auth()->user()->invoices()->where('payment_status', 'late')->sum('file_size')),
            ],
            'last_week' => [
                'count' => auth()->user()->invoices()->where('issued_date', '>=', now()->subWeek())->count(),
                'size' => $this->formatFileSize(auth()->user()->invoices()->where('issued_date', '>=', now()->subWeek())->sum('file_size')),
            ],
            'high_amount' => [
                'count' => auth()->user()->invoices()->where('amount', '>=', 1000)->count(),
                'size' => $this->formatFileSize(auth()->user()->invoices()->where('amount', '>=', 1000)->sum('file_size')),
            ],
            'high_priority' => [
                'count' => auth()->user()->invoices()->where('priority', 'high')->count(),
                'size' => $this->formatFileSize(auth()->user()->invoices()->where('priority', 'high')->sum('file_size')),
            ],
        ];
    }

    public function toggleFavorite($invoiceId)
    {
        $invoice = auth()->user()->invoices()->findOrFail($invoiceId);
        $invoice->update(['is_favorite' => ! $invoice->is_favorite]);
    }

    // Méthode utilitaire pour formater la taille des fichiers
    private function formatFileSize($bytes)
    {
        if ($bytes === 0 || $bytes === null) {
            return '0 KB';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes, 1024));
        $size = round($bytes / pow(1024, $i), 2);

        return $size.' '.$units[$i];
    }

    // Appliquer des filtres par défaut
    public function applyFilter($filter)
    {
        // Si le filtre est déjà actif, on le désactive
        if ($this->activeFilter === $filter) {
            $this->activeFilter = null;
            $this->resetSort();

            return;
        }

        $this->activeFilter = $filter;

        // Parser le filtre pour définir le champ et la direction
        if (in_array($filter, ['payment_status_paid', 'payment_status_unpaid'])) {
            $this->sortField = 'payment_status'; // Filtres spécifiques
            $this->sortDirection = 'asc';
        } else {
            $parts = explode('_', $filter);
            $direction = array_pop($parts);
            $field = implode('_', $parts);

            $this->sortField = $field;
            $this->sortDirection = $direction;
        }

        $this->resetPage(); // Réinitialiser la pagination lors de l'application d'un filtre
    }

    // Réinitialiser le tri et les filtres
    public function resetSort()
    {
        $this->sortField = 'name';
        $this->sortDirection = 'desc';
        $this->activeFilter = null;
        $this->resetPage();
    }

    // Toggle la visibilité des colonnes
    public function toggleColumn($column)
    {
        if (isset($this->visibleColumns[$column])) {
            $this->visibleColumns[$column] = ! $this->visibleColumns[$column];
        }
    }

    // Check si la colonne est visible
    public function isColumnVisible($column)
    {
        return isset($this->visibleColumns[$column]) && $this->visibleColumns[$column];
    }

    // Réinitialiser les colonnes aux valeurs par défaut
    public function resetColumns()
    {
        $this->visibleColumns = [
            'name' => true,
            'file_size' => true,
            'issued_date' => true,
            'tags' => true,
            'payment_status' => false,
            'payment_due_date' => false,
            'amount' => false,
            'issuer_name' => false,
            'type' => false,
            'category' => false,
        ];

        $this->js('window.location.reload()'); // thanks caleb
    }

    // Méthode pour définir la colonne de tri et la direction
    public function sortBy($field)
    {
        $this->activeFilter = null; // Reset les filtres actifs lors du tri

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage(); // Réinitialiser la pagination lors du tri
    }

    // TODO : Télécharger tout les fichiers dans un ordre spécifique
    public function downloadAllFiles()
    {
        dd('Download en cours');
    }

    // Télécharger la facture
    public function downloadInvoice($invoiceId)
    {
        $invoice = auth()->user()->invoices()->findOrFail($invoiceId);

        // Extraire le chemin du fichier sans le préfixe "storage"
        $filePath = str_replace(asset('storage/'), '', $invoice->file_path);

        // Vérifier si le fichier existe
        if (! Storage::disk('public')->exists($filePath)) {
            $this->downloadNotWorking = true;

            return null;
        }

        // Obtenir le nom du fichier original
        $fileName = basename($filePath);

        // Générer un nom de fichier plus stylé
        $downloadName = Str::slug($invoice->name).'_'.$invoice->id.'.'.pathinfo($fileName, PATHINFO_EXTENSION);

        // Télécharger le fichier
        return Storage::disk('public')->download($filePath, $downloadName);
    }

    // Afficher la modal de la facture
    public function showFile($id)
    {
        $invoice = auth()->user()->invoices()->findOrFail($id);
        $this->fileUrl = $invoice->file_path;
        $this->showFileModal = true;
    }

    // Afficher le formulaire d'édition
    public function showEditForm($id)
    {
        $this->showEditFormModal = true;
    }

    // Mettre à jour la facture
    public function updateInvoice()
    {
        dd('Update en cours');
    }

    // Afficher le formulaire de suppression
    public function showDeleteForm($id)
    {
        $invoice = auth()->user()->invoices()->findOrFail($id);
        $this->invoiceId = $invoice->id;
        $this->showDeleteFormModal = true;
    }

    // Supprimer la facture
    public function deleteInvoice()
    {
        $invoice = auth()->user()->invoices()->findOrFail($this->invoiceId);

        try {
            DB::beginTransaction();

            $invoice->delete();

            DB::commit();

            $this->deleteWithSuccess = true;
            $this->showDeleteFormModal = false;

            sleep(1);

            $this->loadInvoices();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage());
        }
    }

    public function render()
    {
        // Récupération des factures avec filtres
        $invoices = auth()->user()->invoices()
            ->when(
                $this->activeFilter === 'payment_status_paid',
                fn ($query) => $query->where('payment_status', 'paid')
            )
            ->when(
                $this->activeFilter === 'payment_status_unpaid',
                fn ($query) => $query->where('payment_status', 'unpaid')
            )
            ->when(
                $this->sortField,
                fn ($query) => $query->orderBy($this->sortField, $this->sortDirection)
            )
            ->paginate(10);

        $this->recentInvoices = auth()->user()->invoices()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $folderStats = $this->getFolderStats();

        // Rendu de la vue
        return view('livewire.pages.invoice-manager', compact('invoices', 'folderStats'))
            ->layout('layouts.app-sidebar');
    }
}
