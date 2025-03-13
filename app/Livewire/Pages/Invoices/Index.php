<?php

namespace App\Livewire\Pages\Invoices;

use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public Invoice $invoiceId;

    public $is_archived = false;

    public $fileUrl;

    public bool $showInvoicePreviewModal = false;

    public bool $showDeleteFormModal = false;

    /* Notifications */
    public bool $addedWithSuccess = false;

    public bool $editWithSuccess = false;

    public bool $deleteWithSuccess = false;

    public bool $downloadNotWorking = false;

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

    // Ajouter ces propriétés à ta classe Index
    public bool $showFolderModal = false;

    public string $currentFolder = '';

    public string $folderTitle = '';

    public $folderInvoices = [];

    // Ajouter ces méthodes à ta classe Index
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
    public function showInvoiceModal($id)
    {
        $invoice = auth()->user()->invoices()->findOrFail($id);
        $this->fileUrl = $invoice->file_path;
        $this->showInvoicePreviewModal = true;
    }

    // Rediriger vers la page de la facture
    public function showInvoicePage($id)
    {
        $invoiceId = auth()->user()->invoices()->findOrFail($id)->id;
        $this->redirectRoute('invoices.show', $invoiceId);
    }

    // Rediriger vers la page d'édition de la facture
    public function showEditPage($invoiceId)
    {
        $this->redirectRoute('invoices.edit', $invoiceId);
    }

    // Mettre à jour la facture
    public function updateInvoice()
    {
        $this->form->update();

        $this->redirectRoute('invoices');
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
        dd('Suppression en cours');
    }

    // Archive ou désarchive la facture
    public function archiveInvoice()
    {
        dd('Archive en cours');
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
        return view('livewire.pages.invoices.index', compact('invoices', 'folderStats'))
            ->layout('layouts.app-sidebar');
    }
}
