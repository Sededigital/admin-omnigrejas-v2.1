<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Financeiro - {{ $igreja->nome ?? 'OmnIgrejas' }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #007bff;
        }

        .system-name {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }

        .church-name {
            font-size: 16px;
            font-weight: bold;
            color: #495057;
            text-align: center;
            flex: 1;
        }

        .report-date {
            font-size: 10px;
            color: #6c757d;
            text-align: right;
        }

        .report-title {
            font-size: 20px;
            font-weight: bold;
            color: #007bff;
            text-align: center;
            margin: 30px 0;
            padding: 15px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 12px;
        }

        .summary-table th,
        .summary-table td {
            padding: 10px 15px;
            text-align: center;
            border: 1px solid #dee2e6;
        }

        .summary-table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #495057;
            font-size: 11px;
        }

        .summary-table .value-cell {
            font-size: 14px;
            font-weight: bold;
        }

        .summary-table .entradas {
            color: #28a745;
        }

        .summary-table .saidas {
            color: #dc3545;
        }

        .summary-table .saldo {
            color: #007bff;
        }

        .summary-table .economia {
            color: #17a2b8;
        }

        .summary-table .rendimento {
            color: #28a745;
            font-weight: bold;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #495057;
            margin: 25px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 1px solid #dee2e6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10px;
        }

        th, td {
            padding: 8px 12px;
            text-align: left;
            border: 1px solid #dee2e6;
        }

        th {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            font-weight: bold;
            font-size: 11px;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .text-success {
            color: #28a745;
        }

        .text-danger {
            color: #dc3545;
        }

        .text-primary {
            color: #007bff;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .fw-bold {
            font-weight: bold;
        }

        .progress {
            height: 15px;
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin: 5px 0;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border-radius: 10px;
        }

        .footer {
            position: fixed;
            bottom: 5px;
            left: 20px;
            right: 20px;
            padding-top: 5px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 8px;
            color: #6c757d;
            background: white;
        }

        .filters-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #dee2e6;
        }

        .filters-info h6 {
            margin: 0 0 10px 0;
            color: #495057;
            font-size: 12px;
        }

        .filters-info p {
            margin: 5px 0;
            font-size: 11px;
            color: #6c757d;
        }

        .account-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            background: #f8f9fa;
        }

        .account-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .account-name {
            font-weight: bold;
            color: #495057;
        }

        .account-status {
            font-size: 10px;
            padding: 3px 8px;
            border-radius: 12px;
            background: #28a745;
            color: white;
        }

        .account-metrics {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }

        .metric-item {
            flex: 1;
            text-align: center;
        }

        .metric-value {
            font-weight: bold;
            font-size: 12px;
        }

        .metric-label {
            font-size: 10px;
            color: #6c757d;
        }

        @page {
            margin: 1cm;
            size: A4;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="system-name">OmnIgrejas</div>
        <div class="church-name">{{ $igreja->nome ?? 'Sistema OmnIgrejas' }}</div>
        <div class="report-date">
            Gerado em: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <!-- Report Title -->
    <div class="report-title">
        @if($reportType === 'summary')
            Relatório Financeiro - Resumo
        @elseif($reportType === 'detailed')
            Relatório Financeiro Detalhado
        @elseif($reportType === 'category')
            Relatório Financeiro - Por Categoria
        @elseif($reportType === 'account')
            Relatório Financeiro - Por Conta
        @else
            Relatório Financeiro
        @endif
    </div>

    <!-- Filters Information -->
    <div class="filters-info">
        <h6>Informações do Relatório</h6>
        <p><strong>Período:</strong> {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} até {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</p>
        <p><strong>Tipo de Relatório:</strong> {{ ucfirst($reportType) }}</p>
        @if($selectedAccount)
            <p><strong>Conta Filtrada:</strong> {{ $selectedAccount }}</p>
        @endif
        @if($selectedCategory)
            <p><strong>Categoria Filtrada:</strong> {{ $selectedCategory }}</p>
        @endif
    </div>

    <!-- Summary Table - Sempre mostrar -->
    <div class="section-title">Resumo Financeiro</div>
    <table class="summary-table">
        <thead>
            <tr>
                <th>Total Entradas</th>
                <th>Total Saídas</th>
                <th>Saldo Líquido</th>
                <th>% Economia</th>
                @if($reportType === 'rendimento')
                    <th>Rendimento Total</th>
                @endif
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="value-cell entradas">{{ number_format($summary['total_entradas'] ?? 0, 2, ',', '.') }} AOA</td>
                <td class="value-cell saidas">{{ number_format($summary['total_saidas'] ?? 0, 2, ',', '.') }} AOA</td>
                <td class="value-cell saldo">{{ number_format($summary['saldo_liquido'] ?? 0, 2, ',', '.') }} AOA</td>
                <td class="value-cell economia">{{ number_format($summary['percentual_economia'] ?? 0, 1) }}%</td>
                @if($reportType === 'rendimento')
                    <td class="value-cell rendimento">{{ number_format($summary['total_rendimento'] ?? 0, 2, ',', '.') }} AOA</td>
                @endif
            </tr>
        </tbody>
    </table>

    <!-- Conteúdo baseado no tipo de relatório -->
    @if($reportType === 'summary')
        <!-- Relatório Resumido - Apenas resumo geral -->
        <div class="section-title">Resumo Geral do Período</div>
        <p>Este relatório apresenta um resumo geral das finanças da igreja no período selecionado, incluindo totais de entradas, saídas, saldo líquido e percentual de economia.</p>

    @elseif($reportType === 'detailed')
        <!-- Relatório Detalhado - Tudo -->
        <div class="section-title">Movimentos por Categoria</div>
        <table>
            <thead>
                <tr>
                    <th>Categoria</th>
                    <th class="text-right">Entradas</th>
                    <th class="text-right">Saídas</th>
                    <th class="text-right">Saldo</th>
                    <th class="text-center">% do Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categorySummary as $category)
                <tr>
                    <td class="fw-bold">{{ $category['nome'] }}</td>
                    <td class="text-right text-success">{{ number_format($category['entradas'], 2, ',', '.') }} AOA</td>
                    <td class="text-right text-danger">{{ number_format($category['saidas'], 2, ',', '.') }} AOA</td>
                    <td class="text-right fw-bold {{ $category['saldo'] >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($category['saldo'], 2, ',', '.') }} AOA
                    </td>
                    <td class="text-center">
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $category['percentual'] }}%"></div>
                        </div>
                        {{ number_format($category['percentual'], 1) }}%
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">Nenhum movimento encontrado para o período</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="section-title">Análise por Conta</div>
        @forelse($accountSummary as $account)
        <div class="account-card">
            <div class="account-header">
                <div class="account-name">{{ $account['banco'] }} - {{ $account['numero_conta'] }}</div>
                <div class="account-status" style="background: {{ $account['ativa'] ? '#28a745' : '#6c757d' }};">
                    {{ $account['ativa'] ? 'Ativa' : 'Inativa' }}
                </div>
            </div>
            <div class="account-metrics">
                <div class="metric-item">
                    <div class="metric-value text-success">{{ number_format($account['entradas'], 2, ',', '.') }} AOA</div>
                    <div class="metric-label">Entradas</div>
                </div>
                <div class="metric-item">
                    <div class="metric-value text-danger">{{ number_format($account['saidas'], 2, ',', '.') }} AOA</div>
                    <div class="metric-label">Saídas</div>
                </div>
                <div class="metric-item">
                    <div class="metric-value fw-bold {{ $account['saldo'] >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($account['saldo'], 2, ',', '.') }} AOA
                    </div>
                    <div class="metric-label">Saldo Atual</div>
                </div>
            </div>
        </div>
        @empty
        <p class="text-center">Nenhuma conta encontrada</p>
        @endforelse

        <div class="section-title">Movimentos Recentes</div>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Descrição</th>
                    <th>Categoria</th>
                    <th class="text-right">Valor</th>
                    <th>Responsável</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentMovements as $movement)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($movement->data_transacao)->format('d/m/Y') }}</td>
                    <td>
                        <span class="fw-bold {{ $movement->tipo === 'entrada' ? 'text-success' : 'text-danger' }}">
                            {{ ucfirst($movement->tipo) }}
                        </span>
                    </td>
                    <td>{{ $movement->descricao }}</td>
                    <td>{{ $movement->categoria->nome ?? 'N/A' }}</td>
                    <td class="text-right fw-bold {{ $movement->tipo === 'entrada' ? 'text-success' : 'text-danger' }}">
                        {{ number_format($movement->valor, 2, ',', '.') }} AOA
                    </td>
                    <td>{{ $movement->responsavel->name ?? 'N/A' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Nenhum movimento recente encontrado</td>
                </tr>
                @endforelse
            </tbody>
        </table>

    @elseif($reportType === 'category')
        <!-- Relatório por Categoria - Foco nas categorias -->
        <div class="section-title">Análise Detalhada por Categoria</div>
        <table>
            <thead>
                <tr>
                    <th>Categoria</th>
                    <th class="text-right">Entradas</th>
                    <th class="text-right">Saídas</th>
                    <th class="text-right">Saldo</th>
                    <th class="text-center">% do Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categorySummary as $category)
                <tr>
                    <td class="fw-bold">{{ $category['nome'] }}</td>
                    <td class="text-right text-success">{{ number_format($category['entradas'], 2, ',', '.') }} AOA</td>
                    <td class="text-right text-danger">{{ number_format($category['saidas'], 2, ',', '.') }} AOA</td>
                    <td class="text-right fw-bold {{ $category['saldo'] >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($category['saldo'], 2, ',', '.') }} AOA
                    </td>
                    <td class="text-center">
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $category['percentual'] }}%"></div>
                        </div>
                        {{ number_format($category['percentual'], 1) }}%
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">Nenhuma categoria encontrada para o período</td>
                </tr>
                @endforelse
            </tbody>
        </table>

    @elseif($reportType === 'account')
        <!-- Relatório por Conta - Foco nas contas -->
        <div class="section-title">Análise Detalhada por Conta</div>
        @forelse($accountSummary as $account)
        <div class="account-card">
            <div class="account-header">
                <div class="account-name">{{ $account['banco'] }} - {{ $account['numero_conta'] }}</div>
                <div class="account-status" style="background: {{ $account['ativa'] ? '#28a745' : '#6c757d' }};">
                    {{ $account['ativa'] ? 'Ativa' : 'Inativa' }}
                </div>
            </div>
            <div class="account-metrics">
                <div class="metric-item">
                    <div class="metric-value text-success">{{ number_format($account['entradas'], 2, ',', '.') }} AOA</div>
                    <div class="metric-label">Entradas</div>
                </div>
                <div class="metric-item">
                    <div class="metric-value text-danger">{{ number_format($account['saidas'], 2, ',', '.') }} AOA</div>
                    <div class="metric-label">Saídas</div>
                </div>
                <div class="metric-item">
                    <div class="metric-value fw-bold {{ $account['saldo'] >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($account['saldo'], 2, ',', '.') }} AOA
                    </div>
                    <div class="metric-label">Saldo Atual</div>
                </div>
            </div>
        </div>
        @empty
        <p class="text-center">Nenhuma conta encontrada</p>
        @endforelse

    @elseif($reportType === 'rendimento')
        <!-- Relatório de Rendimento - Foco no lucro da igreja -->
        <div class="section-title">Análise de Rendimento</div>

        <!-- Detalhamento do Rendimento -->
        <table class="summary-table">
            <thead>
                <tr>
                    <th>Componente</th>
                    <th>Valor</th>
                    <th>Descrição</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Entradas (Movimentos)</strong></td>
                    <td class="value-cell entradas">{{ number_format($summary['total_entradas'] ?? 0, 2, ',', '.') }} AOA</td>
                    <td>Movimentos de entrada registrados</td>
                </tr>
                <tr>
                    <td><strong>Doações Online</strong></td>
                    <td class="value-cell entradas">{{ number_format($summary['doacoes_online'] ?? 0, 2, ',', '.') }} AOA</td>
                    <td>Doações recebidas online</td>
                </tr>
                <tr>
                    <td><strong>Total Entradas</strong></td>
                    <td class="value-cell entradas">{{ number_format(($summary['total_entradas'] ?? 0) + ($summary['doacoes_online'] ?? 0), 2, ',', '.') }} AOA</td>
                    <td>Entradas + Doações Online</td>
                </tr>
                <tr>
                    <td><strong>Saídas</strong></td>
                    <td class="value-cell saidas">{{ number_format($summary['total_saidas'] ?? 0, 2, ',', '.') }} AOA</td>
                    <td>Movimentos de saída registrados</td>
                </tr>
                <tr style="border-top: 2px solid #007bff;">
                    <td><strong>RENDIMENTO TOTAL</strong></td>
                    <td class="value-cell rendimento fw-bold">{{ number_format($summary['total_rendimento'] ?? 0, 2, ',', '.') }} AOA</td>
                    <td class="fw-bold">(Entradas + Doações) - Saídas</td>
                </tr>
            </tbody>
        </table>

        <!-- Análise de Rentabilidade -->
        <div class="section-title">Análise de Rentabilidade</div>
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0;">
            <h6 style="margin: 0 0 10px 0; color: #495057;">Indicadores de Performance:</h6>
            <ul style="margin: 0; padding-left: 20px; color: #6c757d;">
                <li><strong>Rentabilidade:</strong> {{ number_format((($summary['total_rendimento'] ?? 0) / max(($summary['total_entradas'] ?? 0) + ($summary['doacoes_online'] ?? 0), 1)) * 100, 1) }}% sobre o total de entradas</li>
                <li><strong>Eficiência:</strong> {{ number_format((($summary['total_entradas'] ?? 0) / max($summary['total_saidas'] ?? 1, 1)) * 100, 1) }}% de entradas vs saídas</li>
                <li><strong>Contribuição Online:</strong> {{ number_format((($summary['doacoes_online'] ?? 0) / max(($summary['total_entradas'] ?? 0) + ($summary['doacoes_online'] ?? 0), 1)) * 100, 1) }}% das entradas totais</li>
            </ul>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Relatório gerado pelo Sistema OmnIgrejas - {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Este relatório contém informações confidenciais da igreja {{ $igreja->nome ?? 'OmnIgrejas' }}</p>
    </div>
</body>
</html>