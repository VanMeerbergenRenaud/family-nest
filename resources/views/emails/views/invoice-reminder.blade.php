@extends('emails.layouts.base')

@section('title', 'Rappel de facture - FamilyNest')

@section('header-title', 'Rappel de paiement d\'une facture')

@section('content')
    <table width="100%" cellpadding="0" cellspacing="0" style="font-family: sans-serif; background-color: #ffffff; padding: 24px;">
        <!-- Titre -->
        <tr>
            <td style="font-size: 20px; color: #1f2937; font-weight: 600; padding-bottom: 16px;">
                Bonjour {{ $user->name }},
            </td>
        </tr>

        <!-- Message d'information -->
        <tr>
            <td style="font-size: 16px; color: #4b5563; line-height: 1.65; padding-bottom: 24px;">
                Nous vous rappelons que votre facture arrive à échéance prochainement.
                Merci de procéder au paiement dès que possible.
            </td>
        </tr>

        <!-- Bloc facture -->
        <tr>
            <td style="padding-bottom: 32px;">
                <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px;">
                    <tr>
                        <td colspan="2" style="font-size: 18px; color: #334155; font-weight: 600; padding-bottom: 16px;">
                            Détails de la facture
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 24px; padding-bottom: 8px;">📄</td>
                        <td style="font-size: 15px; color: #4b5563; line-height: 1.6; padding-bottom: 8px;">
                            <strong style="color: #334155;">Facture :</strong> {{ $invoice->name }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 8px;">#️⃣</td>
                        <td style="font-size: 15px; color: #4b5563; line-height: 1.6; padding-bottom: 8px;">
                            <strong style="color: #334155;">Référence :</strong> {{ $invoice->reference ?? 'Non spécifiée' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 8px;">🏢</td>
                        <td style="font-size: 15px; color: #4b5563; line-height: 1.6; padding-bottom: 8px;">
                            <strong style="color: #334155;">Émetteur :</strong> {{ $invoice->issuer_name ?? 'Non spécifié' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 8px;">🗓</td>
                        <td style="font-size: 15px; color: #4b5563; line-height: 1.6; padding-bottom: 8px;">
                            <strong style="color: #334155;">Date d'émission :</strong> {{ $invoice->issued_date?->format('d/m/Y') ?? 'Non spécifiée' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom: 8px;">📆</td>
                        <td style="font-size: 15px; color: #4b5563; line-height: 1.6; padding-bottom: 8px;">
                            <strong style="color: #334155;">Date d'échéance :</strong> {{ $invoice->payment_due_date?->format('d/m/Y') ?? 'Non spécifiée' }}
                        </td>
                    </tr>
                    <tr>
                        <td>💶</td>
                        <td style="font-size: 15px; color: #4b5563; line-height: 1.6;">
                            <strong style="color: #334155;">Montant dû :</strong> {{ number_format($invoice->amount, 2, ',', ' ') }} {{ $invoice->symbol ?? '€' }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Bouton -->
        <tr>
            <td align="center">
                <a href="{{ $invoiceUrl }}" style="background-color: #667eea; color: #ffffff; text-decoration: none; padding: 16px 24px; border-radius: 12px; font-weight: 600; font-size: 16px; display: inline-block;">
                    Voir la facture
                </a>
            </td>
        </tr>

        <!-- Séparateur -->
        <tr>
            <td style="padding: 24px 0;">
                <hr style="border: none; height: 1px; background-color: #e2e8f0;">
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="text-align: center; font-size: 14px; color: #64748b; line-height: 1.6;">
                Besoin d'aide ? N'hésitez pas à nous contacter via l'adresse :<br>
                <a href="mailto:{{ $supportEmail }}" style="color: #667eea; text-decoration: none; font-weight: 500;">{{ $supportEmail }}</a>
            </td>
        </tr>
    </table>
@endsection
