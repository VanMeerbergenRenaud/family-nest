<?php

namespace App\Livewire\Pages\Invoices;

use App\Models\Invoice;
use App\Models\InvoiceFile;
use App\Traits\InvoiceFileUrlTrait;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

class Index extends Component
{
    use InvoiceFileUrlTrait;
    use WithPagination;

    public Invoice $invoice;

    public $is_archived = false;

    public $filePath;

    public $fileExtension;

    public $fileName;

    public $fileExists = false;

    public bool $showInvoicePreviewModal = false;

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
    public function getFolderStats(): array
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
    public function applyFilter($filter): void
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

    public function resetSort(): void
    {
        $this->sortField = 'name';
        $this->sortDirection = 'desc';
        $this->activeFilter = null;
        $this->resetPage();
    }

    public function toggleColumn($column): void
    {
        if (isset($this->visibleColumns[$column])) {
            $this->visibleColumns[$column] = ! $this->visibleColumns[$column];
        }
    }

    public function isColumnVisible($column): bool
    {
        return isset($this->visibleColumns[$column]) && $this->visibleColumns[$column];
    }

    public function resetColumns(): void
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

    public function sortBy($field): void
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

    public function toggleFavorite($invoiceId): void
    {
        $invoice = auth()->user()->invoices()->findOrFail($invoiceId);
        $invoice->update(['is_favorite' => ! $invoice->is_favorite]);
        $this->showFolderModal = false;
    }

    // Implémentations des fonctionnalités manquantes
    public function downloadAllFiles(): void
    {
        Toaster::error('Méthode de téléchargement de plusieurs fichiers non implémentée.');
    }

    public function downloadInvoice($invoiceId)
    {
        $invoiceFile = InvoiceFile::where('invoice_id', $invoiceId)
            ->where('is_primary', true)
            ->first();

        if (! $invoiceFile) {
            Toaster::error('Aucun fichier trouvé pour cette facture.');

            return;
        }

        try {
            // Récupérer le chemin brut du fichier dans S3
            $s3FilePath = $invoiceFile->getRawOriginal('file_path');

            if (! Storage::disk('s3')->exists($s3FilePath)) {
                Toaster::error('Fichier introuvable ou mal enregistré : Veuillez modifier votre facture et importer à nouveau le fichier.');

                return;
            }

            // Pour les gros fichiers, utilisez une redirection avec entêtes modifiés
            // Générer une URL présignée avec paramètres spécifiques
            $client = Storage::disk('s3')->getClient();
            $bucket = config('filesystems.disks.s3.bucket');

            // Créer une commande avec des paramètres spécifiques pour le téléchargement
            $command = $client->getCommand('GetObject', [
                'Bucket' => $bucket,
                'Key' => $s3FilePath,
                'ResponseContentType' => $this->getContentType($invoiceFile->file_extension),
                'ResponseContentDisposition' => 'attachment; filename="'.$invoiceFile->file_name.'"',
            ]);

            // Générer l'URL signée avec ces paramètres
            $request = $client->createPresignedRequest($command, '+5 minutes');
            $presignedUrl = (string) $request->getUri();

            // Rediriger vers l'URL présignée qui forcera le téléchargement
            return redirect()->away($presignedUrl);
        } catch (\Exception $e) {
            Toaster::error('Erreur lors du téléchargement : Le fichier n\'a pas pu être téléchargé.');
            \Log::error('Erreur téléchargement S3: '.$e->getMessage());

            return;
        }
    }

    // Méthode auxiliaire pour déterminer le type de contenu
    private function getContentType($extension): string
    {
        $contentTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'csv' => 'text/csv',
        ];

        return $contentTypes[strtolower($extension)] ?? 'application/octet-stream';
    }

    public function getS3FileUrl($invoiceId): ?string
    {
        $invoiceFile = InvoiceFile::where('invoice_id', $invoiceId)->where('is_primary', true)->first();

        if (! $invoiceFile) {
            return null;
        }

        $s3FilePath = $invoiceFile->getRawOriginal('file_path');

        if (Storage::disk('s3')->exists($s3FilePath)) {
            return Storage::disk('s3')->url($s3FilePath);
        }

        return null;
    }

    public function showInvoiceModal($id)
    {
        $invoice = auth()->user()->invoices()
            ->with('file')
            ->findOrFail($id);

        $this->invoice = $invoice;

        // Générer l'URL du fichier
        $fileInfo = $this->generateInvoiceFileUrl($invoice);

        $this->filePath = $fileInfo['url'];
        $this->fileExtension = $fileInfo['extension'];
        $this->fileExists = $fileInfo['exists'];
        $this->fileName = $invoice->file->file_name ?? null;

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

            Toaster::success('Facture archivée avec succès !');

            $this->redirectRoute('invoices.index');

        } catch (\Exception $e) {
            Toaster::error('Erreur lors de l\'archivage::Veuillez réessayer.');
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
