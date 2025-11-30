<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Estatísticas - {{ $igreja->nome ?? 'Igreja' }}</title>
    <style>
        @page {
            margin: 20mm;
            size: {{ strtoupper($paperSize ?? 'A4') }};
        }

        /* Ajustes específicos para A3 */
        @if(($paperSize ?? 'a4') === 'a3')
        .stats-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 15px 10px;
        }

        .stats-grid .stat-card {
            display: table-cell;
            width: 25%; /* 4 colunas em A3 */
            vertical-align: top;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .data-table {
            font-size: 11px; /* Fonte um pouco maior em A3 */
        }

        .data-table th,
        .data-table td {
            padding: 10px 12px; /* Padding maior em A3 */
        }

        .section-title {
            font-size: 18px; /* Títulos maiores em A3 */
            margin: 40px 0 20px 0;
        }

        /* Reduzir quebras de página em A3 para otimizar espaço */
        .section-break {
            page-break-before: auto; /* Menos quebras forçadas */
        }

        /* Ajustar largura das tabelas para usar melhor o espaço A3 */
        .data-table {
            width: 100%;
            max-width: none;
        }

        /* Otimizar espaço entre seções em A3 */
        .mt-20, .mb-20 {
            margin-top: 30px;
            margin-bottom: 30px;
        }

        /* Header mais espaçado em A3 */
        .header {
            margin-bottom: 40px;
        }

        .stats-grid {
            margin-bottom: 40px;
        }

        /* Permitir mais conteúdo por página em A3 */
        .charts-section,
        .data-table,
        .stats-grid {
            page-break-inside: auto;
        }
        @endif

        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .logo-section {
            flex: 1;
        }

        .logo {
            font-size: 20px;
            font-weight: bold;
            color: #007bff;
            margin: 0;
        }

        .subtitle {
            font-size: 8px;
            color: #666;
            margin: 3px 0 0 0;
        }

        .church-info {
            flex: 2;
            text-align: center;
        }

        .church-name {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin: 0;
        }

        .church-details {
            font-size: 9px;
            color: #666;
            margin: 3px 0 0 0;
        }

        .report-title {
            flex: 1;
            text-align: right;
        }

        .report-title-text {
            font-size: 14px;
            font-weight: bold;
            color: #007bff;
            margin: 0;
        }

        .report-subtitle {
            font-size: 9px;
            color: #666;
            margin: 3px 0 0 0;
        }

        .generation-info {
            text-align: right;
            font-size: 9px;
            color: #666;
            margin-bottom: 10px;
        }

        .compact-table {
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .compact-table th {
            background: #f8f9fa;
            font-size: 10px;
            padding: 6px 8px;
        }

        .compact-table td {
            font-size: 9px;
            padding: 6px 8px;
        }


        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10px;
            page-break-inside: auto;
        }

        .data-table thead {
            display: table-header-group;
            background: #f8f9fa;
        }

        .data-table thead th {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            color: #333;
            background: #f8f9fa;
            position: sticky;
            top: 0;
        }

        .data-table tbody {
            display: table-row-group;
        }

        .data-table tbody td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .data-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        .data-table tbody tr:nth-child(odd) {
            background: #ffffff;
        }

        /* Garantir que o cabeçalho se repita em cada página */
        .data-table {
            -fs-table-paginate: paginate;
        }

        /* Evitar quebra de linha dentro das células */
        .data-table td {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* Regras específicas para quebra de página em tabelas */
        .data-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .data-table thead tr {
            page-break-after: avoid;
        }

        .data-table tbody tr:last-child {
            page-break-after: avoid;
        }

        /* Espaçamento adequado entre tabelas e seções */
        .data-table {
            margin-bottom: 30px;
        }

        /* Garantir que títulos de seção não quebrem */
        .section-title {
            page-break-after: avoid;
            margin-bottom: 15px;
        }

        /* Adicionar espaço extra antes de novas seções */
        .section-break {
            height: 50px;
            page-break-before: always;
        }

        /* Para tabelas muito grandes, permitir quebra controlada */
        .data-table.large-table {
            page-break-inside: auto;
        }

        .data-table.large-table tbody tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        /* Separar cabeçalho do corpo com espaço moderado e linha divisória */
        .data-table.large-table thead::after {
            content: '';
            display: block;
            height: 20px; /* Espaço moderado após o cabeçalho */
            background: linear-gradient(to right, #007bff, #28a745, #ffc107, #dc3545);
            margin: 15px 0;
            border-radius: 2px;
        }

        /* Adicionar margem moderada no topo do corpo da tabela */
        .data-table.large-table tbody {
            margin-top: 25px; /* Margem moderada para separar o corpo */
        }

        /* Linha divisória mais visível entre cabeçalho e corpo */
        .data-table.large-table thead th {
            border-bottom: 3px solid #007bff;
            position: relative;
        }

        .data-table.large-table thead th::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #007bff 0%, #28a745 50%, #ffc107 100%);
        }

        /* Espaçamento moderado entre seções em tabelas grandes */
        .data-table.large-table {
            margin: 25px 0 35px 0;
        }

        /* Garantir que o cabeçalho tenha destaque visual */
        .data-table.large-table thead {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Adicionar quebra de página antes de tabelas grandes se necessário */
        .data-table.large-table {
            page-break-before: auto;
            page-break-after: auto;
        }

        /* Estilo especial para separação de cabeçalho em tabelas grandes */
        .table-header-separator {
            border-bottom: 4px solid #007bff;
            margin-bottom: 20px;
            position: relative;
        }

        .table-header-separator::after {
            content: '▼ CORPO DA TABELA CONTINUA ABAIXO ▼';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 5px 15px;
            border: 2px solid #007bff;
            border-radius: 15px;
            font-size: 9px;
            font-weight: bold;
            color: #007bff;
            white-space: nowrap;
        }

        /* Espaçamento maior para tabelas que serão quebradas */
        .data-table.large-table.break-after-header {
            margin-bottom: 60px;
        }

        .data-table.large-table.break-after-header thead {
            margin-bottom: 30px;
        }

        /* Indicador visual de continuação */
        .table-continuation-indicator {
            text-align: center;
            font-size: 10px;
            color: #666;
            margin: 20px 0;
            padding: 10px;
            border-top: 1px dashed #ccc;
            border-bottom: 1px dashed #ccc;
            display: none; /* Escondido por padrão */
        }

        .table-continuation-indicator::before {
            content: '↕️';
            margin-right: 5px;
        }

        /* Mostrar indicador apenas quando a tabela é quebrada */
        .data-table.large-table tbody:first-child .table-continuation-indicator {
            display: table-row;
        }

        /* Estilo para quando a tabela continua em outra página */
        .data-table.large-table {
            position: relative;
        }

        .data-table.large-table::before {
            content: '';
            position: absolute;
            top: -10px;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent 0%, #007bff 20%, #007bff 80%, transparent 100%);
            display: none;
        }

        /* Mostrar linha decorativa apenas em tabelas grandes */
        .data-table.large-table::before {
            display: block;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
            margin: 20px 0 15px 0;
            page-break-after: avoid;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            page-break-inside: auto;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table tbody {
            display: table-row-group;
        }

        .data-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .data-table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #333;
            page-break-after: avoid;
        }

        .data-table td {
            page-break-inside: avoid;
        }

        /* Quebra de página antes de cada seção principal */
        .section-break {
            page-break-before: always;
        }

        /* Evitar quebra de página após títulos */
        h4, h5 {
            page-break-after: avoid;
        }

        /* Garantir que tabelas não quebrem no meio */
        .data-table, .data-table * {
            page-break-inside: avoid;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 9px;
            color: #666;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-left {
            text-align: left;
        }

        .footer-center {
            text-align: center;
            flex: 1;
        }

        .footer-right {
            text-align: right;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        .mt-20 {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo-section">
            <h1 class="logo">OMNIGREJAS</h1>
            <p class="subtitle">Sistema de Gestão Eclesiástica</p>
        </div>

        <div class="church-info">
            <h2 class="church-name">{{ $igreja->nome ?? 'Nome da Igreja' }}</h2>
            <p class="church-details">

            </p>
        </div>

        <div class="report-title">
            <h3 class="report-title-text">Relatório de Estatísticas</h3>
            <p class="report-subtitle">
                @if($startDate->year !== $endDate->year)
                    Período: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
                @else
                    Período: {{ $startDate->format('d/m') }} - {{ $endDate->format('d/m/Y') }}
                @endif
            </p>
        </div>
    </div>

    <!-- Statistics Table -->
    @if(($paperSize ?? 'a4') === 'a3' && ($stats['total_members'] ?? 0) > 100)

    @endif
    <h4 class="section-title" style="margin-top: 10px;">📊 Métricas Principais</h4>
    <table class="data-table compact-table">
        <thead>
            <tr>
                <th>Métrica</th>
                <th>Valor</th>
                <th>Detalhes</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Total de Membros</strong></td>
                <td class="text-center"><strong>{{ $stats['total_members'] ?? 0 }}</strong></td>
                <td>+{{ $stats['members_growth'] ?? 0 }}% este mês</td>
            </tr>
            <tr>
                <td><strong> Membros Ativos</strong></td>
                <td class="text-center"><strong>{{ $stats['active_members'] ?? 0 }}</strong></td>
                <td>{{ $stats['active_percentage'] ?? 0 }}% do total</td>
            </tr>
            <tr>
                <td><strong>Taxa de Retenção</strong></td>
                <td class="text-center"><strong>{{ $stats['retention_rate'] ?? 0 }}%</strong></td>
                <td>Membros que permanecem ativos</td>
            </tr>
            <tr>
                <td><strong>Idade Média</strong></td>
                <td class="text-center"><strong>{{ $stats['average_age'] ?? 0 }}</strong></td>
                <td>Anos de idade</td>
            </tr>
            <tr>
                <td><strong>Eventos Este Mês</strong></td>
                <td class="text-center"><strong>{{ $stats['events_this_month'] ?? 0 }}</strong></td>
                <td>{{ $stats['avg_attendance'] ?? 0 }} participantes/evento</td>
            </tr>
            <tr>
                <td><strong> Cursos Ativos</strong></td>
                <td class="text-center"><strong>{{ $stats['active_courses'] ?? 0 }}</strong></td>
                <td>{{ $stats['total_students'] ?? 0 }} alunos matriculados</td>
            </tr>
            <tr>
                <td><strong>Taxa de Conversão</strong></td>
                <td class="text-center"><strong>{{ $stats['conversion_rate'] ?? 0 }}%</strong></td>
                <td>Batismos/decisões</td>
            </tr>
            <tr>
                <td><strong>Aniversariantes</strong></td>
                <td class="text-center"><strong>{{ count($stats['birthdays_this_month'] ?? []) }}</strong></td>
                <td>Este mês</td>
            </tr>
        </tbody>
    </table>

    <!-- Roles Distribution Table -->
    <h5 style="color: #007bff; margin: 10px 0 8px 0; font-size: 12px;"> Distribuição por Cargo</h5>
    <table class="data-table compact-table">
        <thead>
            <tr>
                <th>Cargo</th>
                <th>Número de Membros</th>
                <th>Percentual</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($chartData['roles']))
                @php $totalRoles = array_sum($chartData['roles']['data']); @endphp
                @foreach($chartData['roles']['labels'] as $index => $label)
                    @php
                        $count = $chartData['roles']['data'][$index] ?? 0;
                        $percentage = $totalRoles > 0 ? ($count / $totalRoles) * 100 : 0;
                    @endphp
                    <tr>
                        <td>{{ $label }}</td>
                        <td class="text-center">{{ $count }}</td>
                        <td class="text-center">{{ number_format($percentage, 1) }}%</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3" class="text-center">Dados não disponíveis</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Events Table -->
    <h5 style="color: #007bff; margin: 10px 0 8px 0; font-size: 12px;">Frequência de Eventos</h5>
    <table class="data-table compact-table">
        <thead>
            <tr>
                <th>Tipo de Evento</th>
                <th>Número de Eventos</th>
                <th>Percentual</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($chartData['events']))
                @php $totalEvents = array_sum($chartData['events']['data']); @endphp
                @foreach($chartData['events']['labels'] as $index => $label)
                    @php
                        $count = $chartData['events']['data'][$index] ?? 0;
                        $percentage = $totalEvents > 0 ? ($count / $totalEvents) * 100 : 0;
                    @endphp
                    <tr>
                        <td>{{ $label }}</td>
                        <td class="text-center">{{ $count }}</td>
                        <td class="text-center">{{ number_format($percentage, 1) }}%</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3" class="text-center">Dados não disponíveis</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Ministries Table -->
    <h5 style="color: #007bff; margin: 10px 0 8px 0; font-size: 12px;">🤝 Engajamento por Ministério</h5>
    <table class="data-table compact-table">
        <thead>
            <tr>
                <th>Ministério</th>
                <th>Número de Membros</th>
                <th>Percentual</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($chartData['ministries']))
                @php $totalMinistry = array_sum($chartData['ministries']['data']); @endphp
                @foreach($chartData['ministries']['labels'] as $index => $label)
                    @php
                        $count = $chartData['ministries']['data'][$index] ?? 0;
                        $percentage = $totalMinistry > 0 ? ($count / $totalMinistry) * 100 : 0;
                    @endphp
                    <tr>
                        <td>{{ $label }}</td>
                        <td class="text-center">{{ $count }}</td>
                        <td class="text-center">{{ number_format($percentage, 1) }}%</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3" class="text-center">Dados não disponíveis</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Members Growth Table -->
    <h5 style="color: #007bff; margin: 10px 0 8px 0; font-size: 12px;"> Crescimento de Membros</h5>
    <table class="data-table compact-table" >
        <thead>
            <tr>
                <th>Mês</th>
                <th>Número de Membros</th>
                <th>Crescimento</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($chartData['members']))
                @foreach($chartData['members']['labels'] as $index => $label)
                    @php
                        $current = $chartData['members']['data'][$index] ?? 0;
                        $previous = $index > 0 ? ($chartData['members']['data'][$index - 1] ?? 0) : 0;
                        $growth = $previous > 0 ? (($current - $previous) / $previous) * 100 : 0;

                        // Melhorar formatação do label
                        if (str_contains($label, '/')) {
                            // Já tem ano (formato M/Y ou d/m)
                            $formattedLabel = $label;
                        } elseif (str_contains($label, 'Sem ')) {
                            // Semana - manter como está
                            $formattedLabel = $label;
                        } else {
                            // Mês sem ano - adicionar ano atual se for período customizado
                            $currentYear = date('Y');
                            $formattedLabel = $label . '/' . $currentYear;
                        }
                    @endphp
                    <tr>
                        <td>{{ $formattedLabel }}</td>
                        <td class="text-center">{{ $current }}</td>
                        <td class="text-center" style="color: {{ $growth >= 0 ? '#28a745' : '#dc3545' }};">
                            {{ $growth >= 0 ? '+' : '' }}{{ number_format($growth, 1) }}%
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3" class="text-center">Dados não disponíveis</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Top Members Table -->
    <!-- Quebra condicional: apenas quebra se A3 com muitos membros -->
    @if(($paperSize ?? 'a4') === 'a3' && count($topMembers ?? []) > 5)
        <div class="section-break"></div>
    @endif
    <h4 class="section-title"> Membros Mais Ativos</h4>
    <!-- Tabela com separação visual entre cabeçalho e corpo -->
    <table class="data-table large-table">
        <thead class="table-header-separator">
            <tr>
                <th>Nome</th>
                <th>Cargo</th>
                <th>Pontos</th>
                <th>Atividades</th>
            </tr>
        </thead>
        <tbody>
            <tr class="table-continuation-indicator" style="display: none;">
                <td colspan="4"> CONTINUAÇÃO DA TABELA </td>
            </tr>
        <tbody>
            @forelse($topMembers ?? [] as $member)
            <tr>
                <td>{{ $member->user->name ?? 'Nome não disponível' }}</td>
                <td>{{ $member->cargo ?? 'Não informado' }}</td>
                <td class="text-center">
                    <span class="badge badge-success">{{ $member->total_points ?? 0 }}</span>
                </td>
                <td class="text-center">{{ $member->activities_count ?? 0 }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">Nenhum dado disponível</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Popular Events Table -->
    <!-- Quebra condicional: apenas quebra se A3 com muitos eventos -->
    @if(($paperSize ?? 'a4') === 'a3' && count($popularEvents ?? []) > 3)
        <div class="section-break"></div>
    @endif
    <h4 class="section-title"> Eventos Mais Populares</h4>
    <!-- Tabela com separação visual entre cabeçalho e corpo -->
    <table class="data-table large-table">
        <thead class="table-header-separator">
            <tr>
                <th>Título</th>
                <th>Tipo</th>
                <th>Participantes</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            <tr class="table-continuation-indicator" style="display: none;">
                <td colspan="4">CONTINUAÇÃO DA TABELA </td>
            </tr>
        <tbody>
            @forelse($popularEvents ?? [] as $event)
            <tr>
                <td>{{ $event->titulo ?? 'Título não disponível' }}</td>
                <td>{{ $event->tipo ?? 'Tipo não informado' }}</td>
                <td class="text-center">
                    <span class="badge badge-info">{{ $event->participants_count ?? 0 }}</span>
                </td>
                <td class="text-right">{{ $event->data_evento ? $event->data_evento->format('d/m/Y') : 'Data não definida' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">Nenhum evento encontrado</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Financial Summary Table -->
    @if(($paperSize ?? 'a4') === 'a3' && ($financialSummary['raw_income'] ?? 0) > 10000)
        <div class="section-break"></div>
    @endif
    <h4 class="section-title"> Resumo Financeiro</h4>
    <table class="data-table compact-table">
        <thead>
            <tr>
                <th>Categoria</th>
                <th>Valor</th>
                <th>Descrição</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Receitas</strong></td>
                <td class="text-right" style="color: #28a745;"><strong>{{ $financialSummary['income'] ?? '0,00 AOA' }}</strong></td>
                <td>Entradas no período</td>
            </tr>
            <tr>
                <td><strong>Despesas</strong></td>
                <td class="text-right" style="color: #dc3545;"><strong>{{ $financialSummary['expenses'] ?? '0,00 AOA' }}</strong></td>
                <td>Saídas no período</td>
            </tr>
            <tr>
                <td><strong>Saldo</strong></td>
                <td class="text-right" style="color: {{ ($financialSummary['raw_balance'] ?? 0) >= 0 ? '#28a745' : '#dc3545' }};">
                    <strong>{{ $financialSummary['balance'] ?? '0,00 AOA' }}</strong>
                </td>
                <td>Receitas - Despesas</td>
            </tr>
            <tr>
                <td><strong>Doações</strong></td>
                <td class="text-right" style="color: #6f42c1;"><strong>{{ $financialSummary['donations'] ?? '0,00 AOA' }}</strong></td>
                <td>Ofertas e contribuições</td>
            </tr>
        </tbody>
    </table>

    <!-- Financial Details Table -->
    <h5 style="color: #007bff; margin: 15px 0 10px 0; font-size: 14px;">📊 Detalhes Financeiros do Período</h5>
    <table class="data-table compact-table">
        <thead>
            <tr>
                <th>Categoria</th>
                <th>Entradas</th>
                <th>Saídas</th>
                <th>Saldo</th>
            </tr>
        </thead>
        <tbody>
            @php
                $categorias = \App\Models\Financeiro\FinanceiroCategoria::where('igreja_id', $igreja->id ?? null)->get();
            @endphp

            @forelse($categorias as $categoria)
                @php
                    $entradas = \App\Models\Financeiro\FinanceiroMovimento::where('igreja_id', $igreja->id ?? null)
                        ->where('categoria_id', $categoria->id)
                        ->where('tipo', 'entrada')
                        ->whereBetween('data_transacao', [$startDate, $endDate])
                        ->sum('valor');

                    $saidas = \App\Models\Financeiro\FinanceiroMovimento::where('igreja_id', $igreja->id ?? null)
                        ->where('categoria_id', $categoria->id)
                        ->where('tipo', 'saida')
                        ->whereBetween('data_transacao', [$startDate, $endDate])
                        ->sum('valor');

                    $saldo = $entradas - $saidas;
                @endphp
                <tr>
                    <td>{{ $categoria->nome }}</td>
                    <td class="text-right">{{ number_format($entradas, 2, ',', '.') }} AOA</td>
                    <td class="text-right">{{ number_format($saidas, 2, ',', '.') }} AOA</td>
                    <td class="text-right" style="color: {{ $saldo >= 0 ? '#28a745' : '#dc3545' }};">
                        {{ number_format($saldo, 2, ',', '.') }} AOA
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Nenhuma categoria financeira encontrada</td>
                </tr>
            @endforelse

            <!-- Total Row -->
            @if($categorias->count() > 0)
            <tr style="background: #f8f9fa; font-weight: bold;">
                <td><strong>TOTAL GERAL</strong></td>
                <td class="text-right"><strong>{{ $financialSummary['income'] ?? '0,00 AOA' }}</strong></td>
                <td class="text-right"><strong>{{ $financialSummary['expenses'] ?? '0,00 AOA' }}</strong></td>
                <td class="text-right" style="color: {{ ($financialSummary['raw_balance'] ?? 0) >= 0 ? '#28a745' : '#dc3545' }};">
                    <strong>{{ $financialSummary['balance'] ?? '0,00 AOA' }}</strong>
                </td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-content">
            <div class="footer-left">
                <strong>OMNIGREJAS</strong><br>
                Sistema de Gestão Eclesiástica
            </div>
            <div class="footer-center">
                Relatório gerado automaticamente<br>
                Página 1 de 1
            </div>
            <div class="footer-right">
                <!-- Generation Info -->
                <div class="generation-info">
                    Gerado em: {{ now()->format('d/m/Y H:i:s') }}
                </div>
                {{ now()->format('d/m/Y H:i') }}<br>
                www.omnigrejas.com
            </div>
        </div>
    </div>
</body>
</html>

