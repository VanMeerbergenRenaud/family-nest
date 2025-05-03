<?php

namespace App\Livewire\Pages\Dashboard;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Form;

class Filters extends Form
{
    public User $user;

    #[Url]
    public $status = 'all';

    #[Url]
    public $family_member = 'all';

    #[Url]
    public Range $range = Range::All_Time;

    #[Url]
    public $start;

    #[Url]
    public $end;

    public function init(User $user): void
    {
        $this->user = $user;
    }

    public function getStatusEnum(): FilterStatus
    {
        return FilterStatus::from($this->status);
    }

    #[Computed]
    public function statuses(): Collection
    {
        return collect(FilterStatus::cases())->map(function ($status) {
            $count = $this->getBaseQuery()
                ->when($status !== FilterStatus::All, fn ($q) => $q->where('payment_status', $status->value))
                ->count();

            return [
                'value' => $status->value,
                'label' => $status->label(),
                'count' => $count,
            ];
        });
    }

    #[Computed]
    public function familyMembers(): Collection
    {
        $family = $this->user->family();

        if (! $family) {
            return collect([]);
        }

        $baseQuery = Invoice::where('family_id', $family->id)
            ->with(['file', 'sharedUsers'])
            ->where('is_archived', false);

        $primaryMember = collect([
            [
                'id' => 'all',
                'name' => 'Tous les membres',
                'invoice_count' => $this->applyStatus($baseQuery)->count(),
            ],
            [
                'id' => $this->user->id,
                'name' => $this->user->name.' (Vous)',
                'invoice_count' => $this->applyStatus($baseQuery)
                    ->where('user_id', $this->user->id)
                    ->count(),
            ],
        ]);

        $otherMembers = $family->users()
            ->where('user_id', '!=', $this->user->id)
            ->get()
            ->map(fn ($member) => [
                'id' => $member->id,
                'name' => $member->name,
                'invoice_count' => $this->applyStatus($member->invoices()
                    ->where('is_archived', false))
                    ->count(),
            ]);

        return $primaryMember->concat($otherMembers);
    }

    public function apply($query)
    {
        return $this->applyRange(
            $this->applyStatus(
                $this->applyFamilyMember($query)
            )
        );
    }

    public function applyStatus($query, $status = null)
    {
        $statusEnum = $status ?? $this->getStatusEnum();

        return $statusEnum === FilterStatus::All
            ? $query
            : $query->where('payment_status', $statusEnum->value);
    }

    public function applyFamilyMember($query)
    {
        if ($this->family_member === 'all') {
            $family = $this->user->family();

            return $family
                ? $query->where('family_id', $family->id)
                : $query;
        }

        return $query->where('user_id', $this->family_member);
    }

    public function applyRange($query)
    {
        if ($this->range === Range::All_Time) {
            return $query;
        }

        if ($this->range === Range::Custom && $this->start && $this->end) {
            $start = Carbon::createFromFormat('Y-m-d', $this->start)->startOfDay();
            $end = Carbon::createFromFormat('Y-m-d', $this->end)->endOfDay();

            return $query->whereBetween('payment_due_date', [$start, $end]);
        }

        return $this->range === Range::Custom
            ? $query
            : $query->whereBetween('payment_due_date', $this->range->dates());
    }

    public function getBaseQuery()
    {
        $user = $this->user;
        $family = $user->family();

        // Query de base pour les factures non archivées
        $query = auth()->user()->accessibleInvoices()
            ->where('is_archived', false)
            ->with(['file', 'sharedUsers']);

        // Si on filtre par membre spécifique
        if ($this->family_member !== 'all') {
            return $query->where('user_id', $this->family_member);
        }

        // Sinon, si c'est "tous les membres" et qu'on a une famille
        if ($family) {
            // Pour tous les utilisateurs, on affiche toutes les factures de la famille
            return $query->where('family_id', $family->id);
        }

        // Si pas de famille, on montre seulement ses propres factures
        return $query->where('user_id', $user->id);
    }

    public function resetAllFilters(): void
    {
        $this->status = 'all';
        $this->family_member = 'all';
        $this->range = Range::All_Time;
        $this->start = null;
        $this->end = null;
    }
}
