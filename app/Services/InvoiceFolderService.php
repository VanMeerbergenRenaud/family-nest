<?php

namespace App\Services;

use App\Enums\PaymentStatusEnum;
use App\Enums\PriorityEnum;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Collection;

class InvoiceFolderService
{
    /**
     * Récupérer les statistiques pour chaque dossier de factures
     *
     * @param  Collection  $invoices  Invoices de l'utilisateur
     */
    public function getFolderStats(Collection $invoices): array
    {
        $activeInvoices = $invoices->where('is_archived', false);

        return [
            'favorites' => $this->calculateFolderStats($activeInvoices->where('is_favorite', true)),
            'paid' => $this->calculateFolderStats($activeInvoices->where('payment_status', PaymentStatusEnum::Paid->value)),
            'unpaid' => $this->calculateFolderStats($activeInvoices->where('payment_status', PaymentStatusEnum::Unpaid->value)),
            'late' => $this->calculateFolderStats($activeInvoices->where('payment_status', PaymentStatusEnum::Late->value)),
            'high_priority' => $this->calculateFolderStats($activeInvoices->where('priority', PriorityEnum::High->value)),
            'last_week' => $this->calculateFolderStats($activeInvoices->where('issued_date', '>=', now()->subWeek())),
        ];
    }

    /**
     * Récupérer les factures pour un dossier spécifique
     *
     * @param  string  $folder  Identifiant du dossier
     * @param  int  $userId  ID de l'utilisateur
     * @param  string  $sortField  Champ de tri
     * @param  string  $sortDirection  Direction du tri
     */
    public function getFolderInvoices(string $folder, int $userId, string $sortField = 'name', string $sortDirection = 'desc'): Collection
    {
        $query = Invoice::where('user_id', $userId)
            ->with('file')
            ->where('is_archived', false);

        // Définir la requête en fonction du dossier sélectionné
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
                return collect();
        }

        return $query->orderBy($sortField, $sortDirection)->get();
    }

    /**
     * Calculer les statistiques pour un dossier
     *
     * @param  Collection  $invoices
     */
    private function calculateFolderStats($invoices): array
    {
        return [
            'count' => $invoices->count(),
            'amount' => $invoices->sum('amount'),
            'currency' => $this->getMostCommonCurrency($invoices),
        ];
    }

    /**
     * Obtenir la devise la plus commune
     *
     * @param  Collection  $invoices
     */
    private function getMostCommonCurrency($invoices): string
    {
        $currencies = $invoices->pluck('currency')->groupBy(function ($currency) {
            return $currency;
        });

        return $currencies->sortByDesc(function ($group) {
            return $group->count();
        })->keys()->first() ?? 'EUR';
    }
}
