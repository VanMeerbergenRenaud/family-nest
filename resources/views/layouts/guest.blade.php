<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FamilyNest') }}</title>

        <!-- Scripts -->
        @livewireStyles
        @vite(['resources/css/app.scss', 'resources/js/app.js'])
    </head>
    <body>
        <a href="{{ route('welcome') }}" title="Retour à l'accueil" wire:navigate>
            <x-app-logo />
        </a>

        {{ $slot }}
    </body>
</html>
