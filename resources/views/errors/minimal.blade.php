<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <!-- Scripts -->
        @livewireStyles
        @vite(['resources/css/app.scss', 'resources/js/app.js'])
    </head>
    <body>
        <div class="error__page">
            <div class="error__page__container">
                <div class="error__page__container__content">
                    <div class="error__page__container__content__icon">
                        @yield('icon')
                    </div>

                    <div class="error__page__container__content__code">
                        @yield('code')
                    </div>

                    <div class="error__page__container__content__message">
                        @yield('message')
                    </div>
                </div>
                <div class="error__page__container__content__links">
                    <a href="{{ url()->previous() }}" class="link-secondary">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15 8H1M1 8L8 15M1 8L8 1" stroke="#344054" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        {{ __('Retour') }}
                    </a>
                    <a href="{{ route('dashboard') }}" class="link-primary">
                        {{ __('Retourner à l’accueil') }}
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>
