<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

// Command to send a test email
Artisan::command('mail:test {email}', function ($email) {
    \Mail::raw('Test from FamilyNest', function ($message) use ($email) {
        $message->to($email)->subject('Test Email');
    });
    $this->info("Nickel l'email à été envoyé à {$email}");
});
