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

require __DIR__.'/auth.php';
