<?php

namespace App\Livewire\Pages\Dashboard;

use Illuminate\Support\Carbon;

enum Range: string
{
    case All_Time = 'all';
    case Year = 'year';
    case This_Month = 'this_month';
    case This_Week = 'this_week';
    case Today = 'today';
    case Next_7 = 'next_7';
    case Next_30 = 'next_30';
    case Future = 'future';
    case Custom = 'custom';

    public function label($start = null, $end = null)
    {
        return match ($this) {
            static::All_Time => 'Toutes les échéances',
            // Basique
            static::Year => 'Cette année',
            static::This_Month => 'Ce mois-ci',
            static::This_Week => 'Cette semaine',
            static::Today => 'Aujourd\'hui',
            // Prochainement
            static::Next_7 => 'Prochains 7 jours',
            static::Next_30 => 'Prochains 30 jours',
            // À venir
            static::Future => 'Échéances futures',
            // Custom
            static::Custom => ($start !== null && $end !== null)
                ? str($start)->replace('-', '/') . ' - ' . str($end)->replace('-', '/')
                : 'Période personnalisée',
        };
    }

    public function dates()
    {
        return match ($this) {
            static::Year => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            static::Today => [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()],
            static::This_Week => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            static::This_Month => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            static::Next_7 => [Carbon::now()->addDays(1)->startOfDay(), Carbon::now()->addDays(7)->endOfDay()],
            static::Next_30 => [Carbon::now()->addDays(1)->startOfDay(), Carbon::now()->addDays(30)->endOfDay()],
            static::Future => [Carbon::now()->addDays(1)->startOfDay(), Carbon::now()->addYears(100)->endOfDay()],
        };
    }
}
