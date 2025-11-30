<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Desativado - {{ config('app.name') }}</title>
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
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
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
            background: linear-gradient(90deg, #dc2626, #ef4444, #dc2626);
            opacity: 0.7;
        }
        .icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 24px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 8px 0 0;
            opacity: 0.9;
            font-size: 16px;
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
        .security-alert {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 1px solid #f87171;
            border-radius: 12px;
            padding: 20px;
            margin: 24px 0;
            color: #991b1b;
            font-size: 14px;
            line-height: 1.6;
            position: relative;
        }
        .security-alert::before {
            content: '🚨';
            font-size: 20px;
            position: absolute;
            top: 20px;
            left: 20px;
        }
        .alert-content {
            margin-left: 35px;
        }
        .impact-section {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
        }
        .impact-section h3 {
            color: #1e293b;
            margin: 0 0 16px;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        .impact-section h3::before {
            content: '📋';
            margin-right: 8px;
        }
        .impact-section ul {
            margin: 0;
            padding-left: 20px;
            color: #475569;
        }
        .impact-section li {
            margin-bottom: 8px;
        }
        .recommendations {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #0ea5e9;
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
        }
        .recommendations h3 {
            color: #0c4a6e;
            margin: 0 0 16px;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        .recommendations h3::before {
            content: '🛡️';
            margin-right: 8px;
        }
        .recommendations ul {
            margin: 0;
            padding-left: 20px;
            color: #0c4a6e;
        }
        .recommendations li {
            margin-bottom: 8px;
        }
        .urgent-notice {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #f59e0b;
            border-radius: 12px;
            padding: 20px;
            margin: 24px 0;
            color: #92400e;
            font-size: 15px;
            line-height: 1.6;
            position: relative;
        }
        .urgent-notice::before {
            content: '⚠️';
            font-size: 20px;
            position: absolute;
            top: 20px;
            left: 20px;
        }
        .urgent-content {
            margin-left: 35px;
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
        .app-name {
            font-weight: 700;
            color: #111827;
            letter-spacing: -0.3px;
        }
        .signature {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-style: italic;
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
            .impact-section, .recommendations {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🔓</div>
            <h1>{{ config('app.name') }}</h1>
            <p>Autenticação de Dois Fatores Desativada</p>
        </div>

        <div class="content">
            <div class="greeting">
                Olá, {{ $user->name }}!
            </div>

            <div class="message">
                <p>Informamos que a autenticação de dois fatores (2FA) foi desativada em sua conta. Sua conta agora está protegida apenas por senha.</p>
            </div>

            <div class="security-alert">
                <div class="alert-content">
                    <strong>Aviso de Segurança:</strong> Sua conta perdeu uma camada importante de proteção. Recomendamos fortemente que você reative o 2FA o mais breve possível.
                </div>
            </div>

            <div class="impact-section">
                <h3>O que mudou em sua conta</h3>
                <ul>
                    <li>Sua conta não está mais protegida por autenticação de dois fatores</li>
                    <li>Você pode fazer login apenas com email e senha</li>
                    <li>Os códigos de recuperação anteriores não são mais válidos</li>
                    <li>Sua conta está mais vulnerável a acessos não autorizados</li>
                    <li>Aplicativos autenticadores configurados foram desvinculados</li>
                </ul>
            </div>

            <div class="recommendations">
                <h3>Recomendações Urgentes de Segurança</h3>
                <ul>
                    <li><strong>Reative o 2FA imediatamente</strong> para restaurar a proteção</li>
                    <li>Use uma senha forte e única (mínimo 12 caracteres)</li>
                    <li>Monitore sua conta regularmente por atividades suspeitas</li>
                    <li>Ative notificações de login se disponível</li>
                    <li>Considere usar um gerenciador de senhas</li>
                    <li>Mantenha seu email de recuperação atualizado</li>
                </ul>
            </div>

            <div class="urgent-notice">
                <div class="urgent-content">
                    <strong>Ação Necessária:</strong> Se você não solicitou esta desativação, sua conta pode ter sido comprometida. Entre em contato conosco IMEDIATAMENTE e altere sua senha.
                </div>
            </div>

            <div class="signature">
                <p>Nossa equipe de segurança está sempre disponível para ajudá-lo a proteger sua conta.</p>
                <p><strong>Equipe de Segurança<br>{{ config('app.name') }}</strong></p>
            </div>
        </div>

        <div class="footer">
            <p><strong class="app-name">{{ config('app.name') }}</strong></p>
            <p>Este email foi enviado para {{ $user->email }}</p>
            <p>Este é um email automático, não responda a esta mensagem.</p>
            <p style="margin-top: 15px; font-size: 12px; color: #a0aec0;">
                © {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.
            </p>
        </div>
    </div>
</body>
</html>
