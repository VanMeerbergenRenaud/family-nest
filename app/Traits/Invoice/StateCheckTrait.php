<?php

namespace App\Traits\Invoice;

use App\Models\Family;
use App\Models\Invoice;

trait StateCheckTrait
{
    protected $familyMemberIds = null;

    protected $hasFamily = null;

    protected $userFamily = null;

    // Optimisation pour éviter les requêtes répétées
    public function hasFamily(): bool
    {
        if ($this->hasFamily === null) {
            $this->hasFamily = auth()->user()->hasFamily();
        }

        return $this->hasFamily;
    }

    public function getUserFamily(): ?Family
    {
        if ($this->userFamily === null && $this->hasFamily()) {
            $this->userFamily = auth()->user()->family();
        }

        return $this->userFamily;
    }

    public function getFamilyMemberIds(): array
    {
        if ($this->familyMemberIds === null) {
            if (! $this->hasFamily()) {
                $this->familyMemberIds = [auth()->id()];
            } else {
                $family = $this->getUserFamily();
                $this->familyMemberIds = $family->users()
                    ->pluck('users.id')
                    ->toArray();
            }
        }

        return $this->familyMemberIds;
    }

    // Requête combinée pour vérifier l'existence des factures
    public function getInvoiceStats(): array
    {
        $memberIds = $this->getFamilyMemberIds();

        // Requête unique qui compte les factures actives et archivées
        $stats = Invoice::selectRaw('
            SUM(CASE WHEN is_archived = false THEN 1 ELSE 0 END) as active_count,
            SUM(CASE WHEN is_archived = true THEN 1 ELSE 0 END) as archived_count
        ')
            ->whereIn('user_id', $memberIds)
            ->first();

        return [
            'has_active' => $stats->active_count > 0,
            'has_archived' => $stats->archived_count > 0,
        ];
    }

    // Les méthodes suivantes utilisent les données du getInvoiceStats
    public function hasFamilyActiveInvoices(): bool
    {
        $user = auth()->user();

        if (! $user->hasFamily()) {
            return $user->invoices()->where('is_archived', false)->exists();
        }

        $familyIds = $user->families()->pluck('families.id')->toArray();

        return Invoice::where(function ($query) use ($user, $familyIds) {
            $query->where('user_id', $user->id)
                ->orWhereIn('family_id', $familyIds);
        })->where('is_archived', false)->exists();
    }

    public function hasFamilyArchivedInvoices(): bool
    {
        $user = auth()->user();

        if (! $user->hasFamily()) {
            return $user->invoices()->where('is_archived', true)->exists();
        }

        $familyIds = $user->families()->pluck('families.id')->toArray();

        return Invoice::where(function ($query) use ($user, $familyIds) {
            $query->where('user_id', $user->id)
                ->orWhereIn('family_id', $familyIds);
        })->where('is_archived', true)->exists();
    }

    public function hasOnlyArchivedInvoices(): bool
    {
        $stats = $this->getInvoiceStats();

        return $stats['has_archived'] && ! $stats['has_active'];
    }

    // Pour l'utilisateur connecté
    public function userHasInvoices(): bool
    {
        return Invoice::where('user_id', auth()->id())
            ->where('is_archived', false)
            ->exists();
    }

    public function userHasArchivedInvoices(): bool
    {
        return Invoice::where('user_id', auth()->id())
            ->where('is_archived', true)
            ->exists();
    }

    public function getDashboardState(): string
    {
        if (! $this->hasFamily()) {
            return 'no_family';
        }

        $stats = $this->getInvoiceStats();

        if (! $stats['has_active'] && ! $stats['has_archived']) {
            return 'no_invoices';
        }

        if ($stats['has_archived'] && ! $stats['has_active']) {
            return 'only_archived_invoices';
        }

        return 'has_active_invoices';
    }
}
