<div>
    {{-- Seção do cabeçalho --}}
    <div class="iq-navbar-header m-0" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h1>Logs de Assinaturas</h1>
                            <p>Histórico completo de todas as ações relacionadas às assinaturas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="iq-header-img">
            <img src="{{ asset('assets/images/dashboard/top-header.png') }}" alt="header" class="theme-color-default-img img-fluid w-100 h-100 animated-scaleX">
            <img src="{{ asset('assets/images/dashboard/top-header1.png') }}" alt="header" class="theme-color-purple-img img-fluid w-100 h-100 animated-scaleX">
            <img src="{{ asset('assets/images/dashboard/top-header2.png') }}" alt="header" class="theme-color-blue-img img-fluid w-100 h-100 animated-scaleX">
            <img src="{{ asset('assets/images/dashboard/top-header3.png') }}" alt="header" class="theme-color-green-img img-fluid w-100 h-100 animated-scaleX">
            <img src="{{ asset('assets/images/dashboard/top-header4.png') }}" alt="header" class="theme-color-yellow-img img-fluid w-100 h-100 animated-scaleX">
            <img src="{{ asset('assets/images/dashboard/top-header5.png') }}" alt="header" class="theme-color-pink-img img-fluid w-100 h-100 animated-scaleX">
        </div>
    </div>

    {{-- Conteúdo principal --}}
    <div class="container-fluid">
        <div class="row">
        {{-- Filtros e busca --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text"  autocomplete="new-password" class="form-control" placeholder="Buscar..." wire:model.live.debounce.300ms="search">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select form-select-sm" wire:model.live="acaoFilter">
                                <option value="">Todas ações</option>
                                @foreach($acaoOptions as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select form-select-sm" wire:model.live="igrejaFilter">
                                <option value="">Todas igrejas</option>
                                @foreach($igrejas as $igreja)
                                    <option value="{{ $igreja->id }}">{{ Str::limit($igreja->nome, 15) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select form-select-sm" wire:model.live="pacoteFilter">
                                <option value="">Todos pacotes</option>
                                @foreach($pacotes as $pacote)
                                    <option value="{{ $pacote->id }}">{{ Str::limit($pacote->nome, 15) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control form-control-sm" wire:model.live="dataInicio">
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control form-control-sm" wire:model.live="dataFim">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="clearFilters">
                                <i class="fas fa-eraser me-1"></i>
                                Limpar Filtros
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lista de Logs --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Logs de Assinaturas ({{ $logs->total() }})</h4>
                    <div class="dropdown">
                        <select class="form-select" wire:model.live="perPage">
                            <option value="10">10 por página</option>
                            <option value="25">25 por página</option>
                            <option value="50">50 por página</option>
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Data/Hora</th>
                                    <th>Igreja</th>
                                    <th>Pacote</th>
                                    <th>Ação</th>
                                    <th>Descrição</th>
                                    <th>Usuário</th>
                                    <th>Detalhes</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <small class="text-muted fw-bold">{{ $log->data_acao ? $log->data_acao->format('d/m/Y') : 'Data não disponível' }}</small>
                                            <small class="text-muted">{{ $log->data_acao ? $log->data_acao->format('H:i:s') : '' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-40 me-3 bg-soft-primary rounded">
                                                <span class="avatar-title">{{ $log->igreja ? substr($log->igreja->nome, 0, 1) : 'I' }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $log->igreja ? $log->igreja->nome : 'Igreja não encontrada' }}</h6>
                                                <small class="text-muted">{{ $log->igreja ? $log->igreja->nif : '-' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($log->pacote)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-32 me-2 bg-soft-success rounded">
                                                    <span class="avatar-title">{{ substr($log->pacote->nome, 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <span class="fw-bold">{{ $log->pacote->nome }}</span><br>
                                                    <small class="text-muted">{{ number_format($log->pacote->preco ?? 0, 2, ',', '.') }} Kz</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">Pacote não encontrado</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $this->getAcaoBadgeClass($log->acao ?? 'default') }}">
                                            {{ $acaoOptions[$log->acao] ?? ($log->acao ?? 'Ação desconhecida') }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ Str::limit($log->descricao ?? '-', 40) }}</small>
                                    </td>
                                    <td>
                                        @if($log->usuario)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-32 me-2 bg-soft-info rounded">
                                                    <span class="avatar-title">{{ substr($log->usuario->name, 0, 1) }}</span>
                                                </div>
                                                <small class="text-muted">{{ $log->usuario->name }}</small>
                                            </div>
                                        @else
                                            <small class="text-muted">Sistema</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->detalhes && count($log->detalhes) > 0)
                                            <button class="btn btn-sm btn-outline-info"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#detalhesModal"
                                                    data-detalhes="{{ json_encode($log->detalhes) }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                title="Excluir log"
                                                wire:click="deleteLog({{ $log->id }})"
                                                wire:confirm="Tem certeza que deseja excluir este log?">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-history fa-2x mb-2"></i>
                                            <p>Nenhum log encontrado.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($logs->hasPages())
                <div class="card-footer">
                    {{ $logs->links() }}
                </div>
                @endif
            </div>
        </div>
              
        </div>
    </div>

    {{-- Modal de Detalhes --}}
    <div class="modal fade" id="detalhesModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Detalhes do Log
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="detalhes-content" class="text-muted">
                        Carregando...
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('system/js/logs.js') }}"></script>

