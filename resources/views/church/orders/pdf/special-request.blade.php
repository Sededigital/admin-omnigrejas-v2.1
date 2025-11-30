<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido Especial - {{ $request->id }}</title>
    <style>
        /* 1. Reset e Configurações Básicas */
        body {
            font-family: 'Times New Roman', serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #111;
            margin: 0;
            padding: 15px;
            position: relative;
        }

        /* 2. Layouts Estruturais para PDF */
        .header-content, .signatures {
            width: 100%;
            display: table;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .header-col, .signature-box {
            display: table-cell;
            vertical-align: top;
        }

        .data-section {
            margin-bottom: 20px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .two-columns {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .col {
            display: table-cell;
            width: 50%;
            padding: 0 10px;
            vertical-align: top;
        }

        /* 3. Estilização do Cabeçalho e Título */
        .header {
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 15px;
            position: relative;
        }

        .doc-number-container {
            position: absolute;
            top: -10px;
            right: 0;
            width: 150px;
            text-align: right;
            line-height: 1;
        }

        .church-info h1 {
            color: #111;
            font-size: 16pt;
            margin: 5pt 0 3pt 0;
            font-weight: bold;
        }

        .church-info p {
            margin: 0;
            color: #333;
            font-size: 10pt;
        }

        .document-title {
            font-size: 14pt;
            font-weight: bold;
            color: #000;
            margin: 15px 0 8px 0;
            text-align: center;
            text-transform: uppercase;
        }

        .document-subtitle {
            text-align: center;
            margin-bottom: 10px;
            font-weight: normal;
            font-size: 11pt;
            font-style: italic;
            color: #444;
        }

        /* 4. Estilização dos Campos de Dados */
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 8px;
            color: #000;
            border-bottom: 1px dashed #666;
            padding-bottom: 3px;
            text-transform: uppercase;
        }

        .data-field {
            margin-bottom: 5px;
            display: block;
        }

        .data-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
            color: #333;
        }

        .data-value {
            border-bottom: 1px dotted #888;
            padding-bottom: 1px;
            display: inline-block;
            min-width: 150px;
        }

        /* 5. Nota Importante */
        .important-note {
            background-color: #f2f2f2;
            border: 1px solid #aaa;
            border-radius: 4px;
            padding: 10px;
            margin: 25px 0 15px 0;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }

        /* 6. Assinaturas */
        .signature-section {
            margin-top: 100px;
            padding-top: 5px;
        }

        .signatures {
            width: 100%;
            display: table;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .signature-box {
            text-align: center;
            width: 33.3%;
            padding: 0 5px;
            display: table-cell;
            vertical-align: top;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            width: 90%;
            margin: 30px auto 3px auto;
            display: block;
        }

        .signature-label {
            font-size: 10pt;
            color: #111;
            margin-top: 3px;
            line-height: 1.2;
        }

        .signature-label strong {
            font-size: 11pt;
        }

        /* 7. Rodapé */
        .footer {
            text-align: center;
            font-size: 8pt;
            color: #555;
            padding-top: 5px;
            margin-top: 15px;
            border-top: 1px solid #eee;
        }

        /* Logo da Igreja */
        .church-logo {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border: none;
            border-radius: 50%;
            margin: 0 auto;
            display: block;
        }

        .logo-placeholder {
            width: 80px;
            height: 80px;
            border: none;
            border-radius: 50%;
            background-color: #f2f2f2;
            text-align: center;
            line-height: 80px;
            font-size: 10pt;
            font-weight: bold;
            margin: 0 auto;
            display: block;
        }

        /* 8. Configuração de Impressão (A4) */
        @page {
            size: A4;
            margin: 10mm;
        }

        .styled-hr {
            border: none;
            height: 1px;
            background-color: #d1d5db;
            margin: 1rem 0;
        }

        .system-logo {
            position: absolute;
            top: -70px;
            left: -30px;
            width: 140px;
            height: 140px;
            z-index: 1000;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pendente { background-color: #fff3cd; color: #856404; }
        .status-em_andamento { background-color: #d1ecf1; color: #0c5460; }
        .status-aprovado { background-color: #d4edda; color: #155724; }
        .status-rejeitado { background-color: #f8d7da; color: #721c24; }
        .status-concluido { background-color: #cce5ff; color: #004085; }
    </style>
</head>
<body>
    <!-- Logo do Sistema - Canto Superior Esquerdo -->
    <img src="{{ asset('system/img/logo-system/icon.png') }}" alt="Omnigrejas" class="system-logo">

    <!-- Contêiner para o Número do Documento no Canto Superior Direito -->
    <div class="doc-number-container">
        <p style="font-size: 9pt; color: #777; margin: 0;">
            DOC. N.º: {{ now()->format('Y/m/d') }}/{{ $numero_pedido }}-{{ now()->timestamp }}
        </p>
    </div>

    <!-- Cabeçalho com Logo e Informações da Igreja -->
    <div class="header">
        <div style="display: flex; flex-direction: column; align-items: center; text-align: center;">
            @if($request->igreja->logo)
                <img src="{{ Storage::disk('supabase')->url($request->igreja->logo) }}" alt="Logo da Igreja" class="church-logo">
            @else
                <div class="logo-placeholder">LOGO</div>
            @endif

            <div class="church-info">
                <h1>{{ $request->igreja->nome }}</h1>
                <p>{{ $request->igreja->sigla ? '(' . $request->igreja->sigla . ')' : '' }}</p>
                <p>NIF: {{ $request->igreja->nif ?? 'Não informado' }}</p>
                <p>Contato: {{ $request->igreja->contacto ?? 'Não informado' }}</p>
            </div>
        </div>
    </div>

    <!-- Título do Documento -->
    <div class="document-title">
        PEDIDO ESPECIAL
    </div>

    <!-- Subtítulo -->
    <div class="document-subtitle">
        Documento oficial de solicitação especial - {{ $numero_pedido }}
    </div>

    <!-- Seção de Informações do Pedido -->
    <div class="data-section">
        <div class="section-title">
            INFORMAÇÕES DO PEDIDO
        </div>

        <div class="two-columns">
            <!-- Coluna 1 -->
            <div class="col">
                <div class="data-field">
                    <span class="data-label">Nº do Pedido:</span>
                    <span class="data-value">{{ $numero_pedido }}</span>
                </div>
                <div class="data-field">
                    <span class="data-label">Tipo:</span>
                    <span class="data-value">{{ $request->pedidoTipo->nome ?? 'Não definido' }}</span>
                </div>
                <div class="data-field">
                    <span class="data-label">Data Pedido:</span>
                    <span class="data-value">{{ $request->data_pedido ? $request->data_pedido->format('d/m/Y') : 'Não informada' }}</span>
                </div>
                <div class="data-field">
                    <span class="data-label">Status:</span>
                    <span class="data-value">
                        <span class="status-badge status-{{ $request->status }}">
                            {{ $request->status_label }}
                        </span>
                    </span>
                </div>
            </div>

            <!-- Coluna 2 -->
            <div class="col">
                <div class="data-field">
                    <span class="data-label">Solicitante:</span>
                    <span class="data-value">{{ $request->membro->user->name ?? 'Não informado' }}</span>
                </div>
                <div class="data-field">
                    <span class="data-label">Nº Membro:</span>
                    <span class="data-value">{{ $request->membro->numero_membro ?? 'Não informado' }}</span>
                </div>
                <div class="data-field">
                    <span class="data-label">Cargo:</span>
                    <span class="data-value">{{ ucfirst($request->membro->cargo ?? 'Não informado') }}</span>
                </div>
                @if($request->responsavel)
                <div class="data-field">
                    <span class="data-label">Responsável:</span>
                    <span class="data-value">{{ $request->responsavel->name }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Descrição em linha separada -->
        <div class="data-field" style="padding-top: 10px;">
            <span class="data-label" style="vertical-align: top;">Descrição:</span>
            <div style="display: inline-block; min-width: 300px; padding: 5px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9;">
                {{ $request->descricao }}
            </div>
        </div>

        <!-- Curso relacionado (se houver) -->
        @if($request->curso)
        <div class="data-field" style="padding-top: 5px;">
            <span class="data-label">Curso Relacionado:</span>
            <span class="data-value">{{ $request->curso->nome }} ({{ $request->curso->tipo }})</span>
        </div>
        @endif

        <!-- Data de resolução (se houver) -->
        @if($request->data_resolucao)
        <div class="data-field" style="padding-top: 5px;">
            <span class="data-label">Data Resolução:</span>
            <span class="data-value">{{ $request->data_resolucao->format('d/m/Y') }}</span>
        </div>
        @endif
    </div>

    <!-- Seção de Observações (se houver) -->
    @if($request->membro->membroPerfil && $request->membro->membroPerfil->observacoes)
    <div class="data-section">
        <div class="section-title">
            OBSERVAÇÕES DO MEMBRO
        </div>
        <div style="padding: 10px; background-color: #f9f9f9; border-radius: 4px;">
            {{ $request->membro->membroPerfil->observacoes }}
        </div>
    </div>
    @endif

    <!-- Informações Institucionais -->
    <div class="important-note" style="background-color: #e3f2fd; border: 1px solid #2196f3; margin-bottom: 15px;">
        <strong style="color: #1976d2;">SISTEMA OMNIGREJAS - GESTÃO ECLESIÁSTICA</strong><br>
        <small style="color: #424242;">
            Documento oficial emitido pelo Sistema Omnigreja para controle e acompanhamento de pedidos especiais.
            Este documento é confidencial e destina-se exclusivamente ao uso interno da instituição religiosa.
            Validade: Indeterminada, sujeito às normas eclesiásticas vigentes.
        </small>
    </div>

    <!-- Declaração -->
    <div class="important-note">
        <strong>DECLARAÇÃO OFICIAL:</strong>
        A Igreja {{ $request->igreja->nome }} certifica que o pedido especial de
        <span style="font-weight: bold;">{{ $request->membro->user->name }}</span>
        foi registrado sob o número
        <span style="font-weight: bold;">{{ $numero_pedido }}</span>,
        com status atual de <span style="font-weight: bold;">{{ $request->status_label }}</span>.
        Este documento serve como comprovante oficial do pedido e deve ser mantido em arquivo.
    </div>

    <!-- Data de Emissão -->
    <div style="text-align: center; margin-top: 30px; padding: 10px; border: 1px solid #000; display: inline-block;">
        <strong>DATA DE EMISSÃO: {{ $data_emissao }}</strong>
    </div>

    <!-- Assinaturas -->
    <div class="signature-section">
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">
                    <strong>{{ $request->membro->user->name }}</strong><br>
                    <small>Solicitante</small>
                </div>
            </div>

            @if($request->responsavel)
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">
                    <strong>{{ $request->responsavel->name }}</strong><br>
                    <small>Responsável</small>
                </div>
            </div>
            @else
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">
                    <strong>____________________</strong><br>
                    <small>Responsável</small>
                </div>
            </div>
            @endif

            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">
                    <strong>{{ Auth::user()->name }}</strong><br>
                    <small>Emissor do Documento</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Rodapé -->
    <div class="footer" style="margin-top: 80px;">
        <p>Documento gerado pelo **Sistema Omnigreja** em {{ now()->format('d/m/Y H:i:s') }} (ID de Rastreio: {{ now()->timestamp }})</p>
        <p>A autenticidade deste documento pode ser verificada junto à igreja emissora.</p>
    </div>
</body>
</html>
