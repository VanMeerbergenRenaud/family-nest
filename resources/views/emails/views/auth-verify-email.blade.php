@extends('emails.layouts.base')

@section('title', 'Vérification d\'email - FamilyNest')

@section('header-title', 'Vérification de votre adresse mail')

@section('content')
    <div style="font-size: 20px; color: #1f2937; font-weight: 600;">
        Bonjour {{ $user->name }},
    </div>

    <div style="font-size: 16px; color: #4b5563; line-height: 1.65;">
        Bienvenue dans la famille FamilyNest ! Pour finaliser votre inscription et sécuriser votre compte, vous devez vérifier votre adresse email en cliquant sur le lien suivant.
    </div>

    <div style="display: flex; justify-content: center;">
        <a href="{{ $verificationUrl }}" style="display: inline-flex; align-items: center; gap: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; padding: 16px 24px; border-radius: 12px; font-weight: 600; font-size: 16px; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4); transition: all 0.2s ease;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 24px; height: auto;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z"/>
            </svg>
            Vérifier mon mail
        </a>
    </div>

    <div style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border: 1px solid #e2e8f0; border-left: 4px solid #667eea; padding: 24px; border-radius: 0 12px 12px 0; display: flex; flex-direction: column; gap: 12px;">
        <div style="display: flex; align-items: flex-start; gap: 20px; font-size: 14px; color: #64748b; line-height: 1.5;">
            <span>⏱️</span>
            <span><strong style="color: #334155;">Ce lien expire dans {{ $expirationMinutes }} minutes</strong></span>
        </div>
        <div style="display: flex; align-items: flex-start; gap: 20px; font-size: 14px; color: #64748b; line-height: 1.5;">
            <span>🔒</span>
            <span>Pour votre sécurité, ne partagez jamais ce lien</span>
        </div>
        <div style="display: flex; align-items: flex-start; gap: 20px; font-size: 14px; color: #64748b; line-height: 1.5;">
            <span>❓</span>
            <span>Vous n'avez pas créé de compte ? Ignorez cet email</span>
        </div>
    </div>

    <div style="height: 1px; background: linear-gradient(90deg, transparent, #e2e8f0 20%, #e2e8f0 80%, transparent);"></div>

    <div style="text-align: center; font-size: 14px; color: #64748b; line-height: 1.6;">
        Besoin d'aide ? N'hésitez pas à nous contactez via l'adresse : <br>
        <a href="mailto:{{ $supportEmail }}" style="color: #667eea; text-decoration: none; font-weight: 500;">{{ $supportEmail }}</a>
    </div>
@endsection
