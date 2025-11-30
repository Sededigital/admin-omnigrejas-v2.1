<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Culto - {{ $report->titulo ?: 'Relatório sem título' }}</title>
    
    <!-- Incluindo a fonte DejaVu Sans para garantir compatibilidade com a maioria dos geradores de PDF (ex: DomPDF) -->
    <style>
        /* ==========================================================
            1. ESTILOS GERAIS (BODY, TEXTO)
            ========================================================== */
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #333;
            margin: 0;
            padding: 10px;
            position: relative;
            min-height: 100vh;
        }

        /* ==========================================================
            2. HEADER E TÍTULO
            ========================================================== */
        .header {
            text-align: center;
            border-bottom: 1px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
            padding-top: 10px;
        }

        /* Logo do sistema (posicionamento absoluto para sair da área de impressão/margem) */
        .system-logo {
            position: absolute;
            top: -70px;
            left: -30px;
            width: 140px;
            height: 140px;
            z-index: 1000;
        }

        .logo-section {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        .logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
            border: none;
        }

        .church-info h1 {
            font-size: 16px;
            margin: 0;
            color: #333;
        }

        .church-info p {
            margin: 3px 0;
            font-size: 10px;
            color: #666;
        }
        
        /* Título do Relatório */
        .report-title {
            font-size: 14px;
            font-weight: bold;
            margin: 15px 0 10px 0;
            text-align: center;
            color: #333;
            text-transform: uppercase;
        }

        .report-subtitle {
            font-size: 12px;
            font-weight: normal;
            margin-top: 5px;
            color: #666;
        }

        /* ==========================================================
            3. ESTILOS DE SEÇÃO E TÍTULOS DE CONTEÚDO
            ========================================================== */
        .content-section {
            margin: 15px 0;
        }

        .content-section h4 {
            font-size: 11px;
            color: #333;
            margin: 10px 0 5px 0;
            padding-bottom: 3px;
            border-bottom: 1px solid #ddd;
            text-transform: uppercase;
            font-weight: bold;
            white-space: nowrap;
        }

        /* ==========================================================
            4. TABELAS E CÉLULAS DE INFORMAÇÃO (USO DE DISPLAY:TABLE)
            ========================================================== */
        .data-table-container, .info-grid {
            /* CORREÇÃO: Adicionado display: table; para que a linha (row) tenha um pai 'table'. */
            display: table;
            width: 100%;
            border-collapse: collapse;
        }

        /* Grid de Informações Principais (2 colunas: Label | Value) */
        .info-grid {
            /* display: table; - Já está na regra acima */
            /* width: 100%; - Já está na regra acima */
            margin-bottom: 20px;
            font-size: 10px;
        }

        .info-row {
            display: table-row;
        }

        .info-cell {
            display: table-cell;
            padding: 6px 8px;
            vertical-align: top;
            border: 1px solid #dee2e6;
            white-space: nowrap;
        }

        .info-label {
            font-weight: bold;
            width: 140px; /* Largura fixa para os labels */
            background-color: #f8f9fa;
            font-size: 9px;
        }

        .info-value {
            background-color: #fff;
        }
        
        /* Tabela de Dados (Financeiro/Participação) - 6 COLUNAS */
        .finance-table {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
            table-layout: fixed; /* Garante que todas as colunas tenham o mesmo tamanho */
        }

        .finance-header {
            display: table-row;
            background-color: #f8f9fa;
        }

        .finance-data {
            display: table-row;
            background-color: #fff;
        }

        .finance-cell {
            display: table-cell;
            padding: 6px 4px;
            border: 1px solid #dee2e6;
            text-align: center;
            font-size: 8px;
            /* width: 16.66%; (6 colunas = 100%/6) - Deixado implícito pelo table-layout: fixed; */
            white-space: nowrap;
            overflow: hidden;
        }

        .finance-cell.value {
            font-weight: bold;
            font-size: 8px;
        }

        .finance-cell.total {
            background-color: #d1ecf1;
            font-weight: bold;
        }
        
        /* Estilos para células de texto longo (Resumo, Relato, Observações) */
        .text-cell {
            display: table-cell; 
            padding: 8px; 
            border: 1px solid #dee2e6; 
            font-size: 10px; 
            line-height: 1.4; 
            text-align: justify; 
            vertical-align: top; 
            width: 50%;
        }

        /* ==========================================================
            5. BADGES E FOOTER
            ========================================================== */

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-rascunho {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-finalizado {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .footer {
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 8px;
            color: #666;
            white-space: nowrap;
            position: absolute;
            bottom: 5pt;
            left: 10px;
            right: 10px;
        }

        /* ==========================================================
            6. ESTILOS DE IMPRESSÃO (PDF)
            ========================================================== */
        @media print {
            body {
                padding: 0;
                margin: 0;
            }

            @page {
                margin-left: 0cm;
                margin-right: 0cm;
                margin-top: 0pt;
                margin-bottom: 5pt;
            }
        }
    </style>
</head>
<body>
    
    <!-- Logo do Sistema - Canto Superior Esquerdo (Posicionamento absoluto) -->
    <img src="{{ asset('system/img/logo-system/icon.png') }}" alt="Omnigrejas" class="system-logo">

    <!-- Header Compacto (Informações da Igreja) -->
    <div class="header">
        <div class="logo-section">
            @if($igreja->logo)
                <img src="{{ Storage::disk('supabase')->url($igreja->logo) }}" alt="Logo da Igreja" class="logo">
            @endif
            <div class="church-info">
                <h1>{{ $igreja->nome }}</h1>
                <p>{{ $igreja->nif .' | '. $igreja->localizacao }}</p>
            </div>
        </div>
    </div>

    <!-- Título e Subtítulo do Relatório -->
    <div class="report-title">
        RELATÓRIO DE CULTO
        <div class="report-subtitle">{{ $report->titulo ?: 'Sem título' }}</div>
    </div>

    <!-- Informações Principais do Culto (Data, Status, Evento/Culto Padrão) -->
    <div class="info-grid">
        <div class="info-row">
            <div class="info-cell info-label">Data do Relatório:</div>
            <div class="info-cell info-value">{{ $report->data_relatorio->format('d/m/Y') }}</div>
            <div class="info-cell info-label">Status:</div>
            <div class="info-cell info-value">
                <span class="status-badge status-{{ $report->status }}">
                    {{ $report->status === 'rascunho' ? 'Rascunho' : 'Finalizado' }}
                </span>
            </div>
        </div>

        @if($report->evento)
        <div class="info-row">
            <div class="info-cell info-label">Evento Relacionado:</div>
            <div class="info-cell info-value">{{ $report->evento->titulo }}</div>
            <div class="info-cell info-label">Data/Horário do Evento:</div>
            <div class="info-cell info-value">{{ $report->evento->data_evento->format('d/m/Y') }} {{ $report->evento->hora_inicio }}</div>
        </div>
        @elseif($report->cultoPadrao)
        <div class="info-row">
            <div class="info-cell info-label">Culto Padrão:</div>
            <div class="info-cell info-value">{{ $report->cultoPadrao->titulo }}</div>
            <div class="info-cell info-label">Horário Programado:</div>
            <div class="info-cell info-value">{{ $report->cultoPadrao->hora_inicio }} - {{ $report->cultoPadrao->hora_fim }}</div>
        </div>
        @endif
    </div>

    <!-- ==========================================================
        CONTEÚDO E ESTATÍSTICAS
        ========================================================== -->
    <div class="main-content">

        <!-- Lógica PHP para verificar se há estatísticas -->
        @php
            $hasParticipacaoStats = $report->numero_participantes || $report->numero_visitantes || $report->numero_decisoes ||
                                    $report->numero_batismos || $report->numero_conversoes || $report->numero_reconciliacoes ||
                                    $report->numero_casamentos || $report->numero_funeral || $report->numero_outros_eventos;

            $hasFinanceiro = $report->valor_oferta || $report->valor_dizimos || $report->valor_ofertas || $report->valor_doacoes || $report->valor_outros;
            $totalFinanceiro = (float)($report->valor_oferta ?? 0) + (float)($report->valor_dizimos ?? 0) + (float)($report->valor_ofertas ?? 0) + (float)($report->valor_doacoes ?? 0) + (float)($report->valor_outros ?? 0);

            $hasInfoCulto = $report->tema_culto || $report->pregador || $report->pregador_convidado || $report->texto_base ||
                            $report->tipo_culto || $report->dirigente || $report->musica_responsavel;

            $hasOutrosCampos = $report->tipo_culto || $report->pregador || $report->pregador_convidado ||
                               $report->dirigente || $report->musica_responsavel || $report->texto_base;
        @endphp

        <!-- Estatísticas de Participação -->
        @if($hasParticipacaoStats)
        <div class="content-section">
            <h4>Estatísticas de Participação</h4>
            <div class="finance-table">
                <!-- Linha de cabeçalhos (10 colunas) -->
                <div class="finance-header">
                    <div class="finance-cell" style="width: 10%;">Participantes</div>
                    <div class="finance-cell" style="width: 10%;">Visitantes</div>
                    <div class="finance-cell" style="width: 10%;">Decisões</div>
                    <div class="finance-cell" style="width: 10%;">Batismos</div>
                    <div class="finance-cell" style="width: 10%;">Conversões</div>
                    <div class="finance-cell" style="width: 10%;">Reconciliações</div>
                    <div class="finance-cell" style="width: 10%;">Casamentos</div>
                    <div class="finance-cell" style="width: 10%;">Funerais</div>
                    <div class="finance-cell" style="width: 10%;">Outros</div>
                </div>
                <!-- Linha de valores -->
                <div class="finance-data">
                    <div class="finance-cell value">{{ $report->numero_participantes ?: '-' }}</div>
                    <div class="finance-cell value">{{ $report->numero_visitantes ?: '-' }}</div>
                    <div class="finance-cell value">{{ $report->numero_decisoes ?: '-' }}</div>
                    <div class="finance-cell value">{{ $report->numero_batismos ?: '-' }}</div>
                    <div class="finance-cell value">{{ $report->numero_conversoes ?: '-' }}</div>
                    <div class="finance-cell value">{{ $report->numero_reconciliacoes ?: '-' }}</div>
                    <div class="finance-cell value">{{ $report->numero_casamentos ?: '-' }}</div>
                    <div class="finance-cell value">{{ $report->numero_funeral ?: '-' }}</div>
                    <div class="finance-cell value">{{ $report->numero_outros_eventos ?: '-' }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Valores Financeiros -->
        @if($hasFinanceiro)
        <div class="content-section">
            <h4>Valores Financeiros (AOA)</h4>
            <div class="finance-table">
                <!-- Linha de cabeçalhos (6 colunas) -->
                <div class="finance-header">
                    <div class="finance-cell">Oferta</div>
                    <div class="finance-cell">Dízimos</div>
                    <div class="finance-cell">Ofertas Especiais</div>
                    <div class="finance-cell">Doações</div>
                    <div class="finance-cell">Outros</div>
                    <div class="finance-cell total">Total Arrecadado</div>
                </div>
                <!-- Linha de valores -->
                <div class="finance-data">
                    <div class="finance-cell value">{{ $report->valor_oferta ? number_format($report->valor_oferta, 2, ',', '.') . ' AOA' : '-' }}</div>
                    <div class="finance-cell value">{{ $report->valor_dizimos ? number_format($report->valor_dizimos, 2, ',', '.') . ' AOA' : '-' }}</div>
                    <div class="finance-cell value">{{ $report->valor_ofertas ? number_format($report->valor_ofertas, 2, ',', '.') . ' AOA' : '-' }}</div>
                    <div class="finance-cell value">{{ $report->valor_doacoes ? number_format($report->valor_doacoes, 2, ',', '.') . ' AOA' : '-' }}</div>
                    <div class="finance-cell value">{{ $report->valor_outros ? number_format($report->valor_outros, 2, ',', '.') . ' AOA' : '-' }}</div>
                    <div class="finance-cell value total">{{ $totalFinanceiro > 0 ? number_format($totalFinanceiro, 2, ',', '.') . ' AOA' : '-' }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Informações do Culto (Tema, Dirigentes, Música) -->
        @if($hasInfoCulto)
        <div class="content-section">
            <h4>Informações da Celebração</h4>

            <!-- Tema (mantém separado para ocupar 100% da largura) -->
            @if($report->tema_culto)
            <div class="info-grid" style="margin-bottom: 10px;">
                <div class="info-row">
                    <div class="info-cell info-label" style="width: 100px;">Tema do Culto:</div>
                    <div class="info-cell info-value" style="width: auto;">{{ $report->tema_culto }}</div>
                </div>
            </div>
            @endif

            <!-- Outros campos em tabela -->
            @if($hasOutrosCampos)
            <div class="finance-table">
                <!-- Linha de cabeçalhos (6 colunas) -->
                <div class="finance-header">
                    <div class="finance-cell">Tipo de Culto</div>
                    <div class="finance-cell">Pregador</div>
                    <div class="finance-cell">Pregador Convidado</div>
                    <div class="finance-cell">Dirigente</div>
                    <div class="finance-cell">Resp. Música</div>
                    <div class="finance-cell">Texto Base</div>
                </div>
                <!-- Linha de valores -->
                <div class="finance-data">
                    <div class="finance-cell value">{{ $report->tipo_culto ? ucfirst($report->tipo_culto) : '-' }}</div>
                    <div class="finance-cell value">{{ $report->pregador ?: '-' }}</div>
                    <div class="finance-cell value">{{ $report->pregador_convidado ?: '-' }}</div>
                    <div class="finance-cell value">{{ $report->dirigente ?: '-' }}</div>
                    <div class="finance-cell value">{{ $report->musica_responsavel ?: '-' }}</div>
                    <div class="finance-cell value">{{ $report->texto_base ?: '-' }}</div>
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- Resumo da Mensagem e Relato do Culto (Em duas colunas se ambos existirem) -->
        @if($report->resumo_mensagem || $report->conteudo)
        <div class="content-section">
            <!-- data-table-container está agora configurado como display: table -->
            <div class="data-table-container"> 
                <!-- Linha de cabeçalhos -->
                <div style="display: table-row; background-color: #f8f9fa;">
                    @if($report->resumo_mensagem)
                    <div style="display: table-cell; padding: 6px 8px; border: 1px solid #dee2e6; font-size: 11px; font-weight: bold; text-align: center; width: 50%;">Resumo da Mensagem</div>
                    @endif
                    @if($report->conteudo)
                    <div style="display: table-cell; padding: 6px 8px; border: 1px solid #dee2e6; font-size: 11px; font-weight: bold; text-align: center; {{ $report->resumo_mensagem ? 'border-left: none;' : '' }} width: 50%;">Relato/Acontecimentos do Culto</div>
                    @endif
                </div>
                <!-- Linha de conteúdo -->
                <div style="display: table-row; background-color: #fff;">
                    @if($report->resumo_mensagem)
                    <div class="text-cell" style="{{ $report->conteudo ? 'width: 50%;' : 'width: 100%;' }}">
                        {{ $report->resumo_mensagem }}
                    </div>
                    @endif
                    @if($report->conteudo)
                    <div class="text-cell" style="{{ $report->resumo_mensagem ? 'border-left: none; width: 50%;' : 'width: 100%;' }}">
                        {!! nl2br(e($report->conteudo)) !!}
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Observações e Observações Gerais -->
        @if($report->observacoes || $report->observacoes_gerais)
        <div class="content-section">
            <!-- data-table-container está agora configurado como display: table -->
            <div class="data-table-container">
                <!-- Linha de cabeçalhos -->
                <div style="display: table-row; background-color: #f8f9fa;">
                    @if($report->observacoes)
                    <div style="display: table-cell; padding: 6px 8px; border: 1px solid #dee2e6; font-size: 11px; font-weight: bold; text-align: center; width: 50%;">Observações Internas (Relator)</div>
                    @endif
                    @if($report->observacoes_gerais)
                    <div style="display: table-cell; padding: 6px 8px; border: 1px solid #dee2e6; font-size: 11px; font-weight: bold; text-align: center; {{ $report->observacoes ? 'border-left: none;' : '' }} width: 50%;">Observações Gerais (Igreja)</div>
                    @endif
                </div>
                <!-- Linha de conteúdo -->
                <div style="display: table-row; background-color: #fff;">
                    @if($report->observacoes)
                    <div class="text-cell" style="{{ $report->observacoes_gerais ? 'width: 50%;' : 'width: 100%;' }}">
                        {{ $report->observacoes }}
                    </div>
                    @endif
                    @if($report->observacoes_gerais)
                    <div class="text-cell" style="{{ $report->observacoes ? 'border-left: none; width: 50%;' : 'width: 100%;' }}">
                        {{ $report->observacoes_gerais }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Avaliação (Se aplicável) -->
        @if($report->avaliado_por || $report->data_avaliacao)
        <div class="content-section">
            <h4>Status de Avaliação</h4>
            <div class="finance-table" style="width: 50%; margin-left: 0;">
                <!-- Linha de cabeçalhos -->
                <div class="finance-header">
                    <div class="finance-cell">Avaliado por</div>
                    <div class="finance-cell">Data Avaliação</div>
                </div>
                <!-- Linha de valores -->
                <div class="finance-data">
                    <div class="finance-cell value">{{ $report->avaliado_por ?: '-' }}</div>
                    <div class="finance-cell value">{{ $report->data_avaliacao ? $report->data_avaliacao->format('d/m/Y H:i') : '-' }}</div>
                </div>
            </div>
        </div>
        @endif

    </div>

    <!-- Footer Fixo na Base -->
    <!-- O estilo 'position: absolute; bottom: 5pt;' no .footer é mais robusto para PDFs -->
    <div class="footer">
        <p>Gerado por: {{ $report->criadoPor->name ?? 'Sistema' }} | Em: {{ now()->format('d/m/Y H:i') }} | ID: {{ $report->id }}</p>
        <p>Sistema Omnigrejas - Gestão de Igrejas</p>
    </div>
</body>
</html>
