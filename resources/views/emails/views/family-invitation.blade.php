@extends('emails.layouts.base')

@section('title', 'Invitation à une famille - FamilyNest')

@section('header-title', 'Invitation à rejoindre une famille')

@section('content')
    <div style="font-size: 20px; color: #1f2937; font-weight: 600;">
        Bonjour ! 👋
    </div>

    <div style="font-size: 16px; color: #4b5563; line-height: 1.65;">
        <strong>{{ $inviter->name }}</strong> vous invite à rejoindre sa famille
        "<strong>{{ $family->name }}</strong>" sur FamilyNest, l'application qui simplifie
        la gestion des finances familiales.
    </div>

    <div style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border: 1px solid #e2e8f0; padding: 24px; border-radius: 12px; display: flex; flex-direction: column; gap: 12px;">
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <div style="display: flex; align-items: flex-start; gap: 20px; font-size: 15px; color: #4b5563; line-height: 1.5;">
                <span>🏠</span>
                <span><strong style="color: #334155;">Famille :</strong> {{ $family->name }}</span>
            </div>
            <div style="display: flex; align-items: flex-start; gap: 20px; font-size: 15px; color: #4b5563; line-height: 1.5;">
                <span>👤</span>
                <span><strong style="color: #334155;">Invité par :</strong> {{ $inviter->name }}</span>
            </div>
            <div style="display: flex; align-items: flex-start; gap: 20px; font-size: 15px; color: #4b5563; line-height: 1.5;">
                <span>🔑</span>
                <span><strong style="color: #334155;">Votre rôle :</strong> {{ $permissionLabel }}</span>
            </div>
            <div style="display: flex; align-items: flex-start; gap: 20px; font-size: 15px; color: #4b5563; line-height: 1.5;">
                <span>❤️</span>
                <span><strong style="color: #334155;">Relation :</strong> {{ $relationLabel }}</span>
            </div>
        </div>
    </div>

    <div style="display: flex; justify-content: center;">
        <a href="{{ $invitationUrl }}" style="display: inline-flex; align-items: center; gap: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; padding: 16px 24px; border-radius: 12px; font-weight: 600; font-size: 16px; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4); transition: all 0.2s ease;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 24px; height: auto;">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            Rejoindre la famille
        </a>
    </div>

    <div style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border: 1px solid #e2e8f0; border-left: 4px solid #667eea; padding: 24px; border-radius: 0 12px 12px 0; display: flex; flex-direction: column; gap: 12px;">
        <div style="display: flex; align-items: flex-start; gap: 20px; font-size: 14px; color: #64748b; line-height: 1.5;">
            <span>⏱️</span>
            <span><strong style="color: #334155;">Cette invitation expire dans {{ $expirationDays }} jours</strong></span>
        </div>
        <div style="display: flex; align-items: flex-start; gap: 20px; font-size: 14px; color: #64748b; line-height: 1.5;">
            <span>🔒</span>
            <span>Lien sécurisé - ne le partagez avec personne d'autre</span>
        </div>
        <div style="display: flex; align-items: flex-start; gap: 20px; font-size: 14px; color: #64748b; line-height: 1.5;">
            <span>📱</span>
            <span>Accédez à FamilyNest depuis n'importe quel appareil</span>
        </div>
        <div style="display: flex; align-items: flex-start; gap: 20px; font-size: 14px; color: #64748b; line-height: 1.5;">
            <span>❓</span>
            <span>Vous ne connaissez pas {{ $inviter->name }} ? Ignorez cet email</span>
        </div>
    </div>

    <div style="height: 1px; background: linear-gradient(90deg, transparent, #e2e8f0 20%, #e2e8f0 80%, transparent);"></div>

    <div style="text-align: center; font-size: 14px; color: #64748b; line-height: 1.6;">
        <strong style="color: #334155;">Pourquoi rejoindre FamilyNest ?</strong><br>
        Gérez vos finances familiales ensemble, partagez vos factures et dépenses,
        et gardez un œil sur le budget familial en détail sans aucun oubli.<br>
        Besoin d'aide ? N'hésitez pas à nous contactez via l'adresse : <br>
        <a href="mailto:{{ $supportEmail }}" style="color: #667eea; text-decoration: none; font-weight: 500;">{{ $supportEmail }}</a>
    </div>
@endsection
