@extends('emails.layouts.base')

@section('title', 'Vérification d\'email - FamilyNest')

@section('header-title', 'Vérification de votre adresse mail')

@section('content')
    <div class="greeting">
        Bonjour {{ $user->name }},
    </div>

    <div class="message">
        Bienvenue dans la famille FamilyNest ! Pour finaliser votre inscription et sécuriser votre compte, vous devez vérifier votre adresse email en cliquant sur le lien suivant.
    </div>

    <div class="cta-container">
        <a href="{{ $verificationUrl }}" class="cta-button">
            <svg class="send-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z"/>
            </svg>
            Vérifier mon mail
        </a>
    </div>

    <div class="info-box">
        <div class="info-item">
            <span class="info-icon">⏱️</span>
            <span><strong>Ce lien expire dans {{ $expirationMinutes }} minutes</strong></span>
        </div>
        <div class="info-item">
            <span class="info-icon">🔒</span>
            <span>Pour votre sécurité, ne partagez jamais ce lien</span>
        </div>
        <div class="info-item">
            <span class="info-icon">❓</span>
            <span>Vous n'avez pas créé de compte ? Ignorez cet email</span>
        </div>
    </div>

    <div class="divider"></div>

    <div class="help-section">
        Besoin d'aide ? N'hésitez pas à nous contactez via l'adresse : <br>
        <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>
    </div>
@endsection
