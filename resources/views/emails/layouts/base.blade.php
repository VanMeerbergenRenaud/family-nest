<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; line-height: 1.6; color: #1f2937; background-color: #f9fafb;">
<div style="max-width: 600px; margin: 32px auto !important; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
    <header style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 32px !important; display: flex; flex-direction: column; align-items: center; gap: 24px;">
        <div style="margin-top: 16px; width: 64px; height: 64px; background-color: #ffffff; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);">
            <svg width="24" height="26" viewBox="0 0 24 26" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 32px; height: 32px;">
                <path opacity="0.8" d="M19.9334 14.1389L10.5019 4.95613L5.72753 0.306098C5.25114 0.103223 4.73709 -0.000987047 4.21765 7.04572e-06C1.91899 7.04572e-06 0.0498682 2.06612 0.00040673 4.63218V15.7203C-0.00785088 16.3457 0.109702 16.9666 0.346353 17.5475C0.583004 18.1284 0.934117 18.6579 1.37964 19.1058C1.82517 19.5537 2.35637 19.9112 2.94293 20.158C3.52949 20.4047 4.1599 20.5358 4.79817 20.5438H18.0148C18.7911 20.171 19.446 19.5935 19.9059 18.8762C20.3657 18.1589 20.6123 17.3301 20.618 16.483C20.6176 15.6543 20.3805 14.8423 19.9334 14.1389Z" fill="#E62C5A"/>
                <path opacity="0.8" d="M19.2018 4.96381H5.98776C5.21143 5.33659 4.55656 5.91409 4.0967 6.63142C3.63685 7.34876 3.39022 8.17752 3.38452 9.02461C3.3837 9.85356 3.61901 10.6663 4.06397 11.3713L13.4955 20.554L18.2828 25.2015C18.7569 25.4031 19.2681 25.5073 19.7849 25.5076C22.081 25.5076 23.9501 23.444 23.9996 20.878V9.78474C24.0155 8.52212 23.5191 7.30496 22.6194 6.40091C21.7197 5.49687 20.4904 4.97994 19.2018 4.96381Z" fill="#6AB7F0"/>
                <path d="M4.06397 11.3713L13.4955 20.554H18.0147C18.791 20.1812 19.4459 19.6037 19.9058 18.8864C20.3656 18.169 20.6123 17.3403 20.618 16.4932C20.6183 15.6647 20.3821 14.8527 19.9359 14.149L10.5044 4.96631H5.98776C5.21183 5.3389 4.55722 5.916 4.09739 6.63284C3.63756 7.34968 3.3907 8.17791 3.38452 9.02456C3.3837 9.85351 3.61901 10.6662 4.06397 11.3713Z" fill="#585FB1"/>
            </svg>
        </div>
        <h1 style="color: #ffffff; font-size: 28px; font-weight: 600; letter-spacing: -0.5px; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">@yield('header-title')</h1>
    </header>

    <main style="padding: 32px !important; display: flex; flex-direction: column; gap: 32px;">
        @yield('content')
    </main>

    <footer style="width: 100% !important; background-color: #f9fafb; padding: 32px !important; border-top: 1px solid #e5e7eb; display: flex; flex-direction: column; align-items: center; gap: 12px; text-align: center;">
        <div style="font-size: 16px; font-weight: 600; color: #1f2937;">FamilyNest</div>
        <div style="font-size: 14px; color: #64748b;">Gérez vos finances familiales en toute simplicité</div>
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 10px; font-size: 13px; color: #38638d;">
            <a href="{{ route('help-center') }}" style="color: #667eea; text-decoration: none; font-weight: 500;">Centre d'aide</a>
            <span>•</span>
            <a href="{{ route('privacy-policy') }}" style="color: #667eea; text-decoration: none; font-weight: 500;">Politique de confidentialité</a>
        </div>
    </footer>
</div>
</body>
</html>
