<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Rappel de paiement</title>
        <style>
            body {
                font-family: 'Helvetica Neue', Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                background-color: #f9f9f9;
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                background-color: #ffffff;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            }
            .header {
                text-align: center;
                padding: 20px 0;
                border-bottom: 1px solid #eaeaea;
            }
            .logo {
                max-width: 150px;
                height: auto;
            }
            .content {
                padding: 30px 20px;
            }
            .invoice-details {
                background-color: #f5f7fa;
                border-radius: 6px;
                padding: 20px;
                margin: 20px 0;
            }
            .detail-row {
                display: flex;
                justify-content: space-between;
                margin-bottom: 10px;
                padding-bottom: 10px;
                border-bottom: 1px dashed #eaeaea;
            }
            .detail-row:last-child {
                border-bottom: none;
                margin-bottom: 0;
                padding-bottom: 0;
            }
            .label {
                font-weight: 600;
                color: #6b7280;
            }
            .value {
                font-weight: 500;
                text-align: right;
            }
            .action-button {
                display: inline-block;
                background-color: #4f46e5;
                color: white;
                text-decoration: none;
                padding: 12px 24px;
                border-radius: 6px;
                font-weight: 500;
                margin: 20px 0;
                text-align: center;
            }
            .action-button:hover {
                background-color: #4338ca;
            }
            .footer {
                text-align: center;
                padding: 20px 0;
                font-size: 12px;
                color: #6b7280;
                border-top: 1px solid #eaeaea;
            }
            .amount {
                font-size: 22px;
                font-weight: bold;
                color: #4f46e5;
            }
            .reminder-badge {
                display: inline-block;
                background-color: #fef3c7;
                color: #92400e;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 500;
                margin-bottom: 15px;
            }
            .greeting {
                font-size: 20px;
                font-weight: 500;
                margin-bottom: 15px;
            }
            @media (max-width: 600px) {
                .container {
                    border-radius: 0;
                    padding: 10px;
                }
                .content {
                    padding: 20px 10px;
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <img src="{{ asset('img/favicon.svg') }}" alt="Logo" class="logo" loading="lazy">
            </div>

            <div class="content">
                <div class="greeting">Bonjour {{ $userName }},</div>

                <p><span class="reminder-badge">Rappel de paiement</span></p>

                <p>Nous vous rappelons que votre facture arrive à échéance prochainement. Merci de bien vouloir procéder au paiement dès que possible.</p>

                <div class="invoice-details">
                    <div class="detail-row">
                        <span class="label">Facture :</span>
                        <span class="value">{{ $invoice->name }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Référence :</span>
                        <span class="value">{{ $invoice->reference ?: 'Non spécifiée' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Émetteur :</span>
                        <span class="value">{{ $invoice->issuer_name ?: 'Non spécifié' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Date d'émission :</span>
                        <span class="value">{{ $invoice->issued_date ? $invoice->issued_date->format('d/m/Y') : 'Non spécifiée' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Date d'échéance :</span>
                        <span class="value">{{ $invoice->payment_due_date ? $invoice->payment_due_date->format('d/m/Y') : 'Non spécifiée' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Montant dû :</span>
                        <span class="value amount">{{ number_format($invoice->amount, 2, ',', ' ') }} {{ $invoice->symbol ?? '€' }}</span>
                    </div>
                </div>

                <p>Pour consulter tous les détails de cette facture ou pour effectuer un paiement, veuillez cliquer sur le bouton ci-dessous :</p>

                <div style="text-align: center;">
                    <a href="{{ url('/invoices/' . $invoice->id . '/show') }}" class="action-button">Voir la facture</a>
                </div>

                <p>Si vous avez déjà procédé au règlement, nous vous remercions et vous prions d'ignorer ce message.</p>

                <p>Cordialement,<br>L'équipe {{ config('app.name') }}</p>
            </div>

            <div class="footer">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
                <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
            </div>
        </div>
    </body>
</html>
