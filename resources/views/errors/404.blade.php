@extends('errors::minimal')

@section('title', __('Erreur 404'))
@section('icon')
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M22.5 22.5L18.4168 18.4167M21.3333 11.4167C21.3333 16.8935 16.8935 21.3333 11.4167 21.3333C5.93984 21.3333 1.5 16.8935 1.5 11.4167C1.5 5.93984 5.93984 1.5 11.4167 1.5C16.8935 1.5 21.3333 5.93984 21.3333 11.4167Z" stroke="#344054" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
@endsection
@section('code', 'Page non trouvé')
@section('message', __('FamilyNest peut aider pour beaucoup de choses, mais trouver cette page n’en fait pas partie.'))
