@extends('emails.layouts.base')

@section('title', 'Vérification d\'email - FamilyNest')

@section('header-title', 'Vérification de votre adresse mail')

@section('content')
    <table width="100%" cellpadding="0" cellspacing="0" style="font-family: sans-serif; background-color: #ffffff; padding: 24px;">
        <tr>
            <td style="font-size: 20px; color: #1f2937; font-weight: 600; padding-bottom: 16px;">
                Bonjour {{ $user->name }},
            </td>
        </tr>

        <tr>
            <td style="font-size: 16px; color: #4b5563; line-height: 1.65; padding-bottom: 24px;">
                Bienvenue dans la famille FamilyNest ! Pour finaliser votre inscription et sécuriser votre compte, vous devez vérifier votre adresse email en cliquant sur le lien suivant.
            </td>
        </tr>

        <tr>
            <td align="center" style="padding-bottom: 32px;">
                <!-- Bouton stylisé -->
                <a href="{{ $verificationUrl }}" style="background-color: #667eea; color: #ffffff; text-decoration: none; padding: 16px 24px; border-radius: 12px; font-weight: 600; font-size: 16px; display: inline-block;">
                    Vérifier mon mail
                </a>
            </td>
        </tr>

        <tr>
            <td>
                <!-- Encadré info -->
                <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f1f5f9; border: 1px solid #e2e8f0; border-left: 4px solid #667eea; border-radius: 0 12px 12px 0; padding: 24px;">
                    <tr>
                        <td style="font-size: 14px; color: #64748b; line-height: 1.5; padding-bottom: 12px;">
                            ⏱️ <strong style="color: #334155;">Ce lien expire dans {{ $expirationMinutes }} minutes</strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px; color: #64748b; line-height: 1.5; padding-bottom: 12px;">
                            🔒 Pour votre sécurité, ne partagez jamais ce lien
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px; color: #64748b; line-height: 1.5;">
                            ❓ Vous n'avez pas créé de compte ? Ignorez cet email
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td style="padding: 32px 0;">
                <!-- Séparateur -->
                <hr style="border: none; height: 1px; background-color: #e2e8f0;">
            </td>
        </tr>

        <tr>
            <td style="text-align: center; font-size: 14px; color: #64748b; line-height: 1.6;">
                Besoin d'aide ? N'hésitez pas à nous contacter à l'adresse :<br>
                <a href="mailto:{{ $supportEmail }}" style="color: #667eea; text-decoration: none; font-weight: 500;">{{ $supportEmail }}</a>
            </td>
        </tr>
    </table>
@endsection
