<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Dans routes/console.php
Artisan::command('mail:test {email}', function ($email) {
    \Mail::raw('Test from FamilyNest', function ($message) use ($email) {
        $message->to($email)->subject('Test Email');
    });
    $this->info("Nickel l'email à été envoyé à {$email}");
});
