@extends('emails.layouts.base')

@section('title', 'Invitation à une famille - FamilyNest')

@section('header-title', 'Invitation à rejoindre une famille')

@section('content')
    <table width="100%" cellpadding="0" cellspacing="0" style="font-family: sans-serif; background-color: #ffffff; padding: 24px;">
        <!-- Titre -->
        <tr>
            <td style="font-size: 20px; color: #1f2937; font-weight: 600; padding-bottom: 16px;">
                Bonjour ! 👋
            </td>
        </tr>

        <!-- Texte d'invitation -->
        <tr>
            <td style="font-size: 16px; color: #4b5563; line-height: 1.65; padding-bottom: 24px;">
                <strong>{{ $inviter->name }}</strong> vous invite à rejoindre sa famille
                "<strong>{{ $family->name }}</strong>" sur FamilyNest, l'application qui simplifie
                la gestion des finances familiales.
            </td>
        </tr>

        <!-- Bloc d'informations famille -->
        <tr>
            <td style="padding-bottom: 32px;">
                <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px;">
                    <tr>
                        <td style="font-size: 15px; color: #4b5563; line-height: 1.5; padding: 6px 0;">
                            🏠 <strong style="color: #334155;">Famille :</strong> {{ $family->name }}
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 15px; color: #4b5563; line-height: 1.5; padding: 6px 0;">
                            👤 <strong style="color: #334155;">Invité par :</strong> {{ $inviter->name }}
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 15px; color: #4b5563; line-height: 1.5; padding: 6px 0;">
                            🔑 <strong style="color: #334155;">Votre rôle :</strong> {{ $permissionLabel }}
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 15px; color: #4b5563; line-height: 1.5; padding: 6px 0;">
                            ❤️ <strong style="color: #334155;">Relation :</strong> {{ $relationLabel }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Bouton Rejoindre -->
        <tr>
            <td align="center" style="padding-bottom: 32px;">
                <a href="{{ $invitationUrl }}" style="background-color: #667eea; color: #ffffff; text-decoration: none; padding: 16px 24px; border-radius: 12px; font-weight: 600; font-size: 16px; display: inline-block;">
                    🏡 Rejoindre la famille
                </a>
            </td>
        </tr>

        <!-- Bloc d'informations sécurités -->
        <tr>
            <td>
                <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f1f5f9; border: 1px solid #e2e8f0; border-left: 4px solid #667eea; border-radius: 0 12px 12px 0; padding: 24px;">
                    <tr>
                        <td style="font-size: 14px; color: #64748b; line-height: 1.5; padding-bottom: 12px;">
                            ⏱️ <strong style="color: #334155;">Cette invitation expire dans {{ $expirationDays }} jours</strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px; color: #64748b; line-height: 1.5; padding-bottom: 12px;">
                            🔒 Lien sécurisé – ne le partagez avec personne d'autre
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px; color: #64748b; line-height: 1.5; padding-bottom: 12px;">
                            📱 Accédez à FamilyNest depuis n'importe quel appareil
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px; color: #64748b; line-height: 1.5;">
                            ❓ Vous ne connaissez pas {{ $inviter->name }} ? Ignorez cet email
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Séparateur -->
        <tr>
            <td style="padding: 32px 0;">
                <hr style="border: none; height: 1px; background-color: #e2e8f0;">
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="text-align: center; font-size: 14px; color: #64748b; line-height: 1.6;">
                <strong style="color: #334155;">Pourquoi rejoindre FamilyNest ?</strong><br>
                Gérez vos finances familiales ensemble, partagez vos factures et dépenses,
                et gardez un œil sur le budget familial en détail sans aucun oubli.<br><br>
                Besoin d'aide ? N'hésitez pas à nous contacter à l'adresse :<br>
                <a href="mailto:{{ $supportEmail }}" style="color: #667eea; text-decoration: none; font-weight: 500;">{{ $supportEmail }}</a>
            </td>
        </tr>
    </table>
@endsection
