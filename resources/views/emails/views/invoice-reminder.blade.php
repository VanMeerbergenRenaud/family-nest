@extends('emails.layouts.base')

@section('title', 'Rappel de facture - FamilyNest')

@section('header-title', 'Rappel de paiement d\'une facture')

@section('content')
    <div style="font-size: 20px; color: #1f2937; font-weight: 600;">
        Bonjour {{ $user->name }},
    </div>

    <div style="font-size: 16px; color: #4b5563; line-height: 1.65;">
        Nous vous rappelons que votre facture arrive à échéance prochainement.
        Merci de procéder au paiement dès que possible.
    </div>

    <div
        style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border: 1px solid #e2e8f0; padding: 24px; border-radius: 12px; display: flex; flex-direction: column; gap: 16px;">
        <h2 style="margin: 0; font-size: 18px; color: #334155; font-weight: 600;">Détails de la facture</h2>
        <div style="display: flex; align-items: flex-start; gap: 20px; font-size: 15px; color: #4b5563; line-height: 1.5;">
            <span>📄</span>
            <span><strong style="color: #334155;">Facture :</strong> {{ $invoice->name }}</span>
        </div>
        <div
            style="display: flex; align-items: flex-start; gap: 20px; font-size: 15px; color: #4b5563; line-height: 1.5;">
            <span>#️⃣</span>
            <span><strong style="color: #334155;">Référence :</strong> {{ $invoice->reference ?? 'Non spécifiée' }}</span>
        </div>
        <div
            style="display: flex; align-items: flex-start; gap: 20px; font-size: 15px; color: #4b5563; line-height: 1.5;">
            <span>🏢</span>
            <span><strong style="color: #334155;">Émetteur :</strong> {{ $invoice->issuer_name ?? 'Non spécifié' }}</span>
        </div>
        <div
            style="display: flex; align-items: flex-start; gap: 20px; font-size: 15px; color: #4b5563; line-height: 1.5;">
            <span>🗓</span>
            <span><strong style="color: #334155;">Date d'émission :</strong> {{ $invoice->issued_date?->format('d/m/Y') ?? 'Non spécifiée' }}</span>
        </div>
        <div
            style="display: flex; align-items: flex-start; gap: 20px; font-size: 15px; color: #4b5563; line-height: 1.5;">
            <span>️📆</span>
            <span><strong style="color: #334155;">Date d'échéance :</strong> {{ $invoice->payment_due_date?->format('d/m/Y') ?? 'Non spécifiée' }}</span>
        </div>
        <div
            style="display: flex; align-items: flex-start; gap: 20px; font-size: 15px; color: #4b5563; line-height: 1.5;">
            <span>💶</span>
            <span><strong style="color: #334155;">Montant dû :</strong> {{ number_format($invoice->amount, 2, ',', ' ') }} {{ $invoice->symbol ?? '€' }}</span>
        </div>
    </div>

    <div style="display: flex; justify-content: center;">
        <a href="{{ $invoiceUrl }}"
           style="display: inline-flex; align-items: center; gap: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; padding: 16px 24px; border-radius: 12px; font-weight: 600; font-size: 16px; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4); transition: all 0.2s ease;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 24px; height: auto;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Voir la facture
        </a>
    </div>

    <div style="height: 1px; background: linear-gradient(90deg, transparent, #e2e8f0 20%, #e2e8f0 80%, transparent);"></div>

    <div style="text-align: center; font-size: 14px; color: #64748b; line-height: 1.6;">
        Besoin d'aide ? N'hésitez pas à nous contactez via l'adresse : <br>
        <a href="mailto:{{ $supportEmail }}" style="color: #667eea; text-decoration: none; font-weight: 500;">{{ $supportEmail }}</a>
    </div>
@endsection
