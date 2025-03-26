<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body>
        <header>
            {{ $banner ?? null }}
        </header>

        <livewire:sidebar />

        <main class="flex-1 transition-all duration-300 max-lg:!ml-0"
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

        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('sidebar-toggled', (event) => {
                    const mainContent = document.querySelector('main');
                    if (event.expanded) {
                        mainContent.style.marginLeft = '16rem'; // 16rem = 64px (w-64)
                    } else {
                        mainContent.style.marginLeft = '5rem';  // 5rem = 20px (w-20)
                    }
                });
            });
        </script>
    </body>
</html>
