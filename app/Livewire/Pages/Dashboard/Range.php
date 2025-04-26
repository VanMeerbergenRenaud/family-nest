<?php

namespace App\Livewire\Pages\Dashboard;

use Illuminate\Support\Carbon;

enum Range: string
{
    case All_Time = 'all';
    case Year = 'year';
    case Last_30 = 'last30';
    case Last_7 = 'last7';
    case Today = 'today';
    case Custom = 'custom';

    public function label($start = null, $end = null)
    {
        return match ($this) {
            static::All_Time => 'Tous les temps',
            static::Year => 'Cette année',
            static::Last_30 => 'Les 30 derniers jours',
            static::Last_7 => 'Les 7 derniers jours',
            static::Today => 'Aujourd\'hui',
            static::Custom => ($start !== null && $end !== null)
                ? str($start)->replace('-', '/') . ' - ' . str($end)->replace('-', '/')
                : 'Custom Range',
        };
    }

    public function dates()
    {
        return match ($this) {
            static::Today => [Carbon::today(), now()],
            static::Last_7 => [Carbon::today()->subDays(6), now()],
            static::Last_30 => [Carbon::today()->subDays(29), now()],
            static::Year => [Carbon::now()->startOfYear(), now()],
        };
    }
}
