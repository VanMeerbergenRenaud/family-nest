<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body>
        @include('partials.no-js')

        <div class="min-h-[100vh]">
            {{ $slot }}
        </div>

        @livewireScripts
    </body>
</html>
