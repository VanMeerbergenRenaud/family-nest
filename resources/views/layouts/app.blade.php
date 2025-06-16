<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body>
        @include('partials.no-js')

        <header>
            <h1 role="heading" aria-level="1" class="sr-only">{{ $title ?? 'Titre par défaut' }}</h1>

            {{ $banner ?? null }}

            <div class="relative flex-center pb-1 pt-2">
                <a href="{{ url()->previous() }}" class="max-md:hidden absolute top-auto left-4 button-classic w-fit text-sm-semibold">
                    <x-svg.arrows.left class="text-gray-900" />
                    Retour
                </a>

                <livewire:breadcrumb /> {{-- Breadcrumb --}}
            </div>

            <x-divider class="mb-4" />
        </header>

        <x-toaster-hub />

        {{-- MAIN --}}
        <main class="p-4">
            <livewire:spotlight /> {{-- Spotlight search --}}

            {{ $slot }}
        </main>

        @livewireScripts
    </body>
</html>
