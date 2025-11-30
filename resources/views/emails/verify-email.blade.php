<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Email - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }
        .header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #4f46e5, #6366f1, #4f46e5);
            opacity: 0.7;
        }
        .logo {
            width: 120px;
            height: auto;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .content {
            padding: 40px 30px;
            background: linear-gradient(180deg, #ffffff 0%, #fafafa 100%);
        }
        .greeting {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 24px;
            letter-spacing: -0.3px;
        }
        .message {
            font-size: 16px;
            color: #374151;
            margin-bottom: 32px;
            line-height: 1.7;
        }
        .button-container {
            text-align: center;
            margin: 36px 0;
        }
        .verify-button {
            display: inline-block;
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: white;
            text-decoration: none;
            padding: 16px 36px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }
        .verify-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.3);
        }
        .warning {
            background-color: #fef2f2;
            border: 1px solid #fee2e2;
            border-radius: 8px;
            padding: 16px;
            margin: 24px 0;
            color: #991b1b;
            font-size: 14px;
            line-height: 1.6;
        }
        .secondary-info {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin: 24px 0;
            font-size: 14px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 6px 0;
            color: #6b7280;
            font-size: 14px;
        }
        .social-links {
            margin: 20px 0;
        }
        .social-link {
            display: inline-block;
            margin: 0 8px;
            color: #6b7280;
            text-decoration: none;
            font-size: 14px;
        }
        .social-link:hover {
            color: #4f46e5;
        }
        .app-name {
            font-weight: 700;
            color: #111827;
            letter-spacing: -0.3px;
        }
        @media only screen and (max-width: 600px) {
            .container {
                margin: 0;
                border-radius: 0;
            }
            .content {
                padding: 30px 20px;
            }
            .header {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1> {{ config('app.name') }}</h1>
            <p>Verificação de Email</p>
        </div>


        <div class="content">
            <div class="greeting">
                Olá, {{ $user->name }}!
            </div>

            <div class="message">
                <p>Obrigado por se registrar em nossa plataforma! Para completar seu cadastro e acessar todos os recursos, precisamos verificar seu endereço de email.</p>

                <p>Clique no botão abaixo para confirmar que este email pertence a você:</p>
            </div>

            <div class="button-container">
                <a href="{{ $verificationUrl }}" class="verify-button">
                    Verificar Email
                </a>
            </div>

            <div class="warning">
                <strong>Importante:</strong> Se você não criou uma conta em {{ config('app.name') }}, pode ignorar este email com segurança.
            </div>

            <div class="message">
                <p><strong>Problemas com o botão?</strong> Se o botão não funcionar, copie e cole o link abaixo no seu navegador:</p>
                <p style="word-break: break-all; background-color: #f7fafc; padding: 10px; border-radius: 4px; font-size: 12px; color: #4a5568;">
                    {{ $verificationUrl }}
                </p>
            </div>
        </div>

        <div class="footer">
            <p><strong class="app-name">{{ config('app.name') }}</strong></p>
            <p>Este email foi enviado para {{ $user->email }}</p>
            <p>Se você não solicitou esta verificação, pode ignorar este email.</p>
            <p style="margin-top: 15px; font-size: 12px; color: #a0aec0;">
                © {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.
            </p>
        </div>
    </div>
</body>
</html>
