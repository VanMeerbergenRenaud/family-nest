<?php

namespace App\Livewire\Pages\Goals;

enum GoalTypeEnum: string
{
    case All = 'all';
    case Reach = 'reach';
    case NotExceed = 'not_exceed';

    public function label(): string
    {
        return match ($this) {
            self::All => 'Tous les types',
            self::Reach => 'Atteindre',
            self::NotExceed => 'Ne pas dépasser',
        };
    }

    public static function forFilter(): array
    {
        return [
            self::All->value => self::All->label(),
            self::Reach->value => self::Reach->label(),
            self::NotExceed->value => self::NotExceed->label(),
        ];
    }

    public function color(): string
    {
        return match ($this) {
            self::All => 'gray',
            self::Reach => 'emerald',
            self::NotExceed => 'amber',
        };
    }
}
