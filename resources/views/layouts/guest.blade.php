<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body>
        <main class="min-h-[100vh]">
            {{ $slot }}
        </main>

        @livewireScripts
    </body>
</html>
