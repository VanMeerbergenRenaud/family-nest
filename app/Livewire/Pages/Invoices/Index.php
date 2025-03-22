<?php

namespace App\Livewire\Pages\Invoices;

use App\Models\Invoice;
use App\Models\InvoiceFile;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public Invoice $invoice;

    public $is_archived = false;

    public $filePath;

    public $fileExtension;

    public bool $showInvoicePreviewModal = false;

    public bool $archivedWithSuccess;

    public bool $downloadNotWorking = false;

    // Filtres et colonnes...
    public $sortField = 'name';

    public $sortDirection = 'desc';

    public $activeFilter = null;

    public array $visibleColumns = [
        'name' => true,
        'type' => false,
        'category' => false,
        'issuer_name' => false,
        'amount' => true,
        'issued_date' => true,
        'payment_status' => false,
        'payment_due_date' => false,
        'tags' => true,
    ];

    // Ajouter les propriétés pour les modales de dossiers
    public $recentInvoices = [];

    public bool $showFolderModal = false;

    public string $currentFolder = '';

    public string $folderTitle = '';

    public $folderInvoices = [];

    // Filtres disponibles
    public $availableFilters = [
        'name_asc' => 'Par ordre alphabétique (A-Z)',
        'name_desc' => 'Par ordre alphabétique (Z-A)',
        'issued_date_asc' => 'Date d\'ajout (plus ancien)',
        'issued_date_desc' => 'Date d\'ajout (plus récent)',
        'payment_due_date_asc' => 'Date de paiement (plus ancien)',
        'payment_due_date_desc' => 'Date de paiement (plus récent)',
        'amount_asc' => 'Montant (du moins cher au plus cher)',
        'amount_desc' => 'Montant (du plus cher au moins cher)',
    ];

    // Méthodes pour le dossier
    public function openFolder($folder, $title): void
    {
        $this->currentFolder = $folder;
        $this->folderTitle = $title;

        $query = auth()->user()->invoices()
            ->with('file')
            ->where('is_archived', false);

        // Requête commune à tous les dossiers
        switch ($folder) {
            case 'favorites':
                $query->where('is_favorite', true);
                break;
            case 'paid':
                $query->where('payment_status', 'paid');
                break;
            case 'unpaid':
                $query->where('payment_status', 'unpaid');
                break;
            case 'late':
                $query->where('payment_status', 'late');
                break;
            case 'last_week':
                $query->where('issued_date', '>=', now()->subWeek());
                break;
            case 'high_priority':
                $query->where('priority', 'high');
                break;
            default:
                $this->folderInvoices = collect();
                $this->showFolderModal = true;

                return;
        }

        $this->folderInvoices = $query->orderBy($this->sortField, $this->sortDirection)->get();
        $this->showFolderModal = true;
    }

    // Méthode pour obtenir les statistiques des dossiers
    public function getFolderStats()
    {
        $invoice = auth()->user()->invoices
            ->where('is_archived', false);

        return [
            'favorites' => [
                'count' => $invoice->where('is_favorite', true)->count(),
                'amount' => $invoice->where('is_favorite', true)->sum('amount'),
            ],
            'paid' => [
                'count' => $invoice->where('payment_status', 'paid')->count(),
                'amount' => $invoice->where('payment_status', 'paid')->sum('amount'),
            ],
            'unpaid' => [
                'count' => $invoice->where('payment_status', 'unpaid')->count(),
                'amount' => $invoice->where('payment_status', 'unpaid')->sum('amount'),
            ],
            'late' => [
                'count' => $invoice->where('payment_status', 'late')->count(),
                'amount' => $invoice->where('payment_status', 'late')->sum('amount'),
            ],
            'high_priority' => [
                'count' => $invoice->where('priority', 'high')->count(),
                'amount' => $invoice->where('priority', 'high')->sum('amount'),
            ],
        ];
    }

    // Méthodes de filtrage
    public function applyFilter($filter)
    {
        if (empty($filter)) {
            $this->activeFilter = null;
            $this->resetSort();

            return;
        }

        $this->activeFilter = $filter;

        $parts = explode('_', $filter);
        $direction = array_pop($parts);
        $field = implode('_', $parts);

        $this->sortField = $field;
        $this->sortDirection = $direction;

        $this->resetPage();
    }

    public function resetSort()
    {
        $this->sortField = 'name';
        $this->sortDirection = 'desc';
        $this->activeFilter = null;
        $this->resetPage();
    }

    public function toggleColumn($column)
    {
        if (isset($this->visibleColumns[$column])) {
            $this->visibleColumns[$column] = ! $this->visibleColumns[$column];
        }
    }

    public function isColumnVisible($column)
    {
        return isset($this->visibleColumns[$column]) && $this->visibleColumns[$column];
    }

    public function resetColumns()
    {
        $this->visibleColumns = [
            'name' => true,
            'issued_date' => true,
            'type' => false,
            'category' => false,
            'issuer_name' => false,
            'amount' => true,
            'payment_status' => false,
            'payment_due_date' => false,
            'tags' => true,
        ];

        $this->js('window.location.reload()');
    }

    public function sortBy($field)
    {
        $this->activeFilter = null;

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function toggleFavorite($invoiceId)
    {
        $invoice = auth()->user()->invoices()->findOrFail($invoiceId);
        $invoice->update(['is_favorite' => ! $invoice->is_favorite]);
        $this->showFolderModal = false;
    }

    // Implémentations des fonctionnalités manquantes
    public function downloadAllFiles()
    {
        dd('Download all the files');
    }

    public function downloadInvoice($invoiceId)
    {
        $invoiceFile = InvoiceFile::where('invoice_id', $invoiceId)->where('is_primary', true)->first();

        if (! $invoiceFile) {
            session()->flash('error', 'Fichier de facture non trouvé.');

            return;
        }

        $filePath = storage_path('app/public/'.$invoiceFile->getRawOriginal('file_path'));

        if (! file_exists($filePath)) {
            session()->flash('error', 'Le fichier n\'existe pas sur le serveur.');

            return;
        }

        return response()->download($filePath, $invoiceFile->file_name);
    }

    public function showInvoiceModal($id)
    {
        $invoice = auth()->user()->invoices()
            ->with('file')
            ->findOrFail($id);

        $this->invoice = $invoice;

        // Vérifier si le fichier existe avant d'accéder à ses propriétés
        if ($invoice->file) {
            $this->filePath = $invoice->file->file_path;
            $this->fileExtension = $invoice->file->file_extension;
        } else {
            // Définir des valeurs par défaut si aucun fichier n'est trouvé
            $this->filePath = null;
            $this->fileExtension = null;
        }

        $this->showFolderModal = false;
        $this->showInvoicePreviewModal = true;
    }

    public function archiveInvoice($invoiceId): void
    {
        try {
            $this->invoice = Invoice::where('id', $invoiceId)
                ->where('is_archived', false)
                ->firstOrFail();

            $this->invoice->update(['is_archived' => true]);

            $this->showFolderModal = false;
            $this->archivedWithSuccess = true;
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'archivage de la facture : '.$e->getMessage());
        }
    }

    public function render()
    {
        // Récupération des factures avec filtres
        $invoices = auth()->user()->invoices()
            ->with('file')
            ->when($this->sortField,
                fn ($query) => $query->orderBy($this->sortField, $this->sortDirection)
            )
            ->where('is_archived', false)
            ->paginate(8);

        $this->recentInvoices = auth()->user()->invoices()
            ->with('file')
            ->where('is_archived', false)
            ->orderBy('updated_at', 'desc')
            ->limit(8)
            ->get();

        $folderStats = $this->getFolderStats();

        return view('livewire.pages.invoices.index', compact('invoices', 'folderStats'))
            ->layout('layouts.app-sidebar');
    }
}
