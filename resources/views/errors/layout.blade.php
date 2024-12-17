<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <!-- Scripts -->
        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="flex flex-col items-center justify-center min-h-screen py-16 bg-[#E8E8E8]">
            @yield('icon')

            <h1 class="mt-8 text-[#CD475E] font-semibold text-8xl tracking-[-0.0625rem] leading-[150%]">
                @yield('code')
            </h1>
            <p class="text-[#191E2C] max-w-[40rem] mb-6 font-medium text-3xl tracking-[-0.0625rem] leading-[100%]">
                @yield('description')
            </p>
            <p class="text-[#666] max-w-[40rem] text-center text-base leading-[135%]">
                @yield('message')
            </p>
        </div>
    </body>
</html>
