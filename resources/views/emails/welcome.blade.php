<!DOCTYPE html>
<html>
<head>
    <title>Bienvenue sur notre plateforme</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .email-container {
            background-color: #ffffff;
            padding: 20px;
            max-width: 600px;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        p {
            color: #555555;
            font-size: 16px;
            line-height: 1.5;
        }

        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #777777;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <h1>Bonjour 👋🏻!</h1>
        <p>
            Merci de vous être inscrit sur notre plateforme. Nous sommes ravis de vous avoir parmi nous.
        </p>
        <p>
            N'hésitez pas à explorer nos fonctionnalités et faites-nous savoir si vous avez besoin d'aide.
        </p>
        <p>
            Cordialement,<br />L'équipe {{ config('app.name') }}
        </p>
        <div class="footer">
            <p>
                &copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.
            </p>
        </div>
    </div>
</body>
</html>
