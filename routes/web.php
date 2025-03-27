<?php

use App\Livewire\Pages\Calendar;
use App\Livewire\Pages\Dashboard;
use App\Livewire\Pages\Family;
use App\Livewire\Pages\Goals;
use App\Livewire\Pages\HelpCenter;
use App\Livewire\Pages\Invoices\Archived;
use App\Livewire\Pages\Invoices\Create;
use App\Livewire\Pages\Invoices\Edit;
use App\Livewire\Pages\Invoices\Index as IndexInvoice;
use App\Livewire\Pages\Invoices\Show;
use App\Livewire\Pages\Settings\Apparence;
use App\Livewire\Pages\Settings\Billing;
use App\Livewire\Pages\Settings\Danger;
use App\Livewire\Pages\Settings\Index as IndexSetting;
use App\Livewire\Pages\Settings\Notification;
use App\Livewire\Pages\Settings\Profile;
use App\Livewire\Pages\Settings\Storage;
use App\Livewire\Pages\Themes;
use Illuminate\Support\Facades\Route;

// Route for the welcome page
Route::view('/', 'welcome')
    ->middleware(['guest'])
    ->name('welcome');

// Routes order => desktop sidebar
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', IndexInvoice::class)->name('index');
        Route::get('/create', Create::class)->name('create');
        Route::get('/{id}/edit', Edit::class)->name('edit');
        Route::get('/{id}/show', Show::class)->name('show');
        Route::get('/archived', Archived::class)->name('archived');
        Route::get('/download/{id}', [IndexInvoice::class, 'downloadInvoice'])->name('download');
    });

    Route::get('/themes', Themes::class)->name('themes');
    Route::get('/calendar', Calendar::class)->name('calendar');
    Route::get('/goals', Goals::class)->name('goals');
    Route::get('/family', Family::class)->name('family');

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', IndexSetting::class)->name('index');
        Route::get('/profile', Profile::class)->name('profile');
        Route::get('/storage', Storage::class)->name('storage');
        Route::get('/notification', Notification::class)->name('notifications');
        Route::get('/billing', Billing::class)->name('billing');
        Route::get('/apparence', Apparence::class)->name('appearance');
        Route::get('/danger', Danger::class)->name('danger');
    });

    Route::get('/help-center', HelpCenter::class)->name('help-center');
});

// Route for the auth routes
require __DIR__.'/auth.php';
