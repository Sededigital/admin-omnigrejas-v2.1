<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Período de Teste Expirando - {{ config('app.name') }}</title>
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
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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
            background: linear-gradient(90deg, #f59e0b, #d97706, #f59e0b);
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
        .warning-alert {
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
        .warning-alert::before {
            content: '⚠️';
            font-size: 20px;
            position: absolute;
            top: 20px;
            left: 20px;
        }
        .alert-content {
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
            color: #f59e0b;
            font-weight: 600;
        }
        .button-container {
            text-align: center;
            margin: 36px 0;
        }
        .upgrade-button {
            display: inline-block;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            text-decoration: none;
            padding: 16px 36px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
        }
        .upgrade-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(245, 158, 11, 0.3);
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
            .trial-info {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">⏰</div>
            <h1>{{ config('app.name') }}</h1>
            <p>Período de Teste Expirando</p>
        </div>

        <div class="content">
            <div class="greeting">
                Olá, {{ $nomeUsuario }}!
            </div>

            <div class="message">
                @if($diasRestantes === 1)
                    <p>Amanhã será o último dia do seu período de teste gratuito do <strong>{{ config('app.name') }}</strong>.</p>
                @else
                    <p>Faltam apenas <strong>{{ $diasRestantes }} dias</strong> para o fim do seu período de teste gratuito do <strong>{{ config('app.name') }}</strong>.</p>
                @endif
                <p>Seu acesso expira em <strong>{{ $dataExpiracao }}</strong>. Para continuar aproveitando todas as funcionalidades, considere fazer um upgrade para um plano pago.</p>
            </div>

            <div class="warning-alert">
                <div class="alert-content">
                    <strong>Ação Necessária:</strong> Para não perder seus dados e continuar usando o sistema, faça o upgrade antes da expiração.
                </div>
            </div>

            <div class="trial-info">
                <h3>Resumo do Seu Período de Teste</h3>
                <p><strong>Igreja:</strong> {{ $igrejaNome }}</p>
                <p><strong>Data de Expiração:</strong> <span class="highlight">{{ $dataExpiracao }}</span></p>
                <p><strong>Dias Restantes:</strong> <span class="highlight">{{ $diasRestantes }}</span></p>
                <p><strong>Status:</strong> Ativo</p>
            </div>

            <div class="button-container">
                <a href="{{ url('/e-commerce/subscription-upgrade') }}" class="upgrade-button">
                    Fazer Upgrade Agora
                </a>
            </div>

            <div class="message">
                <p><strong>Problemas com o botão?</strong> Se o botão não funcionar, acesse diretamente:</p>
                <p style="word-break: break-all; background-color: #f7fafc; padding: 10px; border-radius: 4px; font-size: 12px; color: #4a5568;">
                    {{ url('/e-commerce/subscription-upgrade') }}
                </p>
            </div>

            <div class="signature">
                <p>Esperamos que tenha aproveitado bem o período de teste. Estamos aqui para ajudar!</p>
                <p><strong>Equipe {{ config('app.name') }}</strong></p>
            </div>
        </div>

        <div class="footer">
            <p><strong class="app-name">{{ config('app.name') }}</strong></p>
            <p>Este email foi enviado para {{ $trial->user->email }}</p>
            <p>Este é um email automático, não responda a esta mensagem.</p>
            <p style="margin-top: 15px; font-size: 12px; color: #a0aec0;">
                © {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.
            </p>
        </div>
    </div>
</body>
</html>