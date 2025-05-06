@props([
    'expanded' => true,
])

<ul role="list">
    <x-sidebar-menu.link href="{{ route('dashboard') }}" icon="home" label="Tableau de bord" :expanded="$expanded" />
    <x-sidebar-menu.link href="{{ route('calendar') }}" icon="calendar" label="Calendrier" :expanded="$expanded" />
    <x-sidebar-menu.link href="{{ route('invoices.index') }}" icon="document" label="Mes factures" :expanded="$expanded" />
    <x-sidebar-menu.link href="{{ route('invoices.archived') }}" icon="swatch" label="Archives" :expanded="$expanded" />
    <x-sidebar-menu.link href="{{ route('goals') }}" icon="tag" label="Objectifs" :expanded="$expanded" />
    <x-sidebar-menu.link href="{{ route('family') }}" icon="user" label="Famille" :expanded="$expanded" />
</ul>
