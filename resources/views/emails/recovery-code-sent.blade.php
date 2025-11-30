Re<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Recuperação 2FA - {{ config('app.name') }}</title>
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
        .urgent-alert {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 2px solid #f87171;
            border-radius: 12px;
            padding: 20px;
            margin: 24px 0;
            color: #991b1b;
            font-size: 15px;
            line-height: 1.6;
            position: relative;
        }
        .urgent-alert::before {
            content: '🚨';
            font-size: 20px;
            position: absolute;
            top: 20px;
            left: 20px;
        }
        .urgent-content {
            margin-left: 35px;
        }
        .code-section {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 3px solid #e2e8f0;
            border-radius: 16px;
            padding: 32px;
            margin: 32px 0;
            text-align: center;
            position: relative;
        }
        .code-section::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #dc2626, #ef4444, #dc2626);
            border-radius: 16px;
            z-index: -1;
        }
        .code-section h3 {
            color: #1e293b;
            margin: 0 0 20px;
            font-size: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .code-section h3::before {
            content: '🔑';
            margin-right: 8px;
        }
        .recovery-code {
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 32px;
            font-weight: 700;
            color: #dc2626;
            letter-spacing: 4px;
            background: white;
            padding: 20px 24px;
            border-radius: 12px;
            display: inline-block;
            border: 2px solid #dc2626;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15);
            margin: 16px 0;
            user-select: all;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .recovery-code:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 16px rgba(220, 38, 38, 0.25);
        }
        .code-hint {
            color: #6b7280;
            font-size: 14px;
            margin-top: 12px;
            font-style: italic;
        }
        .security-warning {
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
        .security-warning::before {
            content: '🔒';
            font-size: 20px;
            position: absolute;
            top: 20px;
            left: 20px;
        }
        .security-content {
            margin-left: 35px;
        }
        .security-warning ul {
            margin: 12px 0;
            padding-left: 20px;
        }
        .security-warning li {
            margin-bottom: 6px;
        }
        .instructions {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #0ea5e9;
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
        }
        .instructions h3 {
            color: #0c4a6e;
            margin: 0 0 16px;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        .instructions h3::before {
            content: '📋';
            margin-right: 8px;
        }
        .instructions ol {
            margin: 0;
            padding-left: 20px;
            color: #0c4a6e;
        }
        .instructions li {
            margin-bottom: 8px;
            font-weight: 500;
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
            .code-section {
                padding: 24px 16px;
            }
            .recovery-code {
                font-size: 24px;
                letter-spacing: 2px;
                padding: 16px 20px;
            }
            .instructions {
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
            <p>Código de Recuperação 2FA</p>
        </div>

        <div class="content">
            <div class="greeting">
                Olá, {{ $user->name }}!
            </div>

            <div class="message">
                <p>Você solicitou um código de recuperação para acessar sua conta protegida por autenticação de dois fatores. Este código permitirá que você acesse sua conta mesmo sem seu dispositivo 2FA.</p>
            </div>

            <div class="urgent-alert">
                <div class="urgent-content">
                    <strong>URGENTE:</strong> Este código é válido apenas para este acesso e expira em 15 minutos. Use-o imediatamente para entrar em sua conta.
                </div>
            </div>

            <div class="code-section">
                <h3>Seu Código de Recuperação</h3>
                <div class="recovery-code">{{ $recoveryCode }}</div>
                <div class="code-hint">Clique no código para selecioná-lo</div>
            </div>

            <div class="security-warning">
                <div class="security-content">
                    <strong>Medidas de Segurança:</strong>
                    <ul>
                        <li>Este código será removido automaticamente após o uso</li>
                        <li>Nunca compartilhe este código com ninguém</li>
                        <li>Se você não solicitou este código, altere sua senha imediatamente</li>
                        <li>Este código só funciona uma vez</li>
                    </ul>
                </div>
            </div>

            <div class="instructions">
                <h3>Como usar este código</h3>
                <ol>
                    <li>Volte para a página de login 2FA</li>
                    <li>Clique em "Usar código de recuperação" ou "Problemas com 2FA?"</li>
                    <li>Digite ou cole o código acima no campo indicado</li>
                    <li>Clique em "Verificar" ou "Entrar"</li>
                    <li>Você será direcionado para sua conta</li>
                </ol>
            </div>

            <div class="message">
                <p><strong>💡 Dica Importante:</strong> Após acessar sua conta, considere gerar novos códigos de recuperação para manter sua segurança em dia.</p>
            </div>

            <div class="signature">
                <p>Se você não solicitou este código, entre em contato conosco imediatamente.</p>
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
