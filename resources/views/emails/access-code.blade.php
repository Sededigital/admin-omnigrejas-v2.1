<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Acesso - {{ config('app.name') }}</title>
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

                    <!-- Bloco do Cabeçalho (Fundo Verde) -->
                    <tr>
                        <td class="header-padding" align="center" style="background-image: linear-gradient(135deg, #059669 0%, #10b981 100%); color: white; padding: 40px 30px; text-align: center; border-radius: 12px 12px 0 0;">
                            <!-- Ícone -->
                            <table cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto 20px;">
                                <tr>
                                    <td style="width: 60px; height: 60px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; text-align: center; vertical-align: middle; font-size: 24px;">
                                        🔑
                                    </td>
                                </tr>
                            </table>

                            <h1 style="margin: 0; font-size: 28px; font-weight: 700; letter-spacing: -0.5px; color: #ffffff;">
                                Código de Acesso
                            </h1>
                            <p style="margin: 8px 0 0 0; font-size: 16px; font-weight: 400; color: rgba(255, 255, 255, 0.9);">
                                Para: {{ $igreja->nome }}
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
                                Olá, {{ $user->name }}!
                            </p>

                            <!-- Mensagem Principal -->
                            <p style="font-size: 16px; color: #374151; margin: 0 0 16px 0; line-height: 1.7;">
                                Você solicitou o código de acesso para a igreja <strong style="color: #059669;">{{ $igreja->nome }}</strong>.
                            </p>
                            <p style="font-size: 16px; color: #374151; margin: 0 0 32px 0; line-height: 1.7;">
                                Utilize o código abaixo para concluir seu acesso e entrar na área de membros:
                            </p>

                            <!-- Seção de Código de Acesso (Substituindo Credenciais) -->
                            <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border: 2px solid #e2e8f0; border-radius: 12px; padding: 0; margin: 24px 0;">
                                <tr>
                                    <td align="center" style="padding: 24px;">
                                        <!-- Título da Seção -->
                                        <h3 style="color: #1e293b; margin: 0 0 16px; font-size: 18px; font-weight: 600;">
                                            Seu Código de Acesso
                                        </h3>
                                        <!-- Código Destacado -->
                                        <div class="code-box-value" style="font-family: 'Monaco', 'Menlo', monospace; background-color: #e0f2fe; color: #0ea5e9; padding: 16px 25px; border-radius: 10px; font-weight: 700; font-size: 28px; letter-spacing: 5px; border: 2px dashed #0ea5e9; display: inline-block; max-width: 100%; word-break: break-all;">
                                            {{ $code }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <!-- Fim Seção de Código de Acesso -->

                            <!-- Bloco de Aviso (Amarelo - Tabela Robusta) -->
                            <table width="100%" cellspacing="0" cellpadding="0" border="0" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 1px solid #f59e0b; border-radius: 12px; margin: 24px 0;">
                                <tr>
                                    <td style="padding: 20px; color: #92400e; font-size: 14px; line-height: 1.6;">
                                        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="font-size: 20px; width: 35px; vertical-align: top;">⚠️</td>
                                                <td style="padding-left: 5px;">
                                                    <strong style="color: #92400e;">Importante:</strong> Este código é **confidencial** e só é válido por um curto período. Não compartilhe com terceiros e utilize-o imediatamente.
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Mensagem de Rodapé -->
                            <p style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb; color: #6b7280; font-style: italic; font-size: 14px;">
                                Se você não solicitou este código, por favor, ignore este email.
                            </p>
                            <p style="margin: 0; color: #111827; font-size: 14px;">
                                <strong style="font-weight: 700;">Atenciosamente,<br>Equipe OmnIgrejas</strong>
                            </p>

                        </td>
                    </tr>

                    <!-- Bloco do Rodapé -->
                    <tr>
                        <td align="center" style="background-color: #f8fafc; padding: 24px 30px; text-align: center; border-top: 1px solid #e5e7eb; border-radius: 0 0 12px 12px;">
                            <p style="margin: 6px 0; color: #6b7280; font-size: 14px;">
                                <strong style="font-weight: 700; color: #111827; letter-spacing: -0.3px;">{{ config('app.name') }}</strong>
                            </p>
                            <p style="margin: 6px 0; color: #6b7280; font-size: 14px;">
                                Este email foi enviado para {{ $user->email }}
                            </p>
                            <p style="margin: 6px 0; color: #6b7280; font-size: 14px;">
                                Este é um email automático, não responda a esta mensagem.
                            </p>
                            <p style="margin-top: 15px; font-size: 12px; color: #a0aec0;">
                                &copy; {{ date('Y') }} OmnIgrejas. Todos os direitos reservados.
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
