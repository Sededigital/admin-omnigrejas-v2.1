<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de Membro - <?php echo e(config('app.name')); ?></title>
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
            /* Garante que o valor quebre em telas pequenas */
            .code-box-value { font-size: 24px !important; letter-spacing: 3px !important; }
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

                    <!-- Bloco do Cabeçalho (Fundo Azul) -->
                    <tr>
                        <td class="header-padding" align="center" style="background-image: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%); color: white; padding: 40px 30px; text-align: center; border-radius: 12px 12px 0 0;">
                            <!-- Ícone -->
                            <table cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto 20px;">
                                <tr>
                                    <td style="width: 60px; height: 60px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; text-align: center; vertical-align: middle; font-size: 24px;">
                                        📄
                                    </td>
                                </tr>
                            </table>

                            <h1 style="margin: 0; font-size: 28px; font-weight: 700; letter-spacing: -0.5px; color: #ffffff;">
                                Ficha de Membro
                            </h1>
                            <p style="margin: 8px 0 0 0; font-size: 25px; font-weight: 400; color: rgba(255, 255, 255, 0.9);">
                                <?php echo e($igreja->nome); ?>

                            </p>
                            <!-- Pequena linha decorativa -->
                            <div style="height: 6px; background: linear-gradient(90deg, #2563eb, #3b82f6, #2563eb); opacity: 0.7; width: 100%; margin-top: 20px;"></div>
                        </td>
                    </tr>

                    <!-- Bloco do Conteúdo -->
                    <tr>
                        <td class="content-padding" style="padding: 40px 30px; background-color: #ffffff;">

                            <!-- Saudação -->
                            <p style="font-size: 20px; font-weight: 700; color: #111827; margin: 0 0 24px 0; letter-spacing: -0.3px;">
                                Olá, <?php echo e($member->user->name); ?>!
                            </p>

                            <!-- Mensagem Principal -->
                            <p style="font-size: 16px; color: #374151; margin: 0 0 16px 0; line-height: 1.7;">
                                Sua ficha de membro da igreja <strong style="color: #2563eb;"><?php echo e($igreja->nome); ?></strong> foi gerada com sucesso.
                            </p>
                            <p style="font-size: 16px; color: #374151; margin: 0 0 32px 0; line-height: 1.7;">
                                Este documento contém todas as suas informações eclesiásticas oficiais. Você pode utilizá-lo para identificação e participação em atividades da igreja.
                            </p>
                            <p style="font-size: 16px; color: #374151; margin: 0 0 32px 0; line-height: 1.7;">
                                <strong>Anexo:</strong> A ficha de membro em formato PDF está anexada a este email (verifique a seção de anexos abaixo).
                            </p>

                            <!-- Bloco de Aviso (Azul - Tabela Robusta) -->
                            <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border: 1px solid #3b82f6; border-radius: 12px; margin: 24px 0;">
                                <tr>
                                    <td style="padding: 20px; color: #1e40af; font-size: 14px; line-height: 1.6;">
                                        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="font-size: 20px; width: 35px; vertical-align: top;">ℹ️</td>
                                                <td style="padding-left: 5px;">
                                                    <strong style="color: #1e40af;">Importante:</strong> Este documento é oficial e contém informações confidenciais. Mantenha-o seguro e apresente-o quando solicitado pela liderança eclesiástica.
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Mensagem de Rodapé -->
                            <p style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb; color: #6b7280; font-style: italic; font-size: 14px;">
                                Se você não solicitou esta ficha, por favor, entre em contato com a liderança da igreja.
                            </p>
                            <p style="margin: 0; color: #111827; font-size: 14px;">
                                <strong style="font-weight: 700;">Atenciosamente,<br>Equipe <?php echo e($igreja->nome); ?>, Admin OmnIgrejas</strong>
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
                                Este email foi enviado para <?php echo e($member->user->email); ?>

                            </p>
                            <p style="margin: 6px 0; color: #6b7280; font-size: 14px;">
                                Este é um email automático, não responda a esta mensagem.
                            </p>
                            <p style="margin-top: 15px; font-size: 12px; color: #a0aec0;">
                                &copy; <?php echo e(date('Y')); ?> OmnIgrejas. Todos os direitos reservados.
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
<?php /**PATH C:\laragon\www\admin-omnigrejas-v2.1\resources\views/emails/member-card.blade.php ENDPATH**/ ?>