<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-primary">
                            <i class="fas fa-file-alt me-2"></i>Relatórios de Culto
                        </h1>
                        <p class="mb-0 text-muted">Gerencie os relatórios dos cultos e eventos</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-primary btn-md" wire:click="abrirModalNovo" data-bs-toggle="modal" data-bs-target="#reportModal">
                            <i class="fas fa-plus me-2"></i>Novo Relatório
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Métricas -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-primary metric-card">
                    <div class="card-body">
                        <i class="fas fa-file-alt text-primary display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-primary">{{ $estatisticas['total'] }}</div>
                        <div class="text-muted small">Total de Relatórios</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-success metric-card">
                    <div class="card-body">
                        <i class="fas fa-check-circle text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ $estatisticas['finalizados'] }}</div>
                        <div class="text-muted small">Finalizados</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-warning metric-card">
                    <div class="card-body">
                        <i class="fas fa-edit text-warning display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-warning">{{ $estatisticas['rascunhos'] }}</div>
                        <div class="text-muted small">Rascunhos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-info metric-card">
                    <div class="card-body">
                        <i class="fas fa-calendar-alt text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $estatisticas['este_mes'] }}</div>
                        <div class="text-muted small">Este Mês</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Buscar Relatório</label>
                        <div class="input-group">
                            <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Título ou conteúdo">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Filtrar por Status</label>
                        <select class="form-select" wire:model.live="filtroStatus">
                            <option value="">Todos os status</option>
                            <option value="rascunho">Rascunho</option>
                            <option value="finalizado">Finalizado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Filtrar por Data</label>
                        <select class="form-select" wire:model.live="filtroData">
                            <option value="">Todas as datas</option>
                            <option value="hoje">Hoje</option>
                            <option value="semana">Esta semana</option>
                            <option value="mes">Este mês</option>
                            <option value="recentes">Últimos 7 dias</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary flex-fill" wire:click="clearFilters">
                                <i class="fas fa-filter me-1"></i>Limpar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop: Tabela -->
        <div class="d-none d-lg-block">
            <div class="card">
                <div class="card-header d-flex align-items-center mb-3">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-list-ul me-2"></i>Lista de Relatórios
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Título</th>
                                <th>Data</th>
                                <th>Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                             @forelse($relatorios as $relatorio)
                             <tr>
                                 <td>
                                     <div class="fw-semibold">{{ $relatorio->titulo ?: 'Relatório sem título' }}</div>
                                 </td>
                                 <td>{{ $relatorio->data_relatorio->format('d/m/Y') }}</td>
                                 <td>
                                     <span class="badge bg-{{ $this->getStatusBadgeClass($relatorio->status) }}">
                                         {{ $this->getStatusLabel($relatorio->status) }}
                                     </span>
                                 </td>
                                 <td class="text-center">
                                     <div class="btn-group btn-group-sm">
                                         <button class="btn btn-outline-info" wire:click="visualizarRelatorio('{{ $relatorio->id }}')"  data-bs-toggle="modal" data-bs-target="#viewReportModal"  title="Visualizar">
                                             <i class="fas fa-eye"></i>
                                         </button>
                                         <button class="btn btn-outline-primary" wire:click="exportReport('{{ $relatorio->id }}')" wire:loading.attr="disabled" wire:loading.class="btn-loading" title="Imprimir" wire:target="exportingReport">
                                             <span wire:loading.remove  wire:target="exportReport('{{ $relatorio->id }}')"><i class="fas fa-print"></i></span>
                                             <span wire:loading  wire:target="exportReport('{{ $relatorio->id }}')"><i class="fas fa-spinner fa-spin"></i></span>
                                         </button>
                                         <button class="btn btn-outline-warning" wire:click="abrirModalEditar('{{ $relatorio->id }}')" data-bs-toggle="modal" data-bs-target="#reportModal" title="Editar" >
                                             <i class="fas fa-edit"></i>
                                         </button>
                                         <button class="btn btn-outline-{{ $relatorio->status === 'rascunho' ? 'success' : 'secondary' }}"
                                                wire:click="alterarStatus('{{ $relatorio->id }}')"
                                                wire:loading.attr="disabled"
                                                wire:loading.class="btn-loading"
                                                title="{{ $relatorio->status === 'rascunho' ? 'Finalizar' : 'Mover para rascunho' }}"
                                                wire:target="alterarStatus">
                                            <span wire:loading.remove wire:target="alterarStatus">
                                                <i class="fas fa-{{ $relatorio->status === 'rascunho' ? 'check' : 'undo' }}"></i>
                                            </span>
                                            <span wire:loading wire:target="alterarStatus">
                                                <i class="fas fa-spinner fa-spin"></i>
                                            </span>
                                        </button>
                                         <button class="btn btn-outline-danger" wire:click="abrirModalExclusao('{{ $relatorio->id }}')" data-bs-toggle="modal" data-bs-target="#deleteReportModal" title="Excluir">
                                             <i class="fas fa-trash"></i>
                                         </button>
                                     </div>
                                 </td>
                             </tr>
                             @empty
                             <tr>
                                 <td colspan="4" class="text-center py-4">
                                     <i class="fas fa-file-alt text-muted display-4 mb-3"></i>
                                     <div class="text-muted">Nenhum relatório encontrado</div>
                                 </td>
                             </tr>
                             @endforelse
                         </tbody>
                     </table>
                 </div>
                 <div class="card-footer">
                     <div class="d-flex justify-content-between align-items-center">
                         <span class="text-muted small">Mostrando {{ $relatorios->firstItem() }}-{{ $relatorios->lastItem() }} de {{ $relatorios->total() }} registros</span>
                         <nav aria-label="Paginação">
                             {{ $relatorios->links() }}
                         </nav>
                     </div>
                 </div>
             </div>
         </div>

        <!-- Mobile/Tablet: Cards -->
        <div class="d-lg-none">
            <div class="row g-3">
                @forelse($relatorios as $relatorio)
                <div class="col-12">
                    <div class="card card-hover h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar bg-primary text-white me-3">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-1">{{ $relatorio->titulo ?: 'Relatório sem título' }}</h6>
                                        <span class="badge bg-{{ $this->getStatusBadgeClass($relatorio->status) }}">
                                            {{ $this->getStatusLabel($relatorio->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted">{{ $relatorio->data_relatorio->format('d/m/Y') }}</small>
                                </div>
                            </div>


                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-info btn-sm" wire:click="visualizarRelatorio({{ $relatorio->id }})">
                                    <i class="fas fa-eye me-1"></i>Ver
                                </button>
                                <button class="btn btn-outline-primary btn-sm" wire:click="exportReport({{ $relatorio->id }})" wire:loading.attr="disabled" wire:loading.class="btn-loading">
                                    <span wire:loading.remove><i class="fas fa-print me-1"></i>Imprimir</span>
                                    <span wire:loading><i class="fas fa-spinner fa-spin me-1"></i>Gerando...</span>
                                </button>
                                <button class="btn btn-warning btn-sm flex-fill" wire:click="abrirModalEditar({{ $relatorio->id }})" data-bs-toggle="modal" data-bs-target="#reportModal">
                                    <i class="fas fa-edit me-1"></i>Editar
                                </button>
                                <button class="btn btn-outline-danger btn-sm" wire:click="abrirModalExclusao({{ $relatorio->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-file-alt text-muted display-4 mb-3"></i>
                            <div class="text-muted">Nenhum relatório encontrado</div>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Paginação Mobile -->
            @if($relatorios->hasPages())
            <div class="mt-4">
                <nav aria-label="Paginação Mobile">
                    {{ $relatorios->links() }}
                </nav>
                <div class="text-center text-muted mt-2">
                    <small>Mostrando {{ $relatorios->firstItem() }}-{{ $relatorios->lastItem() }} de {{ $relatorios->total() }} registros</small>
                </div>
            </div>
            @endif
        </div>

        <!-- Scripts para Reports -->
        <script src="{{ asset('system/js/reports.js') }}" data-navigate-once></script>

        {{-- REPORTS MODALS --}}
        @include('church.reports.modals.report-modal')
        @include('church.reports.modals.view-report-modal')
        @include('church.reports.modals.delete-report-modal')
    </div>
</div>
