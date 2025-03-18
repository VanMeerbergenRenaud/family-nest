<?php

use App\Livewire\Pages\Invoices\Archived;
use App\Livewire\Pages\Invoices\Create;
use App\Livewire\Pages\Invoices\Edit;
use App\Livewire\Pages\Invoices\Index;
use App\Livewire\Pages\Invoices\Show;
use App\Livewire\Pages\Themes;
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

/* Routes invoices */
Route::middleware(['auth'])->group(function () {
    Route::get('/invoices', Index::class)->name('invoices');
    Route::get('/invoices/create', Create::class)->name('invoices.create');
    Route::get('/invoices/{id}/edit', Edit::class)->name('invoices.edit');
    Route::get('/invoices/{id}/show', Show::class)->name('invoices.show');
    Route::get('/invoices/archived', Archived::class)->name('invoices.archived');
    // Themes
    Route::get('/themes', Themes::class)->name('themes');
});

/* Routes themes, calendar, archives, goals, family */
Volt::route('calendar', 'pages.calendar')
    ->middleware(['auth'])
    ->name('calendar');

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
