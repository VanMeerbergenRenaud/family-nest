<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body>
        <header>
            <div class="flex items-center justify-between px-4">
                <a href="{{ route('dashboard') }}" class="button-classic w-fit text-sm-semibold">
                    <x-svg.arrows.left class="text-gray-900" />
                    Retour
                </a>

                <livewire:breadcrumb /> {{-- Breadcrumb --}}

                <x-theme-switcher /> {{-- Theme switcher --}}
            </div>

            <x-divider class="mb-4" />
        </header>

        {{-- MAIN --}}
        <main class="p-4">

            <livewire:spotlight /> {{-- Spotlight search --}}

            {{ $slot }}
        </main>

        @livewireScripts
    </body>
</html>
