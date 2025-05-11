<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body>
        <x-toaster-hub />

        <main class="flex-1 flex-center flex-col">
            <div class="flex items-center p-4">
                <h1 role="heading" aria-level="1" class="sr-only">{{ $title ?? 'Interface d\'accueil' }}</h1>
                <x-app.logo class="w-8 h-8"/>
                <span class="ml-4 text-xl-bold">{{ config('app.name') }}</span>
            </div>

            {{ $slot }}
        </main>

        @livewireScripts
    </body>
</html>
