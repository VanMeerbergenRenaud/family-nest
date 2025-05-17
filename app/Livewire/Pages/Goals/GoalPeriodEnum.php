<?php

namespace App\Livewire\Pages\Goals;

enum GoalPeriodEnum: string
{
    case All = 'all';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
    case Quarterly = 'quarterly';
    case Yearly = 'yearly';
    case Custom = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::All => 'Toutes les périodes',
            self::Weekly => 'Hebdomadaire',
            self::Monthly => 'Mensuel',
            self::Quarterly => 'Trimestriel',
            self::Yearly => 'Annuel',
            self::Custom => 'Unique',
        };
    }

    public static function forFilter(): array
    {
        return [
            self::All->value => self::All->label(),
            self::Weekly->value => self::Weekly->label(),
            self::Monthly->value => self::Monthly->label(),
            self::Quarterly->value => self::Quarterly->label(),
            self::Yearly->value => self::Yearly->label(),
            self::Custom->value => self::Custom->label(),
        ];
    }
}
