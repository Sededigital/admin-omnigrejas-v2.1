<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Ativado - {{ config('app.name') }}</title>
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
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
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
            background: linear-gradient(90deg, #059669, #10b981, #059669);
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
        .success-alert {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border: 1px solid #10b981;
            border-radius: 12px;
            padding: 20px;
            margin: 24px 0;
            color: #065f46;
            font-size: 14px;
            line-height: 1.6;
            position: relative;
        }
        .success-alert::before {
            content: '✅';
            font-size: 20px;
            position: absolute;
            top: 20px;
            left: 20px;
        }
        .success-content {
            margin-left: 35px;
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
        .codes-section {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
        }
        .codes-section h3 {
            color: #1e293b;
            margin: 0 0 16px;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        .codes-section h3::before {
            content: '🔐';
            margin-right: 8px;
        }
        .code-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 12px;
            margin: 16px 0;
        }
        .code-item {
            font-family: 'Monaco', 'Menlo', monospace;
            background: white;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            font-weight: 600;
            border: 2px solid #e2e8f0;
            color: #1e293b;
            font-size: 14px;
            letter-spacing: 1px;
            transition: all 0.2s ease;
        }
        .code-item:hover {
            border-color: #059669;
            box-shadow: 0 2px 4px rgba(5, 150, 105, 0.1);
        }
        .next-steps {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #0ea5e9;
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
        }
        .next-steps h3 {
            color: #0c4a6e;
            margin: 0 0 16px;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        .next-steps h3::before {
            content: '📋';
            margin-right: 8px;
        }
        .next-steps ul {
            margin: 0;
            padding-left: 20px;
            color: #0c4a6e;
        }
        .next-steps li {
            margin-bottom: 8px;
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
            .codes-section, .next-steps {
                padding: 20px;
            }
            .code-grid {
                grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🔐</div>
            <h1>{{ config('app.name') }}</h1>
            <p>Autenticação de Dois Fatores Ativada</p>
        </div>

        <div class="content">
            <div class="greeting">
                Olá, {{ $user->name }}!
            </div>

            <div class="success-alert">
                <div class="success-content">
                    <strong>Parabéns!</strong> Sua autenticação de dois fatores foi ativada com sucesso. Sua conta agora está mais segura!
                </div>
            </div>

            <div class="message">
                <p>A partir de agora, além da sua senha, você precisará de um código de verificação para acessar sua conta. Isso adiciona uma camada extra de proteção contra acessos não autorizados.</p>
            </div>

            @if(!empty($recoveryCodes))
            <div class="codes-section">
                <h3>Códigos de Recuperação</h3>
                <div class="warning">
                    <div class="warning-content">
                        <strong>Muito Importante:</strong> Guarde estes códigos em um local seguro e offline. Eles são sua única forma de acessar a conta se você perder seu dispositivo 2FA.
                    </div>
                </div>
                <div class="code-grid">
                    @foreach($recoveryCodes as $code)
                        <div class="code-item">{{ $code }}</div>
                    @endforeach
                </div>
                <p style="color: #6b7280; font-size: 14px; margin-top: 16px;">
                    <strong>💡 Dica:</strong> Imprima estes códigos ou salve-os em um gerenciador de senhas. Cada código só pode ser usado uma vez.
                </p>
            </div>
            @endif

            <div class="next-steps">
                <h3>Próximos Passos</h3>
                <ul>
                    <li>Teste seu 2FA fazendo logout e login novamente</li>
                    <li>Configure um aplicativo autenticador de backup (Google Authenticator, Authy, etc.)</li>
                    <li>Mantenha seus códigos de recuperação em local seguro</li>
                    <li>Considere configurar métodos de recuperação alternativos</li>
                    <li>Informe-se sobre as melhores práticas de segurança</li>
                </ul>
            </div>

            <div class="signature">
                <p>Se você não solicitou esta ativação ou suspeita de atividade não autorizada, entre em contato conosco imediatamente.</p>
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
