<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            background-color: #f9fafb;
        }

        .email-container {
            max-width: 600px;
            margin: 32px auto !important;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 32px !important;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 24px;
        }

        .content {
            padding: 32px !important;
            display: flex;
            flex-direction: column;
            gap: 32px;
        }

        .footer {
            width: 100% !important;
            background-color: #f9fafb;
            padding: 32px !important;
            border-top: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            text-align: center;
        }

        /* Logo */
        .logo {
            margin-top: 16px;
            width: 64px;
            height: 64px;
            background-color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .logo-svg {
            width: 32px;
            height: 32px;
        }

        /* Typography */
        .header-title {
            color: #ffffff;
            font-size: 28px;
            font-weight: 600;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            font-weight: 500;
            margin-top: 8px;
        }

        .greeting {
            font-size: 20px;
            color: #1f2937;
            font-weight: 600;
        }

        .message {
            font-size: 16px;
            color: #4b5563;
            line-height: 1.65;
        }

        .highlight {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0;
            border-left: 4px solid #667eea;
            padding: 24px;
            border-radius: 0 12px 12px 0;
            margin: 0;
        }

        .family-info {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .family-detail {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 15px;
            color: #64748b;
        }

        .family-detail strong {
            color: #334155;
            font-weight: 600;
        }

        /* Button */
        .cta-container {
            display: flex;
            justify-content: center;
        }

        .cta-button {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 16px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            transition: all 0.2s ease;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(102, 126, 234, 0.5);
        }

        /* Info Box */
        .info-box {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0;
            border-left: 4px solid #667eea;
            padding: 24px;
            border-radius: 0 12px 12px 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            font-size: 14px;
            color: #64748b;
            line-height: 1.5;
        }

        .info-item strong {
            color: #334155;
        }

        /* Icons */
        .send-icon {
            width: 24px;
            height: auto;
        }

        .family-icon {
            width: 24px;
            height: auto;
        }

        .show-icon {
            width: 20px;
            height: 20px;
        }

        /* Divider */
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e2e8f0 20%, #e2e8f0 80%, transparent);
        }

        /* Help Section */
        .help-section {
            text-align: center;
            font-size: 14px;
            color: #64748b;
            line-height: 1.6;
        }

        .help-section a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        /* Footer Elements */
        .footer-brand {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
        }

        .footer-tagline {
            font-size: 14px;
            color: #64748b;
        }

        .footer-links {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 13px;
            color: #38638d;
        }

        .footer-links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        /* Responsive */
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                padding: 12px;
            }

            .email-container {
                border-radius: 8px;
            }

            .header {
                padding: 36px 24px;
                gap: 20px;
            }

            .content {
                padding: 36px 24px;
                gap: 28px;
            }

            .footer {
                padding: 24px;
                gap: 10px;
            }

            .logo {
                width: 70px;
                height: 70px;
            }

            .header-title {
                font-size: 24px;
            }

            .greeting {
                font-size: 18px;
            }

            .cta-button {
                padding: 16px 32px;
                font-size: 15px;
            }

            .info-box, .highlight {
                padding: 20px;
                gap: 10px;
            }

            .footer-links {
                flex-direction: column;
                gap: 8px;
            }
        }
    </style>
</head>
<body>
<div class="email-container">
    <header class="header">
        <div class="logo">
            <svg class="logo-svg" width="24" height="26" viewBox="0 0 24 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path opacity="0.8" d="M19.9334 14.1389L10.5019 4.95613L5.72753 0.306098C5.25114 0.103223 4.73709 -0.000987047 4.21765 7.04572e-06C1.91899 7.04572e-06 0.0498682 2.06612 0.00040673 4.63218V15.7203C-0.00785088 16.3457 0.109702 16.9666 0.346353 17.5475C0.583004 18.1284 0.934117 18.6579 1.37964 19.1058C1.82517 19.5537 2.35637 19.9112 2.94293 20.158C3.52949 20.4047 4.1599 20.5358 4.79817 20.5438H18.0148C18.7911 20.171 19.446 19.5935 19.9059 18.8762C20.3657 18.1589 20.6123 17.3301 20.618 16.483C20.6176 15.6543 20.3805 14.8423 19.9334 14.1389Z" fill="#E62C5A"/>
                <path opacity="0.8" d="M19.2018 4.96381H5.98776C5.21143 5.33659 4.55656 5.91409 4.0967 6.63142C3.63685 7.34876 3.39022 8.17752 3.38452 9.02461C3.3837 9.85356 3.61901 10.6663 4.06397 11.3713L13.4955 20.554L18.2828 25.2015C18.7569 25.4031 19.2681 25.5073 19.7849 25.5076C22.081 25.5076 23.9501 23.444 23.9996 20.878V9.78474C24.0155 8.52212 23.5191 7.30496 22.6194 6.40091C21.7197 5.49687 20.4904 4.97994 19.2018 4.96381Z" fill="#6AB7F0"/>
                <path d="M4.06397 11.3713L13.4955 20.554H18.0147C18.791 20.1812 19.4459 19.6037 19.9058 18.8864C20.3656 18.169 20.6123 17.3403 20.618 16.4932C20.6183 15.6647 20.3821 14.8527 19.9359 14.149L10.5044 4.96631H5.98776C5.21183 5.3389 4.55722 5.916 4.09739 6.63284C3.63756 7.34968 3.3907 8.17791 3.38452 9.02456C3.3837 9.85351 3.61901 10.6662 4.06397 11.3713Z" fill="#585FB1"/>
            </svg>
        </div>
        <h1 class="header-title">@yield('header-title')</h1>
    </header>

    <main class="content">
        @yield('content')
    </main>

    <footer class="footer">
        <div class="footer-brand">FamilyNest</div>
        <div class="footer-tagline">Gérez vos finances familiales en toute simplicité</div>
        <div class="footer-links">
            <a href="{{ route('help-center') }}">Centre d'aide</a>
            <span>•</span>
            <a href="{{ route('privacy-policy') }}">Politique de confidentialité</a>
        </div>
    </footer>
</div>
</body>
</html>
