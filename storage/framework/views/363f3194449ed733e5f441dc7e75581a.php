<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Período de Teste Expirado - <?php echo e(config('app.name')); ?></title>
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
        .alert {
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
        .alert::before {
            content: '🚫';
            font-size: 20px;
            position: absolute;
            top: 20px;
            left: 20px;
        }
        .alert-content {
            margin-left: 35px;
        }
        .trial-summary {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
        }
        .trial-summary h3 {
            color: #1e293b;
            margin: 0 0 16px;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        .trial-summary h3::before {
            content: '📊';
            margin-right: 8px;
        }
        .trial-summary p {
            margin: 8px 0;
            color: #475569;
        }
        .trial-summary .highlight {
            color: #dc2626;
            font-weight: 600;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 16px;
            margin-top: 16px;
        }
        .stat-item {
            background: white;
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            text-align: center;
        }
        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: #dc2626;
            margin-bottom: 4px;
        }
        .stat-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .button-container {
            text-align: center;
            margin: 36px 0;
        }
        .upgrade-button {
            display: inline-block;
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            text-decoration: none;
            padding: 16px 36px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);
        }
        .upgrade-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(220, 38, 38, 0.3);
        }
        .reactivate-button {
            display: inline-block;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            margin-left: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.2);
        }
        .reactivate-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
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
            .trial-summary {
                padding: 20px;
            }
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            .upgrade-button, .reactivate-button {
                display: block;
                width: 100%;
                margin: 8px 0;
            }
            .reactivate-button {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">⏰</div>
            <h1><?php echo e(config('app.name')); ?></h1>
            <p>Período de Teste Expirado</p>
        </div>

        <div class="content">
            <div class="greeting">
                Olá, <?php echo e($nomeUsuario); ?>!
            </div>

            <div class="alert">
                <div class="alert-content">
                    <strong>Seu período de teste expirou!</strong> O acesso aos recursos do <?php echo e(config('app.name')); ?> foi temporariamente suspenso.
                </div>
            </div>

            <div class="message">
                <p>Lamentamos informar que seu período de teste gratuito de 10 dias expirou em <strong><?php echo e($dataExpiracao); ?></strong>. Durante esse período, você aproveitou as funcionalidades do sistema para gerenciar sua igreja <strong><?php echo e($igrejaNome); ?></strong>.</p>
            </div>

            <div class="trial-summary">
                <h3>Resumo do Seu Período de Teste</h3>
                <p><strong>Igreja:</strong> <?php echo e($igrejaNome); ?></p>
                <p><strong>Data de Expiração:</strong> <span class="highlight"><?php echo e($dataExpiracao); ?></span></p>
                <p><strong>Dias de Uso:</strong> <span class="highlight"><?php echo e($diasAtivos); ?> dias</span></p>
                <p><strong>Status:</strong> Expirado</p>

                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo e($totalMembros); ?></div>
                        <div class="stat-label">Membros</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo e($totalPosts); ?></div>
                        <div class="stat-label">Posts</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo e($totalEventos); ?></div>
                        <div class="stat-label">Eventos</div>
                    </div>
                </div>
            </div>

            <?php if($podeReativar): ?>
            <div class="message">
                <p>Você ainda tem <strong><?php echo e($trial->periodo_graca_dias ?? 7); ?> dias</strong> para reativar sua conta e continuar usando o sistema. Após esse período, todos os dados serão permanentemente removidos.</p>
            </div>

            <div class="button-container">
                <a href="<?php echo e(url('/e-commerce/subscription-upgrade')); ?>" class="upgrade-button">
                    Fazer Upgrade Agora
                </a>
                <a href="<?php echo e(url('/e-commerce/trial-solicitar')); ?>" class="reactivate-button">
                    Reativar Trial
                </a>
            </div>
            <?php else: ?>
            <div class="message">
                <p>Para continuar usando o <?php echo e(config('app.name')); ?> e manter seus dados, é necessário fazer um upgrade para um plano pago.</p>
            </div>

            <div class="button-container">
                <a href="<?php echo e(url('/e-commerce/subscription-upgrade')); ?>" class="upgrade-button">
                    Escolher Plano Pago
                </a>
            </div>
            <?php endif; ?>

            <div class="message">
                <p><strong>Problemas com os botões?</strong> Se os botões não funcionarem, acesse diretamente:</p>
                <p style="word-break: break-all; background-color: #f7fafc; padding: 10px; border-radius: 4px; font-size: 12px; color: #4a5568;">
                    <?php echo e(url('/e-commerce/subscription-upgrade')); ?>

                </p>
            </div>

            <div class="signature">
                <p>Agradecemos por testar o <?php echo e(config('app.name')); ?>. Esperamos que tenha tido uma boa experiência e aguardamos seu retorno!</p>
                <p><strong>Equipe <?php echo e(config('app.name')); ?></strong></p>
            </div>
        </div>

        <div class="footer">
            <p><strong class="app-name"><?php echo e(config('app.name')); ?></strong></p>
            <p>Este email foi enviado para <?php echo e($trial->user->email); ?></p>
            <p>Este é um email automático, não responda a esta mensagem.</p>
            <p style="margin-top: 15px; font-size: 12px; color: #a0aec0;">
                © <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. Todos os direitos reservados.
            </p>
        </div>
    </div>
</body>
</html><?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/emails/trial-expirado.blade.php ENDPATH**/ ?>