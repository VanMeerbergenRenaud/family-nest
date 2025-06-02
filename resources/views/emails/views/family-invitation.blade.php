@extends('emails.layouts.base')

@section('title', 'Invitation à une famille - FamilyNest')

@section('header-title', 'Invitation à rejoindre une famille')

@section('content')
    <div class="greeting">
        Bonjour ! 👋
    </div>

    <div class="message">
        <strong>{{ $inviter->name }}</strong> vous invite à rejoindre sa famille
        "<strong>{{ $family->name }}</strong>" sur FamilyNest, l'application qui simplifie
        la gestion des finances familiales.
    </div>

    <div class="highlight">
        <div class="family-info">
            <div class="family-detail">
                <span>🏠</span>
                <span><strong>Famille :</strong> {{ $family->name }}</span>
            </div>
            <div class="family-detail">
                <span>👤</span>
                <span><strong>Invité par :</strong> {{ $inviter->name }}</span>
            </div>
            <div class="family-detail">
                <span>🔑</span>
                <span><strong>Votre rôle :</strong> {{ $permissionLabel }}</span>
            </div>
            <div class="family-detail">
                <span>❤️</span>
                <span><strong>Relation :</strong> {{ $relationLabel }}</span>
            </div>
        </div>
    </div>

    <div class="cta-container">
        <a href="{{ $invitationUrl }}" class="cta-button">
            <svg class="family-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            Rejoindre la famille
        </a>
    </div>

    <div class="info-box">
        <div class="info-item">
            <span class="info-icon">⏱️</span>
            <span><strong>Cette invitation expire dans {{ $expirationDays }} jours</strong></span>
        </div>
        <div class="info-item">
            <span class="info-icon">🔒</span>
            <span>Lien sécurisé - ne le partagez avec personne d'autre</span>
        </div>
        <div class="info-item">
            <span class="info-icon">📱</span>
            <span>Accédez à FamilyNest depuis n'importe quel appareil</span>
        </div>
        <div class="info-item">
            <span class="info-icon">❓</span>
            <span>Vous ne connaissez pas {{ $inviter->name }} ? Ignorez cet email</span>
        </div>
    </div>

    <div class="divider"></div>

    <div class="help-section">
        <strong>Pourquoi rejoindre FamilyNest ?</strong><br>
        Gérez vos finances familiales ensemble, partagez vos factures et dépenses,
        et gardez un œil sur le budget familial en détail sans aucun oubli.<br>
        Besoin d'aide ? N'hésitez pas à nous contactez via l'adresse : <br>
        <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>
    </div>
@endsection
