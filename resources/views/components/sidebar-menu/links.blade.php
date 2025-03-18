@props([
    'expanded' => true,
])

<ul>
    <x-sidebar-menu.link href="{{ route('dashboard') }}" icon="home" label="Tableau de bord" :expanded="$expanded" />
    <x-sidebar-menu.link href="{{ route('invoices.index') }}" icon="document" label="Factures" :expanded="$expanded" />
    <x-sidebar-menu.link href="{{ route('themes') }}" icon="swatch" label="Thèmes" :expanded="$expanded" />
    <x-sidebar-menu.link href="{{ route('calendar') }}" icon="calendar" label="Calendrier" :expanded="$expanded" />
    <x-sidebar-menu.link href="{{ route('goals') }}" icon="tag" label="Objectifs" :expanded="$expanded" />
    <x-sidebar-menu.link href="{{ route('family') }}" icon="users" label="Familles" :expanded="$expanded" />
</ul>
