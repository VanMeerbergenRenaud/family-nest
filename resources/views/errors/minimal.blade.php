<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Erreur')</title>

    <!-- Scripts -->
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-200">
    <div class="flex-center flex-col min-h-screen px-8 py-16 bg-gray-200">
        @yield('icon')

        <h1 class="my-8 text-[#CD475E] display-3xl-semibold tracking-[-0.0625rem]">
            @yield('code')
        </h1>

        <p class="mb-8 max-w-[40rem] text-center display-md-medium tracking-[-0.0625rem] text-gray-900">
            @yield('description')
        </p>

        <p class="max-w-[40rem] text-center text-md-regular text-gray-500">
            @yield('message')
        </p>

        <div class="mt-12 flex-center flex-col gap-4 md:flex-row">
            <a href="{{ url()->previous() }}"
               class="flex-center py-3 px-6 gap-4 text-md-medium rounded-lg bg-gray-100 text-dark hover:bg-gray-50">
                <x-svg.arrow-left/>
                Retour en arrière
            </a>
            <a href="{{ route('dashboard') }}"
               class="flex-center py-3 px-8 gap-3 text-md-medium rounded-lg bg-gray-800 text-white hover:bg-gray-900">
                Aller à l'accueil
            </a>
        </div>
    </div>
</body>
</html>
