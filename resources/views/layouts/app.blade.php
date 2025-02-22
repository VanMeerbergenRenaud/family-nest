<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FamilyNest') }}</title>

        <!-- Scripts -->
        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="mx-auto relative h-screen bg-gray-50 dark:bg-gray-900 p-0 min-h-screen max-w-[160rem] text-base font-normal text-black dark:text-white scroll-smooth antialiased">
            <x-sidebar-menu />

            <x-theme-switcher class="absolute top-4 right-4" />

            <main class="flex-1 p-4 lg:ml-64">
                {{ $slot }}
            </main>
    </body>
</html>
