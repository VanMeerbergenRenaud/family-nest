<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body
        x-data="{ expanded: localStorage.getItem('sidebarExpanded') !== 'false' }"
        @sidebar-toggled.window="expanded = $event.detail.expanded"
    >
        <livewire:sidebar />

        <main class="flex-1 p-4 lg:px-6"
              :class="{'lg:ml-64': expanded, 'lg:ml-20': !expanded}"
        >
            <livewire:spotlight /> {{-- Spotlight search --}}

            <livewire:breadcrumb /> {{-- Breadcrumb --}}

            {{ $slot }}
        </main>

        @livewireScripts
    </body>
</html>
