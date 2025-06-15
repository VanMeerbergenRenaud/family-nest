<?php

namespace App\Livewire\Pages\Goals;

enum GoalOwnerEnum: string
{
    case Family = 'family';
    case Personal = 'personal';

    public function label(): string
    {
        return match ($this) {
            self::Family => 'Familiaux',
            self::Personal => 'Personnels',
        };
    }

    public static function forFilter(): array
    {
        return [
            self::Family->value => self::Family->label(),
            self::Personal->value => self::Personal->label(),
        ];
    }

    public function applyQuery($query, $userId, $familyId): void
    {
        match ($this) {
            self::Family => $query->where('family_id', $familyId)
                ->where('is_family_goal', true),
            self::Personal => $query->where('user_id', $userId)
                ->where('is_family_goal', false),
        };
    }
}
