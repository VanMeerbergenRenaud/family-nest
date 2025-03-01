<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome')
    ->middleware(['guest'])
    ->name('welcome');

Volt::route('dashboard', 'pages.dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Volt::route('settings/profile', 'pages.profile')
    ->middleware(['auth'])
    ->name('profile');

Volt::route('invoices', 'pages.invoice-manager')
    ->middleware(['auth'])
    ->name('invoices');

Volt::route('invoices.create', 'pages.create-invoice')
    ->middleware(['auth'])
    ->name('invoices.create');

/* Routes themes, calendar, archives, goals, family */
Volt::route('themes', 'pages.themes')
    ->middleware(['auth'])
    ->name('themes');

Volt::route('calendar', 'pages.calendar')
    ->middleware(['auth'])
    ->name('calendar');

Volt::route('archives', 'pages.archives')
    ->middleware(['auth'])
    ->name('archives');

Volt::route('goals', 'pages.goals')
    ->middleware(['auth'])
    ->name('goals');

Volt::route('family', 'pages.family')
    ->middleware(['auth'])
    ->name('family');

/* Routes settings, help-center */
Volt::route('settings', 'pages.settings')
    ->middleware(['auth'])
    ->name('settings');

Volt::route('help-center', 'pages.help-center')
    ->middleware(['auth'])
    ->name('help-center');

require __DIR__.'/auth.php';
