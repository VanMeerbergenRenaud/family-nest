<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f9fafb; font-family: Arial, sans-serif;">

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f9fafb; padding: 32px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="background-color: #ffffff; width: 100%; max-width: 600px;">

                <!-- Header -->
                <tr>
                   <td align="center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 16px 16px 32px;">
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td align="center" style="padding-top: 16px;">
                                    <span style="
                                        display: inline-block;
                                        width: 64px;
                                        height: 64px;
                                        border-radius: 50%;
                                        background-color: #ffffff;
                                        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
                                        text-align: center;
                                        line-height: 64px;
                                    ">
                                        <img src="{{ asset('img/og-image.png') }}" width="32" height="32" alt="Logo" style="
                                            display: inline-block;
                                            vertical-align: middle;
                                            border: 0;
                                        " />
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" style="padding-top: 24px; color: #ffffff; font-size: 24px; font-weight: bold;">
                                    @yield('header-title')
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Body Content -->
                <tr>
                    <td style="padding: 16px; color: #1f2937; font-size: 16px; line-height: 1.6;">
                        @yield('content')
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background-color: #f9fafb; padding: 24px; border-top: 1px solid #e5e7eb; text-align: center;">
                        <div style="font-size: 16px; font-weight: bold; color: #1f2937;">FamilyNest</div>
                        <div style="font-size: 14px; color: #64748b;">Gérez vos finances familiales en toute simplicité</div>
                        <div style="margin-top: 10px;">
                            <a href="{{ route('help-center') }}" style="color: #667eea; text-decoration: none; font-size: 13px; font-weight: 500;">Centre d'aide</a>
                            &nbsp;|&nbsp;
                            <a href="{{ route('privacy') }}" style="color: #667eea; text-decoration: none; font-size: 13px; font-weight: 500;">Politique de confidentialité</a>
                        </div>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
