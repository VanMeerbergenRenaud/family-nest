<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body>
        <header>
            <h1 role="heading" aria-level="1" class="sr-only">{{ $title ?? 'Titre par défaut' }}</h1>

            {{ $banner ?? null }}
        </header>

        <livewire:sidebar />

        <x-toaster-hub />

        <main
            x-data
            class="flex-1 max-lg:!ml-0 transition-all duration-300"
            @sidebar-toggled.window="$el.style.marginLeft = $event.detail.expanded ? '16rem' : '5rem'"
            style="margin-left: {{ session('sidebar_expanded', true) ? '16rem' : '5rem' }};"
        >
            <livewire:spotlight />

            <div class="lg:mt-3.5">
                <livewire:breadcrumb />

                <x-divider />
            </div>

            <div class="p-4">
                {{ $slot }}
            </div>
        </main>

        @livewireScripts
    </body>
</html>
