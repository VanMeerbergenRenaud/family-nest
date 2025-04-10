<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation à rejoindre la famille {{ $family->name }}</title>
    <span class="preheader">
        {{ $inviter->name }} vous invite à rejoindre la famille {{ $family->name }} sur FamilyNest.
    </span>
    <style>
        .preheader {
            display: none !important;
            visibility: hidden;
            font-size: 1px;
            color: #ffffff;
            line-height: 1px;
            max-height: 0;
            max-width: 0;
            opacity: 0;
            overflow: hidden;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #374151;
            background-color: #F3F4F6;
        }

        /* Conteneurs */
        .email-wrapper {
            background-color: #F3F4F6;
            padding: 32px 0;
        }

        .email-container {
            max-width: 580px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .email-card {
            background-color: #FFFFFF;
            border-radius: 12px;
            border: 1px solid #E5E7EB;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 32px;
            margin-bottom: 24px;
        }

        /* Éléments */
        .header {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: start;
            margin-bottom: 8px;
        }

        .header-description {
            padding-left: 12px;
        }

        .logo {
            max-width: 32px;
            height: auto;
            margin-right: 20px;
        }

        .heading {
            font-size: 24px;
            font-weight: 600;
            color: #222;
            margin: 0 0 8px 0;
        }

        .text {
            margin: 0 0 20px 0;
        }

        .highlight {
            font-weight: 600;
            color: #222;
        }

        /* Tableau */
        .details-table {
            width: 100%;
            margin: 32px 0;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            overflow: hidden;
        }

        .details-th {
            padding: 14px 18px;
            text-align: left;
            border-bottom: 1px solid #E5E7EB;
            color: #6B7280;
            background-color: #F9FAFB;
            width: 35%;
        }

        .details-td {
            padding: 14px 18px;
            text-align: left;
            border-bottom: 1px solid #E5E7EB;
            color: #222;
        }

        .details-last-row .details-th,
        .details-last-row .details-td {
            border-bottom: none;
        }

        /* Bouton */
        .button-container {
            text-align: center;
            margin: 28px 0;
        }

        .button {
            display: inline-block;
            background-color: #4F46E5;
            color: #FFFFFF !important;
            font-weight: 600;
            text-decoration: none !important;
            padding: 12px 20px;
            border-radius: 8px;
        }

        /* Notice d'expiration */
        .expiry-notice {
            background-color: #F9FAFB;
            border-radius: 8px;
            padding: 16px;
            margin-top: 32px;
            text-align: center;
            border: 1px dashed #D1D5DB;
        }

        .expiry-text {
            margin: 0;
            color: #6B7280;
        }

        /* Footer */
        .footer {
            text-align: center;
            color: #9CA3AF;
            margin-top: 32px;
            font-size: 13px;
        }

        .footer-text {
            margin: 4px 0;
        }

        /* Mobile */
        @media screen and (max-width: 600px) {
            .email-card {
                padding: 25px;
            }

            .details-th,
            .details-td {
                display: block;
                width: auto;
            }

            .details-th {
                border-bottom: none;
                padding-bottom: 4px;
            }

            .details-td {
                padding-top: 0;
                padding-left: 15px;
            }
        }
    </style>
</head>
<body>
<div class="email-wrapper">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <div class="email-container">
                    <div class="email-card">
                        <div class="header">
                            <img src="{{ asset('img/favicon.svg')}}" alt="Logo FamilyNest" class="logo">
                            <h1 class="heading">Invitation à rejoindre une famille</h1>
                        </div>

                        <div class="header-description">
                            <p class="text">Salutation 👋🏻,</p>
                            <p class="text">
                                <span class="highlight">{{ $inviter->name }}</span> vous a invité(e) à rejoindre la famille
                                <span class="highlight">{{ $family->name }}</span> sur l'application FamilyNest.
                            </p>
                        </div>

                        <table class="details-table" width="100%" border="0" cellpadding="0" cellspacing="0"
                               role="presentation">
                            <tr>
                                <th class="details-th">Famille</th>
                                <td class="details-td">{{ $family->name }}</td>
                            </tr>
                            <tr>
                                <th class="details-th">Invité par</th>
                                <td class="details-td">{{ $inviter->name }}</td>
                            </tr>
                            <tr>
                                <th class="details-th">Votre rôle</th>
                                <td class="details-td">{{ $role }}</td>
                            </tr>
                            <tr class="details-last-row">
                                <th class="details-th">Votre relation</th>
                                <td class="details-td">{{ $relation }}</td>
                            </tr>
                        </table>

                        <div class="button-container">
                            <a href="{{ $url }}" class="button" target="_blank">
                                Accepter l'invitation
                            </a>
                        </div>

                        <p class="text">
                            Si le bouton ne fonctionne pas, copiez et collez ce lien dans votre navigateur :<br>
                            <a href="{{ $url }}" target="_blank">{{ $url }}</a>
                        </p>

                        <div class="expiry-notice">
                            <p class="expiry-text">Cette invitation expirera dans 5 jours.</p>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <div class="footer">
        <p class="footer-text">Vous recevez cet email car {{ $inviter->name }} vous a invité sur FamilyNest.</p>
        <p class="footer-text">© {{ date('Y') }} FamilyNest. Tous droits réservés.</p>
    </div>
</div>
</body>
</html>
