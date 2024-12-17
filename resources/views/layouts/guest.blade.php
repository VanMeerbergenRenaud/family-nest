<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FamilyNest') }}</title>

        <!-- CDN -->
        <script defer src="https://unpkg.com/@alpinejs/ui@3.13.1-beta.0/dist/cdn.min.js"></script>

        <!-- Scripts -->
        @livewireStyles
        @vite(['resources/css/app.scss', 'resources/js/app.js'])
    </head>
    <body class="mx-auto p-0 min-h-screen max-w-[160rem] text-base font-normal text-black bg-gray-50 scroll-smooth antialiased">
        <header>
            <a href="{{ route('welcome') }}" title="Retour à l'accueil" wire:navigate>
                <x-app-logo />
            </a>
        </header>

        <main class="min-h-[100vh]">
            {{ $slot }}
        </main>

        <footer class="w-full bg-gray-100 p-4 text-center">
            Footer
        </footer>
    </body>
</html>
