<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body>
        <header>
            <livewire:breadcrumb /> {{-- Breadcrumb --}}
        </header>

        {{-- MAIN --}}
        <main class="p-4">
            <livewire:spotlight /> {{-- Spotlight search --}}

            {{ $slot }}
        </main>

        @livewireScripts
    </body>
</html>
