<?php

namespace App\Traits;

use App\Models\Family;
use App\Models\Invoice;

/**
 * Ce trait gère les vérifications d'état des factures et des familles
 */
trait InvoiceStateCheckTrait
{
    // Vérifie si l'utilisateur a une famille associée
    public function hasFamily(): bool
    {
        return auth()->user()->hasFamily();
    }

    // Obtient la famille de l'utilisateur connecté
    public function getUserFamily(): ?Family
    {
        return auth()->user()->family();
    }

    // Récupère les IDs des membres de la famille de l'utilisateur
    public function getFamilyMemberIds(): array
    {
        if (! $this->hasFamily()) {
            return [auth()->id()];
        }

        $family = $this->getUserFamily();

        return $family->users()
            ->select('users.id')
            ->pluck('id')
            ->toArray();
    }

    // Vérifie si l'utilisateur ou sa famille a des factures actives
    public function hasFamilyActiveInvoices(): bool
    {
        if (! $this->hasFamily()) {
            // Si l'utilisateur n'a pas de famille, vérifier seulement ses factures
            return Invoice::where('user_id', auth()->id())
                ->where('is_archived', false)
                ->exists();
        }

        // Si l'utilisateur a une famille, vérifier les factures de tous les membres
        $familyMemberIds = $this->getFamilyMemberIds();

        return Invoice::whereIn('user_id', $familyMemberIds)
            ->where('is_archived', false)
            ->exists();
    }

    // Vérifie si l'utilisateur ou sa famille a des factures archivées
    public function hasFamilyArchivedInvoices(): bool
    {
        if (! $this->hasFamily()) {
            // Si l'utilisateur n'a pas de famille, vérifier seulement ses factures
            return Invoice::where('user_id', auth()->id())
                ->where('is_archived', true)
                ->exists();
        }

        // Si l'utilisateur a une famille, vérifier les factures de tous les membres
        $familyMemberIds = $this->getFamilyMemberIds();

        return Invoice::whereIn('user_id', $familyMemberIds)
            ->where('is_archived', true)
            ->exists();
    }

    // Vérifie si l'utilisateur ou sa famille possède uniquement des factures archivées
    public function hasOnlyArchivedInvoices(): bool
    {
        // Vérifier s'il y a des factures archivées mais pas de factures actives
        return $this->hasFamilyArchivedInvoices() && ! $this->hasFamilyActiveInvoices();
    }

    // Vérifie si le user connecté possède des factures archivées
    public function hasArchivedInvoices(): bool
    {
        return Invoice::where('user_id', auth()->id())
            ->where('is_archived', true)
            ->exists();
    }

    /**
     * Détermine l'état actuel du tableau de bord pour l'utilisateur
     *
     * @return string L'état actuel:
     *                - 'no_family': L'utilisateur n'a pas de famille
     *                - 'no_invoices': L'utilisateur et/ou sa famille n'ont aucune facture
     *                - 'only_archived_invoices': L'utilisateur et/ou sa famille n'ont que des factures archivées
     *                - 'has_active_invoices': L'utilisateur et/ou sa famille ont des factures actives
     */
    public function getDashboardState(): string
    {
        // Cas 1: L'utilisateur n'a pas de famille
        if (! $this->hasFamily()) {
            return 'no_family';
        }

        // Cas 2: La famille n'a aucune facture (ni active ni archivée)
        if (! $this->hasFamilyActiveInvoices() && ! $this->hasFamilyArchivedInvoices()) {
            return 'no_invoices';
        }

        // Cas 3: La famille a uniquement des factures archivées
        if ($this->hasOnlyArchivedInvoices()) {
            return 'only_archived_invoices';
        }

        // Cas 4: La famille a des factures actives
        return 'has_active_invoices';
    }
}
