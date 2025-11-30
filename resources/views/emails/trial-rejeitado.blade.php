<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitação de Trial Rejeitada - OmnIgrejas</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            background-color: #f8f9fa;
        }
        .container {
            background-color: #ffffff;
            margin: 20px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #dc3545;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .logo .primary { color: #007bff; }
        .logo .success { color: #28a745; }
        .alert {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .info-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #dc3545;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .rejection-reason {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <span class="primary">Omn</span><span class="success">Igrejas</span>
            </div>
            <h1 style="color: #dc3545; margin: 10px 0;">Solicitação Rejeitada</h1>
            <p>Sua solicitação de período de teste não foi aprovada</p>
        </div>

        <div class="alert">
            <strong>Olá {{ $trialRequest->nome }}!</strong><br>
            Lamentamos informar que sua solicitação de período de teste foi rejeitada.
        </div>

        <div class="info-section">
            <h3 style="margin-top: 0; color: #495057;">Detalhes da Solicitação</h3>
            <p><strong>Nome:</strong> {{ $trialRequest->nome }}</p>
            <p><strong>Email:</strong> {{ $trialRequest->email }}</p>
            <p><strong>Igreja:</strong> {{ $trialRequest->igreja_nome }}</p>
            <p><strong>Data da Solicitação:</strong> {{ $trialRequest->created_at->format('d/m/Y H:i') }}</p>
        </div>

        @if($trialRequest->motivo_rejeicao)
        <div class="rejection-reason">
            <strong>Motivo da Rejeição:</strong><br>
            {{ $trialRequest->motivo_rejeicao }}
        </div>
        @endif

        @if($trialRequest->observacoes)
        <div class="info-section">
            <h4 style="margin-top: 0; color: #495057;">Observações Adicionais</h4>
            <p>{{ $trialRequest->observacoes }}</p>
        </div>
        @endif

        <div style="text-align: center; margin: 30px 0;">
            <p style="margin-bottom: 20px;">
                Ainda interessado em conhecer o OmnIgrejas? Entre em contato conosco para mais informações.
            </p>
            <a href="{{ url('/') }}" class="btn">Visitar Site</a>
            <a href="mailto:suporte@omnigrejas.com" class="btn" style="background-color: #28a745;">Falar com Suporte</a>
        </div>

        <div class="footer">
            <p>
                Esta é uma mensagem automática, por favor não responda este email.<br>
                © {{ date('Y') }} OmnIgrejas. Todos os direitos reservados.
            </p>
        </div>
    </div>
</body>
</html>