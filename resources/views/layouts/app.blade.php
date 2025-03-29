<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body>
        <header>
            {{-- Slot pour bannière mode édition --}}
            {{ $banner ?? null }}

            <div class="relative flex-center px-4">
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
