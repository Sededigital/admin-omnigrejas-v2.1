<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credenciais de Acesso - <?php echo e(config('app.name')); ?></title>
    <!-- CSS para clientes que suportam, mas a prioridade é o inline -->
    <style type="text/css">
        body, html { margin: 0; padding: 0; }
        a { text-decoration: none; }
        /* Reset para Outlook/MSO */
        .ExternalClass { width: 100%; }
        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }
        /* Responsividade */
        @media only screen and (max-width: 600px) {
            .full-width { width: 100% !important; min-width: 100% !important; }
            .content-padding { padding: 30px 20px !important; }
            .header-padding { padding: 30px 20px !important; }
            .credential-value-cell { word-break: break-all; } /* Garante que o valor quebre em telas pequenas */
        }
    </style>
</head>
<body style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #1a1a1a; margin: 0; padding: 0; background-color: #f5f5f5; -webkit-font-smoothing: antialiased;">

    <!-- Tabela Externa: Background e Centralização -->
    <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f5f5f5;">
        <tr>
            <td align="center" style="padding: 20px 10px;">
                <!-- Tabela Container Principal -->
                <table class="container full-width" width="600" cellspacing="0" cellpadding="0" border="0" align="center" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); overflow: hidden;">

                    <!-- Bloco do Cabeçalho (Fundo Verde) -->
                    <tr>
                        <td class="header-padding" align="center" style="background-image: linear-gradient(135deg, #059669 0%, #10b981 100%); color: white; padding: 40px 30px; text-align: center; border-radius: 12px 12px 0 0;">
                            <!-- Ícone -->
                            <table cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto 20px;">
                                <tr>
                                    <td style="width: 60px; height: 60px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; text-align: center; vertical-align: middle; font-size: 24px;">
                                        👋
                                    </td>
                                </tr>
                            </table>

                            <h1 style="margin: 0; font-size: 28px; font-weight: 700; letter-spacing: -0.5px; color: #ffffff;">
                                <?php echo e(config('app.name')); ?>

                            </h1>
                            <p style="margin: 8px 0 0 0; font-size: 16px; font-weight: 400; color: rgba(255, 255, 255, 0.9);">
                                Bem-vindo à <?php echo e($igrejaNome); ?>

                            </p>
                            <!-- Pequena linha decorativa -->
                            <div style="height: 6px; background: linear-gradient(90deg, #059669, #10b981, #059669); opacity: 0.7; width: 100%; margin-top: 20px;"></div>
                        </td>
                    </tr>

                    <!-- Bloco do Conteúdo -->
                    <tr>
                        <td class="content-padding" style="padding: 40px 30px; background-color: #ffffff;">

                            <!-- Saudação -->
                            <p style="font-size: 20px; font-weight: 700; color: #111827; margin: 0 0 24px 0; letter-spacing: -0.3px;">
                                Olá, <?php echo e($user->name); ?>!
                            </p>

                            <!-- Mensagem Principal -->
                            <p style="font-size: 16px; color: #374151; margin: 0 0 16px 0; line-height: 1.7;">
                                Parabéns! Você foi cadastrado como membro da <strong style="color: #059669;"><?php echo e($igrejaNome); ?></strong> em nosso sistema. Suas credenciais de acesso foram criadas com sucesso.
                            </p>
                            <p style="font-size: 16px; color: #374151; margin: 0 0 32px 0; line-height: 1.7;">
                                Guarde estas informações em um local seguro e não as compartilhe com ninguém:
                            </p>

                            <!-- Seção de Credenciais (FIXADA com Tabela) -->
                            <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border: 2px solid #e2e8f0; border-radius: 12px; padding: 0; margin: 24px 0;">
                                <tr>
                                    <td style="padding: 24px;">
                                        <!-- Título da Seção -->
                                        <h3 style="color: #1e293b; margin: 0 0 16px; font-size: 18px; font-weight: 600;">
                                            <span style="display: inline-block; margin-right: 8px;">🔑</span> Suas Credenciais de Acesso
                                        </h3>
                                        <!-- Tabela das Credenciais (Alinhamento em uma única linha por item) -->
                                        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom: 0;">
                                            <!-- Linha 1: Email -->
                                            <tr>
                                                <td style="width: 30%; font-weight: 600; color: #374151; font-size: 14px; padding-right: 15px; padding-bottom: 16px; vertical-align: middle;">
                                                    Email:
                                                </td>
                                                <td class="credential-value-cell" style="width: 70%; font-family: 'Monaco', 'Menlo', monospace; background-color: #f1f5f9; padding: 12px 15px; border-radius: 8px; color: #1e293b; font-weight: 600; font-size: 16px; letter-spacing: 0.5px; border: 1px solid #cbd5e1; vertical-align: middle;">
                                                    <?php echo e($user->email); ?>

                                                </td>
                                            </tr>
                                            <!-- Linha 2: Senha Temporária -->
                                            <tr>
                                                <td style="width: 30%; font-weight: 600; color: #374151; font-size: 14px; padding-right: 15px; vertical-align: middle;">
                                                    Senha Temporária:
                                                </td>
                                                <td class="credential-value-cell" style="width: 70%; font-family: 'Monaco', 'Menlo', monospace; background-color: #f1f5f9; padding: 12px 15px; border-radius: 8px; color: #1e293b; font-weight: 600; font-size: 16px; letter-spacing: 1px; border: 1px solid #cbd5e1; vertical-align: middle;">
                                                    <?php echo e($plainPassword); ?>

                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <!-- Fim Seção de Credenciais -->

                            <!-- Bloco de Aviso (Amarelo - Tabela Robusta) -->
                            <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 1px solid #f59e0b; border-radius: 12px; margin: 24px 0;">
                                <tr>
                                    <td style="padding: 20px; color: #92400e; font-size: 14px; line-height: 1.6;">
                                        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="font-size: 20px; width: 35px; vertical-align: top;">⚠️</td>
                                                <td style="padding-left: 5px;">
                                                    <strong style="color: #92400e;">Importante:</strong> Esta é uma senha temporária. Recomendamos que você faça login e **altere sua senha imediatamente** após o primeiro acesso.
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Botão CTA (Call to Action) - Tabela para garantia -->
                            <table width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top: 36px; margin-bottom: 36px;">
                                <tr>
                                    <td align="center">
                                        <!-- O link do botão com estilos inline -->
                                        <a href="<?php echo e($loginUrl); ?>" target="_blank" style="display: inline-block; background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: white; text-decoration: none; padding: 16px 36px; border-radius: 8px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2); transition: all 0.3s ease;">
                                            Fazer Login Agora
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Link em Caso de Problemas -->
                            <p style="font-size: 16px; color: #374151; margin: 0 0 10px 0;">
                                <strong style="color: #111827;">Problemas com o botão?</strong> Se o botão não funcionar, acesse diretamente:
                            </p>
                            <p style="font-size: 16px; color: #059669; margin: 0 0 32px 0;">
                                <a href="<?php echo e($loginUrl); ?>" style="color: #059669; text-decoration: underline;"><?php echo e($loginUrl); ?></a>
                            </p>

                            <!-- Seção de Dicas de Segurança (Azul - Tabela Robusta) -->
                            <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 1px solid #0ea5e9; border-radius: 12px; padding: 0; margin: 24px 0;">
                                <tr>
                                    <td style="padding: 24px; color: #0c4a6e; font-size: 14px;">
                                        <h3 style="color: #0c4a6e; margin: 0 0 16px; font-size: 18px; font-weight: 600;">
                                            <span style="display: inline-block; margin-right: 8px;">🛡️</span> Dicas de Segurança
                                        </h3>
                                        <ul style="margin: 0; padding-left: 20px; color: #0c4a6e; list-style-type: disc;">
                                            <li style="margin-bottom: 8px;">Altere sua senha temporária assim que fizer login</li>
                                            <li style="margin-bottom: 8px;">Use uma senha forte com pelo menos 8 caracteres</li>
                                            <li style="margin-bottom: 8px;">Não compartilhe suas credenciais com ninguém</li>
                                            <li style="margin-bottom: 8px;">Ative a autenticação de dois fatores quando disponível</li>
                                            <li style="margin-bottom: 0;">Mantenha seu email atualizado para recuperação de conta</li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                            <!-- Fim Seção Dicas de Segurança -->

                            <!-- Assinatura -->
                            <p style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb; color: #6b7280; font-style: italic; font-size: 14px;">
                                Se você não solicitou este cadastro ou tem dúvidas, entre em contato conosco.
                            </p>
                            <p style="margin: 0; color: #111827; font-size: 14px;">
                                <strong style="font-weight: 700;">Equipe da <?php echo e($igrejaNome); ?><br><?php echo e(config('app.name')); ?></strong>
                            </p>

                        </td>
                    </tr>

                    <!-- Bloco do Rodapé -->
                    <tr>
                        <td align="center" style="background-color: #f8fafc; padding: 24px 30px; text-align: center; border-top: 1px solid #e5e7eb; border-radius: 0 0 12px 12px;">
                            <p style="margin: 6px 0; color: #6b7280; font-size: 14px;">
                                <strong style="font-weight: 700; color: #111827; letter-spacing: -0.3px;"><?php echo e(config('app.name')); ?></strong>
                            </p>
                            <p style="margin: 6px 0; color: #6b7280; font-size: 14px;">
                                Este email foi enviado para <?php echo e($user->email); ?>

                            </p>
                            <p style="margin: 6px 0; color: #6b7280; font-size: 14px;">
                                Este é um email automático, não responda a esta mensagem.
                            </p>
                            <p style="margin-top: 15px; font-size: 12px; color: #a0aec0;">
                                &copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. Todos os direitos reservados.
                            </p>
                        </td>
                    </tr>

                </table>
                <!-- Fim Tabela Container Principal -->
            </td>
        </tr>
    </table>
    <!-- Fim Tabela Externa -->

</body>
</html>
<?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/emails/member-credentials.blade.php ENDPATH**/ ?>