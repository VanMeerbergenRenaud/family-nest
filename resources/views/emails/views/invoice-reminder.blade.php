@extends('emails.layouts.base')

@section('title', 'Rappel de facture - FamilyNest')

@section('header-title', 'Rappel de paiement d\'une facture')

@section('content')
    <div class="greeting">
        Bonjour {{ $user->name }},
    </div>

    <div class="message">
        Nous vous rappelons que votre facture arrive à échéance prochainement.
        Merci de procéder au paiement dès que possible.
    </div>

    <div class="info-box">
        <h2 class="info-box-title">Détails de la facture</h2>
        <div class="info-item">
            <span class="info-icon">📄</span>
            <span><strong>Facture :</strong> {{ $invoice->name }}</span>
        </div>
        <div class="info-item">
            <span class="info-icon">#️⃣</span>
            <span><strong>Référence :</strong> {{ $invoice->reference ?? 'Non spécifiée' }}</span>
        </div>
        <div class="info-item">
            <span class="info-icon">🏢</span>
            <span><strong>Émetteur :</strong> {{ $invoice->issuer_name ?? 'Non spécifié' }}</span>
        </div>
        <div class="info-item">
            <span class="info-icon">🗓</span>
            <span><strong>Date d'émission :</strong> {{ $invoice->issued_date?->format('d/m/Y') ?? 'Non spécifiée' }}</span>
        </div>
        <div class="info-item">
            <span class="info-icon">️📆</span>
            <span><strong>Date d'échéance :</strong> {{ $invoice->payment_due_date?->format('d/m/Y') ?? 'Non spécifiée' }}</span>
        </div>
        <div class="info-item">
            <span class="info-icon">💶</span>
            <span><strong>Montant dû :</strong> {{ number_format($invoice->amount, 2, ',', ' ') }} {{ $invoice->symbol ?? '€' }}</span>
        </div>
    </div>


    <div class="cta-container">
        <a href="{{ $invoiceUrl }}" class="cta-button">
            <svg class="show-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Voir la facture
        </a>
    </div>

    <div class="divider"></div>

    <div class="help-section">
        Besoin d'aide ? N'hésitez pas à nous contactez via l'adresse : <br>
        <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>
    </div>
@endsection
