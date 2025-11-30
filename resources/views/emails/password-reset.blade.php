<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinição de Senha - {{ config('app.name') }}</title>
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
        .button-container {
            text-align: center;
            margin: 36px 0;
            color: #f0f9ff;
        }
        .reset-button {
            display: inline-block;
            background: linear-gradient(135deg, #6f2323 0%, #a71a1a 100%);
            color: white;
            text-decoration: none;
            padding: 16px 36px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);
        }
        .reset-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(220, 38, 38, 0.3);
        }
        .warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #f59e0b;
            border-radius: 12px;
            padding: 20px;
            margin: 24px 0;
            color: #92400e;
            font-size: 14px;
            line-height: 1.6;
            position: relative;
        }
        .warning::before {
            content: '⚠️';
            font-size: 20px;
            position: absolute;
            top: 20px;
            left: 20px;
        }
        .warning-content {
            margin-left: 35px;
        }
        .security-tips {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #0ea5e9;
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
        }
        .security-tips h3 {
            color: #0c4a6e;
            margin: 0 0 16px;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        .security-tips h3::before {
            content: '🛡️';
            margin-right: 8px;
        }
        .security-tips ul {
            margin: 0;
            padding-left: 20px;
            color: #0c4a6e;
        }
        .security-tips li {
            margin-bottom: 8px;
        }
        .url-container {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            margin: 24px 0;
            word-break: break-all;
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 12px;
            color: #4a5568;
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
            .security-tips {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🔑</div>
            <h1>{{ config('app.name') }}</h1>
            <p>Redefinição de Senha</p>
        </div>

        <div class="content">
            <div class="greeting">
                Olá, {{ $user->name }}!
            </div>

            <div class="message">
                <p>Recebemos uma solicitação para redefinir a senha da sua conta. Para sua segurança, criamos um link temporário que permitirá que você defina uma nova senha.</p>
            </div>

            <div class="warning">
                <div class="warning-content">
                    <strong>Importante:</strong> Este link é válido por apenas 60 minutos por motivos de segurança. Se você não solicitou esta redefinição, pode ignorar este email com segurança.
                </div>
            </div>

            <div class="button-container">
                <a href="{{ $resetUrl }}" class="reset-button">
                    Redefinir Minha Senha
                </a>
            </div>

            <div class="message">
                <p><strong>Problemas com o botão?</strong> Se o botão não funcionar, copie e cole o link abaixo no seu navegador:</p>
                <div class="url-container">
                    {{ $resetUrl }}
                </div>
            </div>

            <div class="security-tips">
                <h3>Dicas de Segurança</h3>
                <ul>
                    <li>Use uma senha forte com pelo menos 8 caracteres</li>
                    <li>Combine letras maiúsculas, minúsculas, números e símbolos</li>
                    <li>Não reutilize senhas de outros sites ou serviços</li>
                    <li>Considere ativar a autenticação de dois fatores</li>
                    <li>Mantenha suas informações de recuperação atualizadas</li>
                </ul>
            </div>

            <div class="signature">
                <p>Se você não solicitou esta redefinição, sua senha permanecerá inalterada e sua conta continuará segura.</p>
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
