<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome')
    ->middleware(['guest'])
    ->name('welcome');

Volt::route('dashboard', 'pages.dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Volt::route('profile', 'pages.profile')
    ->middleware(['auth'])
    ->name('profile');

Volt::route('invoices', 'pages.invoice-manager')
    ->middleware(['auth'])
    ->name('invoices');

Volt::route('invoices.create', 'pages.create-invoice')
    ->middleware(['auth'])
    ->name('invoices.create');

require __DIR__.'/auth.php';
