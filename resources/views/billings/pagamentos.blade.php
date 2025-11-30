<div>
    {{-- Seção do cabeçalho --}}
    <div class="iq-navbar-header m-0" style="height: 215px;">
        <div class="container-fluid iq-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h1>Pagamentos de Assinaturas</h1>
                            <p>Gerencie os pagamentos das assinaturas das igrejas</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#pagamentoModal">
                                <i class="fas fa-plus me-2"></i>
                                Novo Pagamento
                            </button>
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
    <div class="row">
        {{-- Abas de navegação --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"  wire:ignore>
                    <ul class="nav nav-tabs" id="pagamentosTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $activeTab === 'pagamentos' ? 'active' : '' }}" id="pagamentos-tab" data-bs-toggle="tab"
                               href="#pagamentos" role="tab" aria-controls="pagamentos" aria-selected="{{ $activeTab === 'pagamentos' ? 'true' : 'false' }}">
                                <i class="fas fa-credit-card me-2"></i>Pagamentos
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="ciclos-tab" data-bs-toggle="tab"
                               href="#ciclos" role="tab" aria-controls="ciclos" aria-selected="false">
                                <i class="fas fa-calendar-alt me-2"></i>Ciclos de Cobrança
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="falhas-tab" data-bs-toggle="tab"
                               href="#falhas" role="tab" aria-controls="falhas" aria-selected="false">
                                <i class="fas fa-exclamation-triangle me-2"></i>Falhas de Pagamento
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Conteúdo das abas --}}
                <div class="card-body"  wire:ignore>
                    <div class="tab-content" id="pagamentosTabContent">
                        {{-- Aba Pagamentos --}}
                        <div class="tab-pane fade {{ $activeTab === 'pagamentos' ? 'show active' : '' }}" id="pagamentos" role="tabpanel" aria-labelledby="pagamentos-tab" tabindex="0" >
                            <div class="row mb-3">
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text"  autocomplete="new-password" class="form-control" placeholder="Buscar..." wire:model.live.debounce.300ms="search">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-select" wire:model.live="statusFilter">
                                        <option value="">Todos os status</option>
                                        @foreach($statusOptions as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-select" wire:model.live="metodoFilter">
                                        <option value="">Todos os métodos</option>
                                        @foreach($metodoOptions as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" wire:model.live="igrejaFilter">
                                        <option value="">Todas as igrejas</option>
                                        @foreach($igrejas as $igreja)
                                            <option value="{{ $igreja->id }}">{{ $igreja->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 text-end">
                                    <button type="button" class="btn btn-outline-secondary" wire:click="clearFilters">
                                        <i class="fas fa-eraser me-1"></i>
                                        Limpar Filtros
                                    </button>
                                </div>
                            </div>

                            {{-- Tabela de Pagamentos --}}
                            <div class="card"  wire:ignore.self>
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Pagamentos ({{ $pagamentos->total() }})</h4>
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
                                                    <th>Igreja</th>
                                                    <th>Pacote</th>
                                                    <th>Valor</th>
                                                    <th>Método</th>
                                                    <th>Status</th>
                                                    <th>Data</th>
                                                    <th>Referência</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($pagamentos as $pagamento)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-40 me-3 bg-soft-primary rounded">
                                                                <span class="avatar-title">{{ substr($pagamento->igreja->nome ?? 'I', 0, 1) }}</span>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0">{{ $pagamento->igreja->nome ?? 'Igreja' }}</h6>
                                                                <small class="text-muted">{{ $pagamento->igreja->nif ?? '-' }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-32 me-2 bg-soft-success rounded">
                                                                <span class="avatar-title">{{ substr($pagamento->assinatura->pacote->nome ?? 'P', 0, 1) }}</span>
                                                            </div>
                                                            <div>
                                                                <span class="fw-bold">{{ $pagamento->assinatura->pacote->nome ?? 'Pacote' }}</span><br>
                                                                <small class="text-muted">{{ $pagamento->assinatura->pacote->duracao_meses ?? 0 }} meses</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success">{{ number_format($pagamento->valor, 2, ',', '.') }} Kz</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $metodoOptions[$pagamento->metodo_pagamento] ?? $pagamento->metodo_pagamento }}</span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $statusClass = match($pagamento->status) {
                                                                'confirmado' => 'success',
                                                                'pendente' => 'warning',
                                                                'falhou' => 'danger',
                                                                'estornado' => 'secondary',
                                                                default => 'secondary'
                                                            };
                                                        @endphp
                                                        <span class="badge bg-{{ $statusClass }}">{{ $statusOptions[$pagamento->status] ?? $pagamento->status }}</span>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">{{ $pagamento->data_pagamento ? $pagamento->data_pagamento->format('d/m/Y H:i') : '-' }}</small>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">{{ $pagamento->referencia ?? '-' }}</small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click.prevent="openModal('{{ $pagamento->id }}')" data-bs-toggle="modal" data-bs-target="#pagamentoModal" title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deletePagamento({{ $pagamento->id }})" title="Excluir"
                                                                    onclick="return confirm('Tem certeza que deseja excluir este pagamento?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="8" class="text-center py-4">
                                                        <div class="text-muted">
                                                            <i class="fas fa-credit-card fa-2x mb-2"></i>
                                                            <p>Nenhum pagamento encontrado.</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @if($pagamentos->hasPages())
                                <div class="card-footer">
                                    {{ $pagamentos->links() }}
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Aba Ciclos --}}
                        <div class="tab-pane fade" id="ciclos" role="tabpanel" aria-labelledby="ciclos-tab" tabindex="0">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text"  autocomplete="new-password" class="form-control" placeholder="Buscar ciclos..." wire:model.live.debounce.300ms="searchCiclos">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" wire:model.live="statusCicloFilter">
                                        <option value="">Todos os status</option>
                                        <option value="pendente">Pendente</option>
                                        <option value="pago">Pago</option>
                                        <option value="atrasado">Atrasado</option>
                                        <option value="falhou">Falhou</option>
                                    </select>
                                </div>
                                <div class="col-md-5 text-end">
                                    <button type="button" class="btn btn-primary" wire:click="openModalCiclo" data-bs-toggle="modal" data-bs-target="#cicloModal">
                                        <i class="fas fa-plus me-2"></i>
                                        Novo Ciclo
                                    </button>
                                </div>
                            </div>

                            {{-- Tabela de Ciclos --}}
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Ciclos de Cobrança ({{ $ciclos->total() }})</h4>
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
                                                    <th>Igreja</th>
                                                    <th>Pacote</th>
                                                    <th>Período</th>
                                                    <th>Valor</th>
                                                    <th>Status</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($ciclos as $ciclo)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-40 me-3 bg-soft-primary rounded">
                                                                <span class="avatar-title">{{ substr($ciclo->assinaturaHistorico->igreja->nome ?? 'I', 0, 1) }}</span>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0">{{ $ciclo->assinaturaHistorico->igreja->nome ?? 'Igreja' }}</h6>
                                                                <small class="text-muted">{{ $ciclo->assinaturaHistorico->igreja->nif ?? '-' }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-32 me-2 bg-soft-success rounded">
                                                                <span class="avatar-title">{{ substr($ciclo->assinaturaHistorico->pacote->nome ?? 'P', 0, 1) }}</span>
                                                            </div>
                                                            <div>
                                                                <span class="fw-bold">{{ $ciclo->assinaturaHistorico->pacote->nome ?? 'Pacote' }}</span><br>
                                                                <small class="text-muted">{{ $ciclo->assinaturaHistorico->pacote->duracao_meses ?? 0 }} meses</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            {{ $ciclo->inicio ? $ciclo->inicio->format('d/m/Y') : '-' }}<br>
                                                            até {{ $ciclo->fim ? $ciclo->fim->format('d/m/Y') : '-' }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success">{{ number_format($ciclo->valor, 2, ',', '.') }} Kz</span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $statusClass = match($ciclo->status) {
                                                                'pago' => 'success',
                                                                'pendente' => 'warning',
                                                                'atrasado' => 'danger',
                                                                'falhou' => 'secondary',
                                                                default => 'secondary'
                                                            };
                                                        @endphp
                                                        <span class="badge bg-{{ $statusClass }}">{{ ucfirst($ciclo->status) }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openModalCiclo({{ $ciclo->id }})" data-bs-toggle="modal" data-bs-target="#cicloModal" title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteCiclo({{ $ciclo->id }})" title="Excluir"
                                                                    onclick="return confirm('Tem certeza que deseja excluir este ciclo?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">
                                                        <div class="text-muted">
                                                            <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                                                            <p>Nenhum ciclo encontrado.</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @if($ciclos->hasPages())
                                <div class="card-footer">
                                    {{ $ciclos->links() }}
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Aba Falhas --}}
                        <div class="tab-pane fade" id="falhas" role="tabpanel" aria-labelledby="falhas-tab" tabindex="0">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text"  autocomplete="new-password" class="form-control" placeholder="Buscar falhas..." wire:model.live.debounce.300ms="searchFalhas">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" wire:model.live="statusFalhaFilter">
                                        <option value="">Todos os status</option>
                                        <option value="resolvido">Resolvido</option>
                                        <option value="pendente">Pendente</option>
                                    </select>
                                </div>
                                <div class="col-md-5 text-end">
                                    <button type="button" class="btn btn-primary" wire:click="openModalFalha" data-bs-toggle="modal" data-bs-target="#falhaModal">
                                        <i class="fas fa-plus me-2"></i>
                                        Nova Falha
                                    </button>
                                </div>
                            </div>

                            {{-- Tabela de Falhas --}}
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="card-title">Falhas de Pagamento ({{ $falhas->total() }})</h4>
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
                                                    <th>Igreja</th>
                                                    <th>Pacote</th>
                                                    <th>Motivo</th>
                                                    <th>Data</th>
                                                    <th>Status</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($falhas as $falha)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-40 me-3 bg-soft-primary rounded">
                                                                <span class="avatar-title">{{ substr($falha->pagamento->igreja->nome ?? 'I', 0, 1) }}</span>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0">{{ $falha->pagamento->igreja->nome ?? 'Igreja' }}</h6>
                                                                <small class="text-muted">{{ $falha->pagamento->igreja->nif ?? '-' }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar-32 me-2 bg-soft-success rounded">
                                                                <span class="avatar-title">{{ substr($falha->pagamento->assinatura->pacote->nome ?? 'P', 0, 1) }}</span>
                                                            </div>
                                                            <div>
                                                                <span class="fw-bold">{{ $falha->pagamento->assinatura->pacote->nome ?? 'Pacote' }}</span><br>
                                                                <small class="text-muted">{{ number_format($falha->pagamento->valor, 2, ',', '.') }} Kz</small>
                                                            </div>

                                                        </div>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">{{ Str::limit($falha->motivo, 40) }}</small>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">{{ $falha->data ? $falha->data->format('d/m/Y H:i') : '-' }}</small>
                                                    </td>
                                                    <td>
                                                        @if($falha->resolvido)
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check me-1"></i>Resolvido
                                                            </span>
                                                        @else
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-exclamation-triangle me-1"></i>Pendente
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openModalFalha({{ $falha->id }})" data-bs-toggle="modal" data-bs-target="#falhaModal" title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteFalha({{ $falha->id }})" title="Excluir"
                                                                    onclick="return confirm('Tem certeza que deseja excluir esta falha?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-4">
                                                        <div class="text-muted">
                                                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                                            <p>Nenhuma falha encontrada.</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @if($falhas->hasPages())
                                <div class="card-footer">
                                    {{ $falhas->links() }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal de Ciclo --}}
            <div class="modal fade" id="cicloModal" tabindex="-1" aria-labelledby="cicloModalLabel" aria-hidden="true"
                 data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-light border-bottom">
                            <h5 class="modal-title fw-bold" id="cicloModalLabel">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                <span id="modal-title">{{ $editingCiclo ? 'Editar Ciclo' : 'Novo Ciclo' }}</span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body p-4">
                            <form wire:submit.prevent="saveCiclo">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <select class="form-select @error('ciclo_assinatura_id') is-invalid @enderror"
                                                    wire:model.live="ciclo_assinatura_id">
                                                <option value="">Selecione uma assinatura</option>
                                                @foreach($assinaturas as $assinatura)
                                                    <option value="{{ $assinatura->id }}">
                                                        {{ $assinatura->igreja->nome ?? 'Igreja' }} - {{ $assinatura->pacote->nome ?? 'Pacote' }} ({{ number_format($assinatura->valor, 2, ',', '.') }} Kz)
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label><i class="fas fa-file-signature text-primary me-1"></i>Assinatura *</label>
                                            @error('ciclo_assinatura_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="date" class="form-control @error('ciclo_inicio') is-invalid @enderror"
                                                   wire:model="ciclo_inicio" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                                            <label><i class="fas fa-calendar-plus text-primary me-1"></i>Data Início *</label>
                                            @error('ciclo_inicio')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="date" class="form-control @error('ciclo_fim') is-invalid @enderror"
                                                   wire:model="ciclo_fim" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                                            <label><i class="fas fa-calendar-minus text-primary me-1"></i>Data Fim *</label>
                                            @error('ciclo_fim')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="number" step="0.01" class="form-control @error('ciclo_valor') is-invalid @enderror"
                                                   wire:model="ciclo_valor" placeholder="0.00" min="0" readonly>
                                            <label><i class="fas fa-dollar-sign text-primary me-1"></i>Valor *</label>
                                            @error('ciclo_valor')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <select class="form-select @error('ciclo_status') is-invalid @enderror"
                                                    wire:model="ciclo_status">
                                                <option value="pendente">Pendente</option>
                                                <option value="pago">Pago</option>
                                                <option value="atrasado">Atrasado</option>
                                                <option value="falhou">Falhou</option>
                                            </select>
                                            <label><i class="fas fa-toggle-on text-primary me-1"></i>Status *</label>
                                            @error('ciclo_status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="alert alert-light border">
                                            <i class="fas fa-info-circle text-primary me-2"></i>
                                            <strong>Status:</strong>
                                            <span class="text-muted">
                                                {{ $editingCiclo ? 'Editando Ciclo' : 'Novo Ciclo' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer border-top bg-light">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" wire:click="saveCiclo" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="saveCiclo">
                                    <i class="fas fa-save me-1"></i>{{ $editingCiclo ? 'Atualizar Ciclo' : 'Salvar Ciclo' }}
                                </span>
                                <span wire:loading wire:target="saveCiclo">
                                    <i class="fas fa-spinner fa-spin me-1"></i>{{ $editingCiclo ? 'Atualizando...' : 'Salvando...' }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal de Falha --}}
            <div class="modal fade" id="falhaModal" tabindex="-1" aria-labelledby="falhaModalLabel" aria-hidden="true"
                 data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-light border-bottom">
                            <h5 class="modal-title fw-bold" id="falhaModalLabel">
                                <i class="fas fa-exclamation-triangle text-primary me-2"></i>
                                <span id="modal-title">{{ $editingFalha ? 'Editar Falha' : 'Nova Falha' }}</span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body p-4">
                            <form wire:submit.prevent="saveFalha">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <select class="form-select @error('falha_pagamento_id') is-invalid @enderror"
                                                    wire:model="falha_pagamento_id">
                                                <option value="">Selecione um pagamento</option>
                                                @foreach($pagamentos as $pagamento)
                                                    <option value="{{ $pagamento->id }}">
                                                        {{ $pagamento->igreja->nome ?? 'Igreja' }} - {{ number_format($pagamento->valor, 2, ',', '.') }} Kz
                                                    </option>
                                                @endforeach
                                            </select>
                                            <label><i class="fas fa-credit-card text-primary me-1"></i>Pagamento *</label>
                                            @error('falha_pagamento_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <textarea class="form-control @error('falha_motivo') is-invalid @enderror"
                                                      wire:model="falha_motivo" rows="3"
                                                      placeholder="Descreva o motivo da falha"></textarea>
                                            <label><i class="fas fa-comment text-primary me-1"></i>Motivo *</label>
                                            @error('falha_motivo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input @error('falha_resolvido') is-invalid @enderror"
                                                   type="checkbox" wire:model="falha_resolvido" id="falhaResolvidoSwitch">
                                            <label class="form-check-label" for="falhaResolvidoSwitch">
                                                <i class="fas fa-check-circle text-primary me-1"></i>Falha Resolvida
                                            </label>
                                            @error('falha_resolvido')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="alert alert-light border">
                                            <i class="fas fa-info-circle text-primary me-2"></i>
                                            <strong>Status:</strong>
                                            <span class="text-muted">
                                                {{ $editingFalha ? 'Editando Falha' : 'Nova Falha' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer border-top bg-light">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" wire:click="saveFalha" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="saveFalha">
                                    <i class="fas fa-save me-1"></i>{{ $editingFalha ? 'Atualizar Falha' : 'Salvar Falha' }}
                                </span>
                                <span wire:loading wire:target="saveFalha">
                                    <i class="fas fa-spinner fa-spin me-1"></i>{{ $editingFalha ? 'Atualizando...' : 'Salvando...' }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </div>


    {{-- Modal de Pagamento --}}
    <div class="modal fade" id="pagamentoModal" tabindex="-1" aria-labelledby="pagamentoModalLabel" aria-hidden="true"
          data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <!-- Header do Modal -->
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold" id="pagamentoModalLabel">
                        <i class="fas fa-credit-card text-primary me-2"></i>
                        <span id="modal-title">{{ $editingPagamento ? 'Editar Pagamento' : 'Novo Pagamento' }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <!-- Corpo do Modal -->
                <div class="modal-body p-4">
                    <form wire:submit.prevent="savePagamento" onsubmit="console.log('Form submitted with metodo_pagamento:', document.querySelector('[name=metodo_pagamento]').value)">

                        <!-- Seleção da Assinatura e Igreja -->
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <select class="form-select @error('assinatura_id') is-invalid @enderror"
                                            wire:model="assinatura_id" disabled>
                                        <option value="">Selecione uma assinatura</option>
                                        @foreach($assinaturas as $assinatura)
                                            <option value="{{ $assinatura->id }}">
                                                {{ $assinatura->pacote->nome ?? 'Pacote' }} - {{ $assinatura->igreja->nome ?? 'Igreja' }} ({{ number_format($assinatura->valor, 2, ',', '.') }} Kz)
                                            </option>
                                        @endforeach
                                    </select>
                                    <label><i class="fas fa-file-signature text-primary me-1"></i>Assinatura *</label>
                                    @error('assinatura_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <select class="form-select @error('igreja_id') is-invalid @enderror"
                                            wire:model="igreja_id" disabled>
                                        <option value="">Selecione uma igreja</option>
                                        @foreach($igrejas as $igreja)
                                            <option value="{{ $igreja->id }}">{{ $igreja->nome }} ({{ $igreja->nif }})</option>
                                        @endforeach
                                    </select>
                                    <label><i class="fas fa-church text-primary me-1"></i>Igreja *</label>
                                    @error('igreja_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Valor e Método -->
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="number" step="0.01" class="form-control @error('valor') is-invalid @enderror"
                                           wire:model="valor" placeholder="0.00" required disabled>
                                    <label><i class="fas fa-dollar-sign text-primary me-1"></i>Valor (Kz) *</label>
                                    @error('valor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select @error('metodo_pagamento') is-invalid @enderror"
                                            wire:model="metodo_pagamento"
                                            onchange="console.log('Método changed to:', this.value)">
                                        <option value="">Selecione um método</option>
                                        @foreach($metodoOptions as $key => $value)
                                            <option value="{{ $key }}" {{ $metodo_pagamento === $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <label><i class="fas fa-money-bill-wave text-primary me-1"></i>Método *</label>
                                    @error('metodo_pagamento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Referência e Status -->
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text"  autocomplete="new-password" class="form-control @error('referencia') is-invalid @enderror"
                                           wire:model="referencia" placeholder="Referência do pagamento" readonly>
                                    <label><i class="fas fa-hashtag text-primary me-1"></i>Referência</label>
                                    @error('referencia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select @error('status') is-invalid @enderror"
                                            wire:model="status">
                                        @foreach($statusOptions as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <label><i class="fas fa-toggle-on text-primary me-1"></i>Status *</label>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Data do Pagamento -->
                            <div class="col-12">
                                <div class="form-floating mb-3" wire:ignore>
                                    <input type="date" class="form-control date_flatpicker @error('data_pagamento') is-invalid @enderror"
                                           wire:model="data_pagamento">
                                    <label><i class="fas fa-calendar-alt text-primary me-1"></i>Data do Pagamento</label>
                                    @error('data_pagamento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status Visual -->
                            <div class="col-12">
                                <div class="alert alert-light border">
                                    <i class="fas fa-info-circle text-primary me-2"></i>
                                    <strong>Status:</strong>
                                    <span class="text-muted">
                                        {{ $editingPagamento ? 'Editando Pagamento' : 'Novo Pagamento' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer do Modal -->
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-outline-info" wire:click="gerarNovaReferencia" wire:loading.attr="disabled" {{ $editingPagamento ? 'disabled' : '' }} >
                        <span wire:loading.remove wire:target="gerarNovaReferencia">
                            <i class="fas fa-refresh me-1"></i>Nova Referência
                        </span>
                        <span wire:loading wire:target="gerarNovaReferencia">
                            <i class="fas fa-spinner fa-spin me-1"></i>Gerando...
                        </span>
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="savePagamento" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="savePagamento">
                            <i class="fas fa-save me-1"></i>{{ $editingPagamento ? 'Atualizar Pagamento' : 'Salvar Pagamento' }}
                        </span>
                        <span wire:loading wire:target="savePagamento">
                            <i class="fas fa-spinner fa-spin me-1"></i>{{ $editingPagamento ? 'Atualizando...' : 'Salvando...' }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

     {{-- Scripts para Pagamentos --}}
     <script src="{{ asset('system/js/assignatures.js') }}" data-navigate-once></script>

     {{-- Botão oculto para abrir modal --}}
     <button type="button" id="openPagamentoModalBtn" data-bs-toggle="modal" data-bs-target="#pagamentoModal" style="display: none;"></button>

</div>
