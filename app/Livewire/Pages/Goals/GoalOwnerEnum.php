<?php

namespace App\Livewire\Pages\Goals;

enum GoalOwnerEnum: string
{
    case All = 'all';
    case Personal = 'personal';

    public function label(): string
    {
        return match ($this) {
            self::All => 'Tous',
            self::Personal => 'Personnels',
        };
    }

    public static function forFilter(): array
    {
        return [
            self::All->value => self::All->label(),
            self::Personal->value => self::Personal->label(),
        ];
    }

    public function applyQuery($query, $userId, $familyId): void
    {
        match ($this) {
            self::All => $query->where('family_id', $familyId),
            self::Personal => $query->where('user_id', $userId),
        };
    }
}
