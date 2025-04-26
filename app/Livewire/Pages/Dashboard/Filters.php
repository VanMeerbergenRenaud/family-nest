<?php

namespace App\Livewire\Pages\Dashboard;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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

    public function init($user): void
    {
        $this->user = $user;
    }

    public function getStatusEnum(): FilterStatus
    {
        return FilterStatus::from($this->status);
    }

    public function statuses(): Collection
    {
        return collect(FilterStatus::cases())->map(function ($status) {
            $baseQuery = $this->getBaseQueryForCurrentMember();

            $count = $this->applyRange(
                $this->applyStatus($baseQuery, $status,)
            )->count();

            return [
                'value' => $status->value,
                'label' => $status->label(),
                'count' => $count,
            ];
        });
    }

    private function getBaseQueryForCurrentMember()
    {
        $user = $this->user;

        if ($this->family_member === 'all') {
            $family = $user->family();
            if ($family) {
                return Invoice::where('family_id', $family->id);
            }

            return $user->invoices();
        } else {
            if ($this->family_member == $user->id) {
                return $user->invoices();
            } else {
                return Invoice::where('user_id', $this->family_member);
            }
        }
    }

    public function familyMembers(): Collection
    {
        $family = $this->user->family();

        if (! $family) {
            return collect([]);
        }

        $allFamilyInvoices = Invoice::where('family_id', $family->id);

        $primaryMember = collect([
            [
                'id' => 'all',
                'name' => 'Tous les membres',
                'invoice_count' => $this->applyStatus($allFamilyInvoices)->count(),
            ],
            [
                'id' => $this->user->id,
                'name' => $this->user->name.' (Vous)',
                'invoice_count' => $this->applyStatus($this->user->invoices())->count(),
            ],
        ]);

        $otherMembers = $family->users()
            ->where('user_id', '!=', $this->user->id)
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->name,
                    'invoice_count' => $this->applyStatus(
                        $member->invoices()
                    )->count(),
                ];
            });

        return $primaryMember->concat($otherMembers);
    }

    public function apply($query)
    {
        $query = $this->applyStatus($query);
        $query = $this->applyFamilyMember($query);
        $query = $this->applyRange($query);

        return $query;
    }

    public function applyStatus($query, $status = null)
    {
        $statusEnum = $status ?? $this->getStatusEnum();

        if ($statusEnum === FilterStatus::All) {
            return $query;
        }

        return $query->where('payment_status', $statusEnum->value);
    }

    public function applyFamilyMember($query)
    {
        if ($this->family_member === 'all') {
            $family = $this->user->family();
            if (! $family) {
                return $query;
            }

            return $query->where('family_id', $family->id);
        }

        return $query->where('user_id', $this->family_member);
    }

    public function applyRange($query)
    {
        if ($this->range === Range::All_Time) {
            return $query;
        }

        if ($this->range === Range::Custom) {
            if ($this->start && $this->end) {
                $start = Carbon::createFromFormat('Y-m-d', $this->start)->startOfDay();
                $end = Carbon::createFromFormat('Y-m-d', $this->end)->endOfDay();

                return $query->whereBetween('payment_due_date', [$start, $end]);
            }
            return $query;
        }

        $dates = $this->range->dates();
        return $query->whereBetween('payment_due_date', $dates);
    }

    public function resetRange(): void
    {
        $this->range = Range::All_Time;
        $this->start = null;
        $this->end = null;

        $this->dispatch('rangeChanged');
        $this->dispatch('filtersUpdated');
        $this->dispatch('refresh-dashboard-components');
    }

    public function updated($property): void
    {
        if ($property === 'status') {
            $this->dispatch('statusChanged');
            $this->dispatch('filtersUpdated');
        } elseif ($property === 'family_member') {
            $this->dispatch('familyMemberChanged');
            $this->dispatch('filtersUpdated');
        } elseif ($property === 'range' || $property === 'start' || $property === 'end') {
            $this->dispatch('rangeChanged');
            $this->dispatch('filtersUpdated');
            // Force parent to re-render the child components
            $this->dispatch('refresh-dashboard-components');
        }
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
