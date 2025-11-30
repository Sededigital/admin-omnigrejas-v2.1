<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Estatístico de Movimentação</title>
    <style>
        /*
        * ESTILO GERAL INSPIRADO NA FICHA DE TRANSFERÊNCIA
        * Fonte: Times New Roman, para um visual clássico e oficial.
        * Layout: Limpo, com seções bem definidas por bordas.
        * Cores: Monocromático para uma impressão profissional.
        */
        
        /* 1. Configurações Globais e da Página de Impressão (A4) */
        @page {
            size: A4;
            margin: 0.5cm; /* Margem bem estreita para impressão */
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 10.5pt;
            line-height: 1.4;
            color: #111;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        /* 2. Container Principal */
        .report-container {
            max-width: 21cm; 
            /* ALTERAÇÃO AQUI: Removida a margem inferior (2rem) para eliminar a sobra cinzenta na visualização em tela. */
            margin: 0 auto; 
            padding: 0.5cm; 
            background: #ffffff;
            border: 1px solid #ddd;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            box-sizing: border-box; 
        }

        /* 3. Cabeçalho */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 10px; 
        }
        .header-title h1 {
            font-size: 18pt;
            margin: 0;
            font-weight: bold;
        }
        .header-title h2 {
            font-size: 13pt;
            margin: 5px 0 0 0;
            font-weight: normal;
            color: #333;
        }

        /* 4. Seções de Conteúdo */
        .report-main-content {
            /* Classe adicionada para o Flexbox na impressão */
        }
        .content-section {
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 15px;
        }
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            margin: 0 0 8px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
            text-transform: uppercase;
        }
        
        /* 5. Tabela de Estatísticas */
        .stats-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }
        .stats-table th, .stats-table td {
            border: 1px solid #ccc;
            padding: 6px 10px;
            text-align: left;
        }
        .stats-table thead {
            background-color: #f2f2f2;
        }
        .stats-table th {
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-green { color: #006400; } 
        .text-red { color: #8B0000; }
        .text-blue { color: #00008B; }

        /* 6. Rodapé */
        .footer {
            text-align: center;
            font-size: 8.5pt;
            color: #555;
            padding-top: 10px;
            margin-top: 185px;
            border-top: 1px solid #eee;
        }

        /* 7. Otimizações para Impressão */
        @media print {
            body {
                background-color: #ffffff;
            }
            .report-container {
                margin: 0;
                padding: 0;
                border: none;
                box-shadow: none;
                
                /* REGRAS PARA FIXAR O FOOTER AO FUNDO DA PÁGINA A4 */
                display: flex;
                flex-direction: column;
                /* 28.7cm é a altura da folha A4 (29.7cm) menos as margens @page (0.5cm topo + 0.5cm fundo) */
                min-height: 28.7cm; 
            }
            
            /* Esta seção preenche todo o espaço restante, empurrando o footer para o fundo */
            .report-main-content {
                flex-grow: 1;
            }
            
            .stats-table thead {
                background-color: #f2f2f2 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .content-section {
                 page-break-inside: avoid; 
                 margin-bottom: 15px; 
            }
        }
    </style>
</head>
<body>

    <div class="report-container">
        
        <!-- Cabeçalho do Relatório -->
        <div class="header">
            <div class="header-title">
                <h1>RELATÓRIO DE MOVIMENTAÇÃO</h1>
                <h2>Análise Estatística Eclesiástica</h2>
            </div>
        </div>
        
        <!-- INÍCIO: Wrapper do Conteúdo Principal para Flexbox -->
        <div class="report-main-content">
            <!-- Detalhes da Emissão e Período -->
            <div style="margin-bottom: 10px; font-size: 10pt; color: #444;">
                <p style="margin: 2px 0;"><strong>Igreja:</strong> {{ $church->nome ?? 'N/A' }}</p>
                <p style="margin: 2px 0;"><strong>Período:</strong> Último Mês</p>
                <p style="margin: 2px 0;"><strong>Emissão:</strong> {{ \Carbon\Carbon::now()->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y \à\s H:i:s') }}</p>
            </div>

            <!-- Sumário Executivo -->
            <div class="content-section">
                <h3 class="section-title">Sumário Executivo</h3>
                <p style="text-align: justify; margin: 0;">
                    Este relatório apresenta as estatísticas consolidadas dos principais movimentos de membresia e cargos ocorridos no período de referência para a igreja <strong>{{ $church->nome ?? 'N/A' }}</strong>. Os dados a seguir fornecem uma visão clara da dinâmica interna da organização.
                </p>
            </div>

            <!-- Tabela de Estatísticas -->
            <div class="content-section">
                <h3 class="section-title">Tabela de Indicadores Chave</h3>
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>Tipo de Movimento</th>
                            <th class="text-center">Eventos (Mês)</th>
                            <th class="text-center">Acumulado (Trimestre)</th>
                            <th class="text-center">Acumulado (Ano)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="font-bold">NOVA ADESÃO</span></td>
                            <td class="text-center text-green font-bold">{{ $stats['nova_adesao_mes'] ?? 0 }}</td>
                            <td class="text-center">{{ $stats['nova_adesao_trimestre'] ?? 0 }}</td>
                            <td class="text-center">{{ $stats['nova_adesao_ano'] ?? 0 }}</td>
                        </tr>
                        <tr>
                            <td>Transferência (Saída)</td>
                            <td class="text-center text-red">{{ $stats['transferencia_saida_mes'] ?? 0 }}</td>
                            <td class="text-center">{{ $stats['transferencia_saida_trimestre'] ?? 0 }}</td>
                            <td class="text-center">{{ $stats['transferencia_saida_ano'] ?? 0 }}</td>
                        </tr>
                        <tr>
                            <td>Reintegração (Retorno)</td>
                            <td class="text-center text-blue">{{ $stats['reintegracao_mes'] ?? 0 }}</td>
                            <td class="text-center">{{ $stats['reintegracao_trimestre'] ?? 0 }}</td>
                            <td class="text-center">{{ $stats['reintegracao_ano'] ?? 0 }}</td>
                        </tr>
                        <tr>
                            <td>MUDANÇA DE CARGO (Interna)</td>
                            <td class="text-center">{{ $stats['mudanca_cargo_mes'] ?? 0 }}</td>
                            <td class="text-center">{{ $stats['mudanca_cargo_trimestre'] ?? 0 }}</td>
                            <td class="text-center">{{ $stats['mudanca_cargo_ano'] ?? 0 }}</td>
                        </tr>
                    </tbody>
                </table>
                <p style="font-size: 9pt; color: #666; margin-top: 10px;">*Dados baseados nas migrações registradas no sistema Omnigreja para a igreja {{ $church->nome ?? 'N/A' }}.</p>
            </div>

            <!-- Análise de Tendências -->
            <div class="content-section">
                 <h3 class="section-title">Análise de Tendências</h3>
                <ul style="margin: 0; padding-left: 20px; font-size: 10pt;">
                    <li><span class="font-bold">Nova Adesão:</span> O crescimento de {{ $stats['nova_adesao_mes'] ?? 0 }} adesões no mês indica um {{ ($stats['nova_adesao_mes'] ?? 0) > 10 ? 'forte impulso na evangelização' : 'crescimento moderado' }} e captação de novos membros{{ ($stats['nova_adesao_mes'] ?? 0) > ($stats['transferencia_saida_mes'] ?? 0) ? ', mantendo uma tendência positiva' : ', com necessidade de estratégias de retenção' }}.</li>
                    <li style="margin-top: 8px;"><span class="font-bold">Transferência vs. Reintegração:</span> É crucial monitorar a diferença entre as {{ $stats['transferencia_saida_mes'] ?? 0 }} saídas por Transferência e os {{ $stats['reintegracao_mes'] ?? 0 }} Retornos por Reintegração, visando aprimorar as estratégias de retenção{{ (($stats['transferencia_saida_mes'] ?? 0) > ($stats['reintegracao_mes'] ?? 0)) ? ' e reduzir a taxa de saída' : '' }}.</li>
                    <li style="margin-top: 8px;"><span class="font-bold">Mudança de Cargo:</span> As {{ $stats['mudanca_cargo_mes'] ?? 0 }} Mudanças de Cargo mostram uma gestão {{ ($stats['mudanca_cargo_mes'] ?? 0) > 5 ? 'dinâmica' : 'estável' }}, focada no desenvolvimento interno e na realocação de talentos para novas responsabilidades.</li>
                </ul>
            </div>
        </div>
        <!-- FIM: Wrapper do Conteúdo Principal para Flexbox -->

        <!-- Rodapé do Relatório (Agora pinado ao fundo da página na impressão) -->
        <div class="footer">
            <p>Documento gerado automaticamente pelo Sistema de Gestão Interna <b>Omnigrejas</b> (SGO) para fins administrativos e eclesiásticos.</p>
            <p>Sistema Omnigreja | {{ \Carbon\Carbon::now()->locale('pt_BR')->translatedFormat('d \d\e F \d\e Y \à\s H:i:s') }}.</p>
        </div>
    </div>

</body>
</html>
