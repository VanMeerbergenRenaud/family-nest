<?php

namespace App\Livewire\Pages\Goals;

enum GoalStatusEnum: string
{
    case All = 'all';
    case Inactive = 'inactive';
    case Active = 'active';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::All => 'Tous les statuts',
            self::Inactive => 'Inactif',
            self::Active => 'En cours',
            self::Completed => 'Terminé',
        };
    }

    public static function forFilter(): array
    {
        return [
            self::All->value => self::All->label(),
            self::Inactive->value => self::Inactive->label(),
            self::Active->value => self::Active->label(),
            self::Completed->value => self::Completed->label(),
        ];
    }

    public function applyQuery($query): void
    {
        match ($this) {
            self::All => null,
            self::Inactive => $query->where('is_active', false),
            self::Active => $query->where('is_active', true)->where('is_completed', false),
            self::Completed => $query->where('is_completed', true),
        };
    }
}
