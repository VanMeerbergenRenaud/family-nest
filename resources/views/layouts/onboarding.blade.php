<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body>
        @include('partials.no-js')

        <x-toaster-hub />

        <!-- Background -->
        <div class="fixed inset-0 -z-10 h-screen w-screen bg-white isolate">
            <!-- Grille de points -->
            <div class="absolute inset-0 h-full w-full bg-[radial-gradient(theme(colors.slate.200)_1px,transparent_1px)] bg-[size:20px_20px] opacity-75"></div>

            <!-- Conteneur pour les formes Aurora -->
            <div class="absolute inset-0 -z-10 h-full w-full overflow-hidden">
                <div class="animate-aurora absolute -top-80 -left-96 h-[600px] w-[800px] rounded-full bg-violet-300/20 blur-[150px]"></div>
                <div class="animate-aurora absolute -bottom-96 -right-60 h-[700px] w-[700px] rounded-full bg-rose-300/20 blur-[150px] [animation-delay:-6s]"></div>
                <div class="animate-aurora absolute -bottom-20 left-20 h-[400px] w-[300px] rounded-full bg-sky-300/15 blur-[120px] [animation-delay:-12s]"></div>
            </div>
        </div>

        <main class="flex-1 flex-center flex-col z-1 !bg-transparent">
            <h1 role="heading" aria-level="1" class="sr-only">{{ $title ?? 'Interface d\'accueil' }}</h1>

            {{ $slot }}
        </main>

        @livewireScripts
    </body>
</html>
