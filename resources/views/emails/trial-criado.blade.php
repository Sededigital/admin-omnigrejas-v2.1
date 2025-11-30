<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo ao {{ config('app.name') }} - Seu período de teste começou!</title>
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
        .trial-info {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
        }
        .trial-info h3 {
            color: #1e293b;
            margin: 0 0 16px;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        .trial-info h3::before {
            content: '📊';
            margin-right: 8px;
        }
        .trial-info p {
            margin: 8px 0;
            color: #475569;
        }
        .trial-info .highlight {
            color: #059669;
            font-weight: 600;
        }
        .credentials-section {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #0ea5e9;
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
        }
        .credentials-section h3 {
            color: #0c4a6e;
            margin: 0 0 16px;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        .credentials-section h3::before {
            content: '🔑';
            margin-right: 8px;
        }
        .credentials-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }
        .credential-item {
            background: white;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .credential-label {
            font-weight: 600;
            color: #374151;
            font-size: 14px;
            margin-bottom: 4px;
        }
        .credential-value {
            font-family: 'Monaco', 'Menlo', monospace;
            background-color: #f8fafc;
            padding: 8px 12px;
            border-radius: 6px;
            color: #1e293b;
            font-weight: 600;
            font-size: 16px;
            border: 1px solid #e2e8f0;
            word-break: break-all;
        }
        .button-container {
            text-align: center;
            margin: 36px 0;
        }
        .login-button {
            display: inline-block;
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: white;
            text-decoration: none;
            padding: 16px 36px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2);
        }
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(5, 150, 105, 0.3);
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
            .trial-info, .credentials-section {
                padding: 20px;
            }
            .credentials-grid {
                gap: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🎉</div>
            <h1>{{ config('app.name') }}</h1>
            <p>Bem-vindo! Seu período de teste começou</p>
        </div>

        <div class="content">
            <div class="greeting">
                Olá, {{ $nomeUsuario }}!
            </div>

            <div class="success-alert">
                <div class="success-content">
                    <strong>Parabéns!</strong> Sua conta foi criada com sucesso e seu período de teste gratuito de {{ $periodoDias }} dias já começou!
                </div>
            </div>

            <div class="message">
                <p>Você agora tem acesso completo ao <strong>{{ config('app.name') }}</strong> para gerenciar sua igreja <strong>{{ $igrejaNome }}</strong>. Utilize este período para conhecer todas as funcionalidades e ver como podemos ajudar no crescimento da sua comunidade.</p>
            </div>

            <div class="trial-info">
                <h3>Informações do Seu Período de Teste</h3>
                <p><strong>Igreja:</strong> {{ $igrejaNome }}</p>
                <p><strong>Data de Início:</strong> <span class="highlight">{{ $dataInicio }}</span></p>
                <p><strong>Data de Expiração:</strong> <span class="highlight">{{ $dataFim }}</span></p>
                <p><strong>Dias de Teste:</strong> <span class="highlight">10 dias</span></p>
                <p><strong>Status:</strong> Ativo</p>
            </div>

            <div class="credentials-section">
                <h3>Seus Dados de Acesso</h3>
                <div class="credentials-grid">
                    <div class="credential-item">
                        <div class="credential-label">Email:</div>
                        <div class="credential-value">{{ $emailUsuario }}</div>
                    </div>
                    <div class="credential-item">
                        <div class="credential-label">Senha Temporária:</div>
                        <div class="credential-value">{{ $senhaTemporaria }}</div>
                    </div>
                </div>
                <p style="color: #0c4a6e; font-size: 14px; margin-top: 16px; font-style: italic;">
                    <strong>💡 Importante:</strong> Recomendamos alterar sua senha no primeiro acesso para maior segurança.
                </p>
            </div>

            <div class="warning">
                <div class="warning-content">
                    <strong>Lembrete:</strong> Este é um período de teste gratuito. Para continuar usando o sistema após {{ $dataFim }}, será necessário fazer um upgrade para um plano pago.
                </div>
            </div>

            <div class="button-container">
                <a href="{{ $loginUrl }}" class="login-button">
                    Acessar Minha Conta Agora
                </a>
            </div>

            <div class="message">
                <p><strong>Problemas com o botão?</strong> Se o botão não funcionar, acesse diretamente:</p>
                <p style="word-break: break-all; background-color: #f7fafc; padding: 10px; border-radius: 4px; font-size: 12px; color: #4a5568;">
                    {{ $loginUrl }}
                </p>
            </div>

            <div class="signature">
                <p>Esperamos que você tenha uma excelente experiência conosco. Estamos aqui para ajudar!</p>
                <p><strong>Equipe {{ config('app.name') }}</strong></p>
            </div>
        </div>

        <div class="footer">
            <p><strong class="app-name">{{ config('app.name') }}</strong></p>
            <p>Este email foi enviado para {{ $emailUsuario }}</p>
            <p>Este é um email automático, não responda a esta mensagem.</p>
            <p style="margin-top: 15px; font-size: 12px; color: #a0aec0;">
                © {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.
            </p>
        </div>
    </div>
</body>
</html>