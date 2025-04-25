<?php

namespace App\Traits;

use Carbon\Carbon;

trait HumanDateTrait
{
    public function dateForHumans($date): string
    {
        if (! $date) {
            return 'Non spécifiée';
        }

        return Carbon::parse($date)
            ->locale('fr_FR')
            ->isoFormat('D MMM YYYY');
    }
}
