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

    public function label($start = null, $end = null): string
    {
        return match ($this) {
            self::All_Time => 'Toutes les échéances',
            self::Year => 'Cette année',
            self::This_Month => 'Ce mois-ci',
            self::This_Week => 'Cette semaine',
            self::Today => 'Aujourd\'hui',
            self::Next_7 => 'Prochains 7 jours',
            self::Next_30 => 'Prochains 30 jours',
            self::Future => 'Échéances futures',
            self::Custom => ($start !== null && $end !== null)
                ? str($start)->replace('-', '/').' - '.str($end)->replace('-', '/')
                : 'Période personnalisée',
        };
    }

    public function dates(): array
    {
        return match ($this) {
            self::Year => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            self::This_Month => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            self::This_Week => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            self::Today => [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()],
            self::Next_7 => [Carbon::now()->addDays(1)->startOfDay(), Carbon::now()->addDays(7)->endOfDay()],
            self::Next_30 => [Carbon::now()->addDays(1)->startOfDay(), Carbon::now()->addDays(30)->endOfDay()],
            self::Future => [Carbon::now()->addDays(1)->startOfDay(), Carbon::now()->addYears(100)->endOfDay()],
            default => [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()],
        };
    }
}
