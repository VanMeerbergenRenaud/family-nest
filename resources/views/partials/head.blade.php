<!-- Meta tags -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Meta tags SEO -->
<meta name="description" content="FamilyNest - Votre solution familiale de gestion de factures intelligente. Centralisez, organisez et analysez vos factures, planifiez vos dépenses et atteignez vos objectifs financiers ensemble. Reconnaissance OCR, prévisions budgétaires et partage familial global.">
<meta name="keywords" content="gestion factures familiale, budget collaboratif, OCR factures, prévision financière, suivi dépenses mensuelles, analyse budget famille, centralisation documents financiers, rappel automatique paiements, économies ménage, application SaaS finances">
<meta name="author" content="Renaud Van Meerbergen, renaud.vanmeerbergen@gmail.com">
<meta name="robots" content="index, follow">

<!-- Open Graphs -->
<meta property="og:title" content="FamilyNest - Gestion financière intelligente pour toute la famille">
<meta property="og:description" content="Simplifiez votre vie financière familiale. Centralisez et organisez vos factures, collaborez avec votre famille, analysez vos dépenses et planifiez votre avenir financier grâce à notre solution tout-en-un.">
<meta property="og:image" content="{{ asset('img/og-image.png') }}">
<meta property="og:url" content="https://family-nest.laravel.cloud">
<meta property="og:site_name" content="FamilyNest">
<meta property="og:type" content="website">

<!-- Favicon -->
<link rel="icon" href="{{ asset('img/favicon.svg') }}">

{{-- M.Vilain css styles --}}
@if(auth()->check() && auth()->user()->email === 'dominique.vilain@gmail.com')
    @if(\Illuminate\Support\Facades\Storage::exists('user_styles/vip_user.css'))
        <style>
            {!! \Illuminate\Support\Facades\Storage::get('user_styles/vip_user.css') !!}
        </style>
    @endif
@endif

<!-- JavaScript required -->
<noscript>
    <style>
        .js-required {
            display: none !important;
        }

        .js-disabled-message {
            display: block !important;
        }
    </style>
</noscript>


<!-- Title -->
@if (Route::currentRouteName() === 'welcome')
    <title>{{ config('app.name', 'FamilyNest') }}</title>
@else
    <title>{{ $title ?? 'Titre de la page' }} | FamilyNest</title>
@endif

<!-- Styles -->
@livewireStyles
@vite(['resources/css/app.css', 'resources/js/app.js'])
