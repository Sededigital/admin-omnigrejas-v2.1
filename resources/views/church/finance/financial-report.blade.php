<div>
    <div class="container-fluid p-4">
        <!-- Scripts necessários -->
        <script src="{{ asset('system/js/financial-reports.js') }}" data-navigate-once></script>
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-primary">
                            <i class="fas fa-chart-line me-2"></i>Relatórios Financeiros
                        </h1>
                        <p class="mb-0 text-muted">Dashboards e relatórios detalhados das finanças da igreja</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="btn-group" role="group">
                            <button class="btn btn-success btn-sm" wire:click="exportPDF" wire:loading.attr="disabled" title="Exportar PDF">
                                <span wire:loading.remove wire:target="exportPDF">
                                    <i class="fas fa-file-pdf"></i>
                                </span>
                                <span wire:loading wire:target="exportPDF">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                            </button>
                            <button class="btn btn-primary btn-sm" wire:click="exportExcel" wire:loading.attr="disabled" title="Exportar Excel">
                                <span wire:loading.remove wire:target="exportExcel">
                                    <i class="fas fa-file-excel"></i>
                                </span>
                                <span wire:loading wire:target="exportExcel">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                            </button>
                            <button class="btn btn-info btn-sm" wire:click="exportCSV" wire:loading.attr="disabled" title="Exportar CSV">
                                <span wire:loading.remove wire:target="exportCSV">
                                    <i class="fas fa-file-csv"></i>
                                </span>
                                <span wire:loading wire:target="exportCSV">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros de Período -->
        <div class="filter-section">
            <div class="filter-title">
                <i class="fas fa-filter"></i>
                Filtros de Relatório
            </div>
            <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Período</label>
                        <select class="form-select" wire:model.live="selectedPeriod">
                            <option value="month">Este Mês</option>
                            <option value="quarter">Este Trimestre</option>
                            <option value="year">Este Ano</option>
                            <option value="custom">Período Personalizado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Data Inicial</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-calendar text-muted"></i>
                            </span>
                            <input type="date"
                                   class="form-control  @error('dateFrom') is-invalid @enderror"
                                   wire:model.defer="dateFrom"
                                   min="2010-01-01"
                                   max="{{ date('Y-m-d') }}">
                        </div>
                        @error('dateFrom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Data Final</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-calendar text-muted"></i>
                            </span>
                            <input type="date"
                                   class="form-control @error('dateTo') is-invalid @enderror"
                                   wire:model.defer="dateTo"
                                   min="2010-01-01"
                                   max="{{ date('Y-m-d') }}">
                        </div>
                        @error('dateTo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tipo de Relatório</label>
                        <select class="form-select" wire:model.live="reportType">
                            <option value="summary">Resumo Geral</option>
                            <option value="detailed">Detalhado</option>
                            <option value="category">Por Categoria</option>
                            <option value="account">Por Conta</option>
                            <option value="rendimento">Rendimento</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary flex-fill btn-apply d-inline-flex align-items-center justify-content-center" wire:click="applyFilters" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="applyFilters">
                                    <i class="fas fa-search me-1"></i>Aplicar Filtros
                                </span>
                                <span wire:loading wire:target="applyFilters">
                                    <i class="fas fa-spinner fa-spin me-1 d-inline-flex align-items-center justify-content-center"></i>Processando...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Resumo -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-success metric-card">
                    <div class="card-body">
                        <i class="fas fa-arrow-up text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ number_format($summary['total_entradas'] ?? 0, 2, ',', '.') }} AOA</div>
                        <div class="text-muted small">Total Entradas</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-danger metric-card">
                    <div class="card-body">
                        <i class="fas fa-arrow-down text-danger display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-danger">{{ number_format($summary['total_saidas'] ?? 0, 2, ',', '.') }} AOA</div>
                        <div class="text-muted small">Total Saídas</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-primary metric-card">
                    <div class="card-body">
                        <i class="fas fa-balance-scale text-primary display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-primary">{{ number_format($summary['saldo_liquido'] ?? 0, 2, ',', '.') }} AOA</div>
                        <div class="text-muted small">Saldo Líquido</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-info metric-card">
                    <div class="card-body">
                        <i class="fas fa-percentage text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ number_format($summary['percentual_economia'] ?? 0, 1) }}%</div>
                        <div class="text-muted small">% Economia</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos e Análises -->
        <div class="row g-3 mb-4" wire:ignore.self>
            <!-- Gráfico de Entradas vs Saídas -->
            <div class="col-12 col-lg-6">
                <div class="card chart-card">
                    <div class="card-header py-2">
                        <h6 class="mb-0 text-primary fw-bold">
                            <i class="fas fa-chart-pie me-2"></i>Entradas vs Saídas
                        </h6>
                    </div>
                    <div class="card-body py-2">
                        <canvas id="entriesVsExitsChart" width="400" height="200" style="max-height: 200px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Gráfico por Categoria -->
            <div class="col-12 col-lg-6" wire:ignore.self>
                <div class="card chart-card">
                    <div class="card-header py-2">
                        <h6 class="mb-0 text-primary fw-bold">
                            <i class="fas fa-chart-bar me-2"></i>Por Categoria
                        </h6>
                    </div>
                    <div class="card-body py-2">
                        <canvas id="categoryChart" width="400" height="200" style="max-height: 200px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabela de Movimentos por Categoria -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-list-ul me-2"></i>Movimentos por Categoria
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Categoria</th>
                                <th>Entradas</th>
                                <th>Saídas</th>
                                <th>Saldo</th>
                                <th>% do Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categorySummary as $category)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $category['nome'] }}</div>
                                </td>
                                <td class="text-success">
                                    {{ number_format($category['entradas'], 2, ',', '.') }} AOA
                                </td>
                                <td class="text-danger">
                                    {{ number_format($category['saidas'], 2, ',', '.') }} AOA
                                </td>
                                <td class="fw-bold {{ $category['saldo'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($category['saldo'], 2, ',', '.') }} AOA
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-primary" role="progressbar"
                                             style="width: {{ $category['percentual'] }}%"
                                             aria-valuenow="{{ $category['percentual'] }}"
                                             aria-valuemin="0" aria-valuemax="100">
                                            {{ number_format($category['percentual'], 1) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-chart-line text-muted display-4 mb-3"></i>
                                    <div class="text-muted">Nenhum dado encontrado para o período</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Análise por Conta -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-university me-2"></i>Análise por Conta
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @forelse($accountSummary as $account)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card border">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div>
                                        <h6 class="card-title mb-1">{{ $account['banco'] }} - {{ $account['numero_conta'] }}</h6>
                                        <small class="text-muted">{{ $account['banco'] }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-{{ $account['ativa'] ? 'success' : 'secondary' }}">
                                            {{ $account['ativa'] ? 'Ativa' : 'Inativa' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="text-center">
                                            <div class="text-success fw-bold">{{ number_format($account['entradas'], 2, ',', '.') }} AOA</div>
                                            <small class="text-muted">Entradas</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <div class="text-danger fw-bold">{{ number_format($account['saidas'], 2, ',', '.') }} AOA</div>
                                            <small class="text-muted">Saídas</small>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="text-center">
                                    <div class="fw-bold h5 {{ $account['saldo'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($account['saldo'], 2, ',', '.') }} AOA
                                    </div>
                                    <small class="text-muted">Saldo Atual</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-university text-muted display-4 mb-3"></i>
                                <div class="text-muted">Nenhuma conta encontrada</div>
                            </div>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Export Options -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 text-primary">
                    <i class="fas fa-download me-2"></i>Exportar Relatório
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <button class="btn btn-success w-100" wire:click="exportPDF">
                            <i class="fas fa-file-pdf me-2"></i>PDF
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100" wire:click="exportExcel">
                            <i class="fas fa-file-excel me-2"></i>Excel
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-info w-100" wire:click="exportCSV">
                            <i class="fas fa-file-csv me-2"></i>CSV
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-secondary w-100" wire:click="printReport">
                            <i class="fas fa-print me-2"></i>Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estilos para os filtros -->
<style>
    /* Melhorar aparência dos filtros */
    .filter-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid #dee2e6;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .filter-title {
        color: #495057;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-title i {
        color: #007bff;
    }

    /* Melhorar botões */
    .btn-apply {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-apply:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,123,255,0.3);
    }

    /* Loading states */
    .filter-loading {
        opacity: 0.6;
        pointer-events: none;
    }

    /* Cards métricos melhorados */
    .metric-card {
        transition: all 0.3s ease;
        border-radius: 12px;
    }

    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
    }

    .icon-interactive {
        transition: all 0.3s ease;
    }

    .metric-card:hover .icon-interactive {
        transform: scale(1.1);
    }

    /* Compactar cards dos gráficos */
    .chart-card {
        max-height: 300px;
    }

    .chart-card .card-body {
        padding: 0.75rem;
        min-height: auto;
    }

    .chart-card .card-header {
        padding: 0.5rem 0.75rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #dee2e6;
    }

    .chart-card canvas {
        max-height: 200px !important;
        width: 100% !important;
        height: auto !important;
    }

    /* Responsividade para gráficos */
    @media (max-width: 768px) {
        .chart-card {
            margin-bottom: 1rem;
        }

        .chart-card canvas {
            max-height: 150px !important;
        }
    }
</style>

    <!-- Dados para gráficos -->
    <script>
        window.financialData = {
            totalEntradas: {{ $summary['total_entradas'] ?? 0 }},
            totalSaidas: {{ $summary['total_saidas'] ?? 0 }},
            categoryNames: @json(collect($categorySummary)->pluck('nome')),
            categorySaldos: @json(collect($categorySummary)->pluck('saldo'))
        };
    </script>
    </div>
</div>


