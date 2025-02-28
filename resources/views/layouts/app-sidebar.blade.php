<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body
        x-data="{ expanded: true }"
        @sidebar-toggled.window="expanded = $event.detail.expanded"
    >
        <livewire:sidebar />

        {{-- MAIN --}}
        <main class="flex-1 p-4"
              :class="{'lg:ml-64': expanded, 'lg:ml-20': !expanded}"
        >
            <livewire:spotlight /> {{-- Spotlight search --}}

            <livewire:breadcrumb /> {{-- Breadcrumb --}}

            {{ $slot }}
        </main>

        @livewireScripts
    </body>
</html>
