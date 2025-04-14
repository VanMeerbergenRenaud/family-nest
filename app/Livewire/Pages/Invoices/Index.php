<?php

namespace App\Livewire\Pages\Invoices;

use App\Enums\PaymentStatusEnum;
use App\Enums\PriorityEnum;
use App\Models\Invoice;
use App\Models\InvoiceFile;
use App\Traits\InvoiceFileUrlTrait;
use App\Traits\InvoiceShareCalculationTrait;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toaster;

#[Title('Factures')]
class Index extends Component
{
    use InvoiceFileUrlTrait;
    use InvoiceShareCalculationTrait;
    use WithPagination;

    public Invoice $invoice;

    public $is_archived = false;

    public $filePath;

    public $fileExtension;

    public $fileName;

    public $fileExists = false;

    public bool $showInvoicePreviewModal = false;

    public bool $showSidebarInvoiceDetails = false;

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

    public $selectedInvoiceIds = [];

    public $invoiceIdsOnPage = [];

    public $selectedPaymentStatus = null;

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

    public function showSidebarInvoice($id): void
    {
        $this->invoice = auth()->user()->invoices()
            ->with(['file', 'sharedUsers'])
            ->findOrFail($id);

        $this->showSidebarInvoiceDetails = true;
    }

    public function toggleSidebar(): void
    {
        $this->showSidebarInvoiceDetails = ! $this->showSidebarInvoiceDetails;
    }

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
                $query->where('payment_status', PaymentStatusEnum::Paid->value);
                break;
            case 'unpaid':
                $query->where('payment_status', PaymentStatusEnum::Unpaid->value);
                break;
            case 'late':
                $query->where('payment_status', PaymentStatusEnum::Late->value);
                break;
            case 'high_priority':
                $query->where('priority', PriorityEnum::High->value);
                break;
            case 'last_week':
                $query->where('issued_date', '>=', now()->subWeek());
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
                'currency' => $this->getMostCommonCurrency($invoice->where('is_favorite', true)),
            ],
            'paid' => [
                'count' => $invoice->where('payment_status', PaymentStatusEnum::Paid->value)->count(),
                'amount' => $invoice->where('payment_status', PaymentStatusEnum::Paid->value)->sum('amount'),
                'currency' => $this->getMostCommonCurrency($invoice->where('payment_status', PaymentStatusEnum::Paid->value)),
            ],
            'unpaid' => [
                'count' => $invoice->where('payment_status', PaymentStatusEnum::Unpaid->value)->count(),
                'amount' => $invoice->where('payment_status', PaymentStatusEnum::Unpaid->value)->sum('amount'),
                'currency' => $this->getMostCommonCurrency($invoice->where('payment_status', PaymentStatusEnum::Unpaid->value)),
            ],
            'late' => [
                'count' => $invoice->where('payment_status', PaymentStatusEnum::Late->value)->count(),
                'amount' => $invoice->where('payment_status', PaymentStatusEnum::Late->value)->sum('amount'),
                'currency' => $this->getMostCommonCurrency($invoice->where('payment_status', PaymentStatusEnum::Late->value)),
            ],
            'high_priority' => [
                'count' => $invoice->where('priority', PriorityEnum::High->value)->count(),
                'amount' => $invoice->where('priority', PriorityEnum::High->value)->sum('amount'),
                'currency' => $this->getMostCommonCurrency($invoice->where('priority', PriorityEnum::High->value)),
            ],
            'last_week' => [
                'count' => $invoice->where('issued_date', '>=', now()->subWeek())->count(),
                'amount' => $invoice->where('issued_date', '>=', now()->subWeek())->sum('amount'),
                'currency' => $this->getMostCommonCurrency($invoice->where('issued_date', '>=', now()->subWeek())),
            ],
        ];
    }

    /**
     * Détermine le symbole de devise la plus utilisée dans le folder de factures
     */
    private function getMostCommonCurrency($invoices): string
    {
        $currencies = $invoices->pluck('currency')->groupBy(function ($currency) {
            return $currency;
        });

        $mostCommonCurrency = $currencies->sortByDesc(function ($group) {
            return $group->count();
        })->keys()->first();

        // get the symbol for the most common currency
        $this->form = (object) [
            'currency' => $mostCommonCurrency ?? 'EUR',
        ];

        return $this->getCurrencySymbol();
    }

    /**
     * Récupère le symbole correct pour une facture donnée
     */
    public function getInvoiceCurrencySymbol($invoice): string
    {
        $this->form = (object) [
            'currency' => $invoice->currency ?? 'EUR',
        ];

        return $this->getCurrencySymbol();
    }

    /**
     * Formate un montant avec le symbole de la devise appropriée
     */
    public function formatAmount($amount, $currency = 'EUR'): string
    {
        $this->form = (object) [
            'currency' => $currency,
        ];

        $symbol = $this->getCurrencySymbol();

        return number_format($amount, 2, ',', ' ').' '.$symbol;
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
            'type' => false,
            'category' => false,
            'issuer_name' => false,
            'amount' => true,
            'issued_date' => true,
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

            return false;
        }

        try {
            // Récupérer le chemin brut du fichier dans S3
            $s3FilePath = $invoiceFile->getRawOriginal('file_path');

            if (! Storage::disk('s3')->exists($s3FilePath)) {
                Toaster::error('Fichier introuvable ou mal enregistré : Veuillez modifier votre facture et importer à nouveau le fichier.');

                return false;
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

            return false;
        }
    }

    public function markAsPaymentStatusSelected(): void
    {
        try {
            if (empty($this->selectedInvoiceIds)) {
                Toaster::error('Aucune facture sélectionnée.');

                return;
            }

            if ($this->selectedPaymentStatus === null) {
                Toaster::error('Veuillez sélectionner un statut de paiement.');

                return;
            }

            $invoices = Invoice::whereIn('id', $this->selectedInvoiceIds)
                ->where('is_archived', false)
                ->get();

            if ($invoices->isEmpty()) {
                Toaster::error('Aucune facture sélectionnée ou déjà archivée.');

                return;
            }

            $count = $invoices->count();
            foreach ($invoices as $invoice) {
                $invoice->payment_status = $this->selectedPaymentStatus;
                $invoice->save();
            }

            $statusEnum = PaymentStatusEnum::from($this->selectedPaymentStatus);
            $statusEmoji = $statusEnum->emoji();
            $statusLabel = $statusEnum->label();

            if ($count > 1) {
                Toaster::success("$count factures marquées comme \"$statusLabel\" avec succès.");
            } else {
                Toaster::success("La facture a été marquée comme \"$statusLabel\" avec succès.");
            }

            $this->selectedInvoiceIds = [];
            $this->selectedPaymentStatus = null;

        } catch (\Exception $e) {
            Toaster::error('Erreur lors de la modification des factures sélectionnées');
            \Log::error('Erreur lors de la modification des factures sélectionnées: '.$e->getMessage());
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
                ->where('user_id', auth()->id())
                ->firstOrFail();

            $this->invoice->update(['is_archived' => true]);

            $this->showFolderModal = false;

            Toaster::success('Facture archivée avec succès !');
        } catch (\Exception $e) {
            Toaster::error('Erreur lors de l\'archivage::Veuillez réessayer.');
        }
    }

    public function archiveSelected(): void
    {
        if (empty($this->selectedInvoiceIds)) {
            Toaster::error('Aucune facture sélectionnée.');

            return;
        }

        try {
            $count = Invoice::whereIn('id', $this->selectedInvoiceIds)
                ->where('is_archived', false)
                ->where('user_id', auth()->id())
                ->update(['is_archived' => true]);

            if ($count > 1) {
                Toaster::success("$count factures archivées avec succès.");
                $this->selectedInvoiceIds = [];
            } elseif ($count == 1) {
                Toaster::success('La facture a été archivée avec succès.');
            } else {
                Toaster::error('Aucune facture n\'a pu être archivée.');
            }
        } catch (\Exception) {
            Toaster::error('Erreur lors de l\'archivage::Veuillez réessayer.');
        }
    }

    public function copyInvoice($invoiceId): void
    {
        try {
            $originalInvoice = auth()->user()->invoices()
                ->with(['file', 'sharedUsers'])
                ->findOrFail($invoiceId);

            $newInvoice = $originalInvoice->replicate();
            $newInvoice->name = $originalInvoice->name.' (version copiée)';
            $newInvoice->is_favorite = false; // Ne pas copier l'état favori
            $newInvoice->created_at = now();
            $newInvoice->updated_at = now();
            $newInvoice->save();

            if ($originalInvoice->file) {
                $originalFile = $originalInvoice->file;
                $newFile = $originalFile->replicate();
                $newFile->invoice_id = $newInvoice->id;
                $newFile->save();
            }

            if ($originalInvoice->tags) {
                $newInvoice->tags = $originalInvoice->tags;
                $newInvoice->save();
            }

            $this->redirectRoute('invoices.edit', $newInvoice->id);

        } catch (\Exception $e) {
            Toaster::error('Erreur lors de la copie de la facture::Veuillez réessayer.');
            \Log::error('Erreur lors de la copie de la facture: '.$e->getMessage());
        }
    }

    public function render()
    {
        // Récupération des factures avec filtres
        $invoices = auth()->user()->invoices()
            ->with(['file', 'sharedUsers'])
            ->when($this->sortField,
                fn ($query) => $query->orderBy($this->sortField, $this->sortDirection)
            )
            ->where('is_archived', false)
            ->paginate(8);

        $this->recentInvoices = auth()->user()->invoices()
            ->with(['file', 'sharedUsers'])
            ->where('is_archived', false)
            ->orderBy('updated_at', 'desc')
            ->limit(8)
            ->get();

        $folderStats = $this->getFolderStats();

        $this->invoiceIdsOnPage = $invoices->map(fn ($invoice) => (string) $invoice->id)->toArray();

        $archivedInvoices = auth()->user()->invoices()
            ->where('is_archived', true)
            ->get();

        return view('livewire.pages.invoices.index', [
            'invoices' => $invoices,
            'folderStats' => $folderStats,
            'paymentStatuses' => PaymentStatusEnum::getStatusOptions(),
            'archivedInvoices' => $archivedInvoices,
        ])->layout('layouts.app-sidebar');
    }
}
