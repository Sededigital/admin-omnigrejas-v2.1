<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-info">
                            <i class="fas fa-hands-helping me-2"></i>Pedidos Especiais
                        </h1>
                        <p class="mb-0 text-muted">Gerencie os pedidos especiais dos membros da igreja</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="d-flex gap-2 justify-content-end">
                            <button class="btn btn-outline-secondary btn-md" wire:click="$dispatch('show-modal', 'typesListModal')" data-bs-toggle="modal" data-bs-target="#typesListModal">
                                <i class="fas fa-tags me-2"></i>Tipos
                            </button>
                            <button class="btn bg-info text-light btn-md" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#requestModal">
                                <i class="fas fa-plus me-2"></i>Novo Pedido
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Abas -->
        <div class="card mb-4">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="requestTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $activeTab === 'requests' ? 'active' : '' }}"
                                wire:click="switchTab('requests')"
                                type="button" role="tab">
                            <i class="fas fa-list-ul me-2"></i>Lista de Pedidos Especiais
                            @if(isset($stats['total']) && $stats['total'] > 0)
                                <span class="badge bg-info text-light ms-2">{{ $stats['total'] }}</span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $activeTab === 'urgent' ? 'active' : '' }}"
                                wire:click="switchTab('urgent')"
                                type="button" role="tab">
                            <i class="fas fa-exclamation-triangle me-2 text-danger"></i>Pedidos Urgentes
                            @if(isset($stats['urgent']) && $stats['urgent'] > 0)
                                <span class="badge bg-danger ms-2">{{ $stats['urgent'] }}</span>
                            @endif
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <!-- Conteúdo das Abas -->
                @if($activeTab === 'requests')
                <!-- Conteúdo da aba Lista de Pedidos Especiais -->

        <!-- Cards de Métricas -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-primary metric-card">
                    <div class="card-body">
                        <i class="fas fa-hands-helping text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $stats['total'] ?? 0 }}</div>
                        <div class="text-muted small">Total de Pedidos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-warning metric-card">
                    <div class="card-body">
                        <i class="fas fa-clock text-warning display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-warning">{{ $stats['pending'] ?? 0 }}</div>
                        <div class="text-muted small">Pendentes</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-info metric-card">
                    <div class="card-body">
                        <i class="fas fa-cogs text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $stats['in_progress'] ?? 0 }}</div>
                        <div class="text-muted small">Em Andamento</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-success metric-card">
                    <div class="card-body">
                        <i class="fas fa-check-circle text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ $stats['completed'] ?? 0 }}</div>
                        <div class="text-muted small">Concluídos</div>
                    </div>
                </div>
            </div>
        </div>

                <!-- Filtros -->
                <div class="row g-3 align-items-end mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Buscar Pedido</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Descrição ou membro">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tipo de Pedido</label>
                        <select class="form-select" wire:model.live="selectedType">
                            <option value="">Todos os tipos</option>
                            @foreach($requestTypes ?? [] as $type)
                                <option value="{{ $type->id }}">{{ $type->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($activeTab === 'requests')
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select class="form-select" wire:model.live="selectedStatus">
                            <option value="">Todos os status</option>
                            <option value="pendente">Pendente</option>
                            <option value="em_andamento">Em Andamento</option>
                            <option value="aprovado">Aprovado</option>
                            <option value="rejeitado">Rejeitado</option>
                            <option value="concluido">Concluído</option>
                        </select>
                    </div>
                    @endif
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button class="btn bg-info text-light flex-fill" wire:click="clearFilters">
                                <i class="fas fa-filter me-1"></i>Limpar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Botões de Filtro Rápido -->
                <div class="row g-2 mb-3">
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-outline-primary btn-sm" wire:click="setStatusFilter('')">
                                <i class="fas fa-hands-helping me-1"></i>Todos
                            </button>
                            @if($activeTab === 'requests')
                            <button class="btn btn-outline-warning btn-sm" wire:click="setStatusFilter('pendente')">
                                <i class="fas fa-clock me-1"></i>Pendentes
                            </button>
                            <button class="btn btn-outline-info btn-sm" wire:click="setStatusFilter('em_andamento')">
                                <i class="fas fa-cogs me-1"></i>Em Andamento
                            </button>
                            <button class="btn btn-outline-success btn-sm" wire:click="setStatusFilter('concluido')">
                                <i class="fas fa-check-circle me-1"></i>Concluídos
                            </button>
                            <button class="btn btn-outline-danger btn-sm" wire:click="setStatusFilter('rejeitado')">
                                <i class="fas fa-times-circle me-1"></i>Rejeitados
                            </button>
                            @else
                            <button class="btn btn-outline-danger btn-sm" wire:click="setStatusFilter('pendente')">
                                <i class="fas fa-exclamation-triangle me-1"></i>Urgentes
                            </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Conteúdo baseado na aba ativa -->
                @if($activeTab === 'requests')
                <!-- Desktop: Tabela de Pedidos Especiais -->
                <div class="d-none d-lg-block">
                    <div class="card">
                        <div class="card-header d-flex align-items-center mb-3">
                            <h5 class="mb-0 text-info">
                                <i class="fas fa-list-ul me-2"></i>Lista de Pedidos Especiais
                            </h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Membro</th>
                                        <th>Tipo de Pedido</th>
                                        <th>Data Pedido</th>
                                        <th>Responsável</th>
                                        <th>Status</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($requests ?? [] as $request)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar bg-info text-light text-white me-3">
                                                    {{ strtoupper(substr($request->membro->user->name ?? 'M', 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $request->membro->user->name ?? 'Nome do Membro' }}</div>
                                                    <small class="text-muted">{{ $request->membro->user->email ?? 'email@exemplo.com' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                
                                                {{ Str::limit($request->pedidoTipo->nome ?? 'Tipo não definido', 30) }}
                                            </span>
                                        </td>
                                      
                                        <td>
                                            <div>{{ $request->data_pedido ? $request->data_pedido->format('d/m/Y') : 'Não definida' }}</div>
                                            <small class="text-muted">{{ $request->data_pedido ? $request->data_pedido->diffForHumans() : '' }}</small>
                                        </td>
                                        <td>
                                            @if($request->responsavel)
                                                <div class="d-flex align-items-center">
                                                    <div class="user-avatar bg-success text-white me-2" style="width: 24px; height: 24px; font-size: 10px;">
                                                        {{ strtoupper(substr($request->responsavel->name, 0, 2)) }}
                                                    </div>
                                                    <small>{{ $request->responsavel->name }}</small>
                                                </div>
                                            @else
                                                <span class="text-muted">Não atribuído</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $this->getStatusBadgeClass($request->status ?? 'pendente') }}">
                                                {{ $this->getStatusLabel($request->status ?? 'pendente') }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" wire:click="openModal('{{ $request->id ?? '' }}')" data-bs-toggle="modal" data-bs-target="#requestModal" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-info" wire:click="viewRequest('{{ $request->id ?? '' }}')" title="Visualizar">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <a href="{{ route('churches.orders.special-requests.pdf', $request->id) }}" target="_blank" class="btn btn-outline-primary" title="Imprimir PDF">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                @if(in_array($request->status, ['pendente', 'em_andamento']))
                                                    <button class="btn btn-outline-success" wire:click="approveRequest('{{ $request->id ?? '' }}')" title="Aprovar">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" wire:click="rejectRequest('{{ $request->id ?? '' }}')" title="Rejeitar">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-hands-helping text-muted display-4 mb-3"></i>
                                            <div class="text-muted">Nenhum pedido especial encontrado</div>
                                            <button class="btn bg-info text-light mt-3" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#requestModal">
                                                <i class="fas fa-plus me-1"></i>Criar Primeiro Pedido
                                            </button>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if(isset($requests) && $requests->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Mostrando {{ $requests->firstItem() }}-{{ $requests->lastItem() }} de {{ $requests->total() }} registros</span>
                                <nav aria-label="Paginação">
                                    {{ $requests->links() }}
                                </nav>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @else
                <!-- Desktop: Tabela de Pedidos Urgentes -->
                <div class="d-none d-lg-block">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white d-flex align-items-center mb-3">
                            <h5 class="mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>Pedidos Urgentes
                            </h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Membro</th>
                                        <th>Tipo de Pedido</th>
                                        <th>Descrição</th>
                                        <th>Data Pedido</th>
                                        <th>Dias em Aberto</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($urgentRequests ?? [] as $request)
                                    <tr class="table-warning">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar bg-danger text-white me-3">
                                                    {{ strtoupper(substr($request->membro->user->name ?? 'M', 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $request->membro->user->name ?? 'Nome do Membro' }}</div>
                                                    <small class="text-muted">{{ $request->membro->user->email ?? 'email@exemplo.com' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ $request->pedidoTipo->nome ?? 'Tipo não definido' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 200px;" title="{{ $request->descricao ?? 'Sem descrição' }}">
                                                {{ Str::limit($request->descricao ?? 'Sem descrição', 50) }}
                                            </div>
                                        </td>
                                        <td>
                                            <div>{{ $request->data_pedido ? $request->data_pedido->format('d/m/Y') : 'Não definida' }}</div>
                                            <small class="text-muted">{{ $request->data_pedido ? $request->data_pedido->diffForHumans() : '' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger fs-6">
                                                {{ $request->data_pedido ? $request->data_pedido->diffInDays(now()) : 0 }} dias
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" wire:click="openModal('{{ $request->id ?? '' }}')" data-bs-toggle="modal" data-bs-target="#requestModal" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-info" wire:click="viewRequest('{{ $request->id ?? '' }}')" title="Visualizar">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-primary" wire:click="generatePdf({{ $request->id }})" title="Imprimir PDF">
                                                    <i class="fas fa-print"></i>
                                                </button>
                                                <button class="btn btn-outline-success" wire:click="approveRequest('{{ $request->id ?? '' }}')" title="Aprovar">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn btn-outline-danger" wire:click="rejectRequest('{{ $request->id ?? '' }}')" title="Rejeitar">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-check-circle text-success display-4 mb-3"></i>
                                            <div class="text-muted">Nenhum pedido urgente encontrado</div>
                                            <small class="text-muted">Todos os pedidos estão sendo atendidos dentro do prazo!</small>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if(isset($urgentRequests) && $urgentRequests->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Mostrando {{ $urgentRequests->firstItem() }}-{{ $urgentRequests->lastItem() }} de {{ $urgentRequests->total() }} registros</span>
                                <nav aria-label="Paginação">
                                    {{ $urgentRequests->links() }}
                                </nav>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Mobile/Tablet: Cards -->
                @if($activeTab === 'requests')
                <div class="d-lg-none">
                    <div class="row g-3">
                        @forelse($requests ?? [] as $request)
                        <div class="col-12 col-md-6">
                            <div class="card card-hover h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar bg-info text-light text-white me-3">
                                                {{ strtoupper(substr($request->membro->user->name ?? 'M', 0, 2)) }}
                                            </div>
                                            <div>
                                                <h6 class="card-title mb-1">{{ $request->membro->user->name ?? 'Nome do Membro' }}</h6>
                                                <span class="badge bg-{{ $this->getStatusBadgeClass($request->status ?? 'pendente') }}">
                                                    {{ $this->getStatusLabel($request->status ?? 'pendente') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <i class="fas fa-tag text-muted me-1"></i>
                                        <small class="text-muted">{{ $request->pedidoTipo->nome ?? 'Tipo não definido' }}</small>
                                    </div>
                                    <div class="mb-2">
                                        <i class="fas fa-align-left text-muted me-1"></i>
                                        <small class="text-muted">{{ Str::limit($request->descricao ?? 'Sem descrição', 60) }}</small>
                                    </div>
                                    <div class="mb-2">
                                        <i class="fas fa-calendar text-muted me-1"></i>
                                        <small class="text-muted">{{ $request->data_pedido ? $request->data_pedido->format('d/m/Y') : 'Data não definida' }}</small>
                                    </div>
                                    @if($request->responsavel)
                                    <div class="mb-3">
                                        <i class="fas fa-user-tie text-muted me-1"></i>
                                        <small class="text-muted">{{ $request->responsavel->name }}</small>
                                    </div>
                                    @endif
                                    <div class="d-flex gap-2">
                                        <button class="btn bg-info text-light btn-sm flex-fill" wire:click="openModal('{{ $request->id ?? '' }}')" data-bs-toggle="modal" data-bs-target="#requestModal">
                                            <i class="fas fa-edit me-1"></i>Editar
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" wire:click="viewRequest('{{ $request->id ?? '' }}')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-primary btn-sm" wire:click="generatePdf({{ $request->id }})" title="Imprimir PDF">
                                            <i class="fas fa-print"></i>
                                        </button>
                                        @if(in_array($request->status, ['pendente', 'em_andamento']))
                                            <button class="btn btn-outline-success btn-sm" wire:click="approveRequest('{{ $request->id ?? '' }}')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm" wire:click="rejectRequest('{{ $request->id ?? '' }}')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <i class="fas fa-hands-helping text-muted display-4 mb-3"></i>
                                    <div class="text-muted mb-3">Nenhum pedido especial encontrado</div>
                                    <button class="btn bg-info text-light" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#requestModal">
                                        <i class="fas fa-plus me-1"></i>Criar Primeiro Pedido
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforelse
                    </div>

                    <!-- Paginação Mobile -->
                    @if(isset($requests) && $requests->hasPages())
                    <div class="mt-4">
                        <nav aria-label="Paginação Mobile">
                            {{ $requests->links() }}
                        </nav>
                        <div class="text-center text-muted mt-2">
                            <small>Mostrando {{ $requests->firstItem() }}-{{ $requests->lastItem() }} de {{ $requests->total() }} registros</small>
                        </div>
                    </div>
                    @endif
                </div>
                @else
                <div class="d-lg-none">
                    <div class="row g-3">
                        @forelse($urgentRequests ?? [] as $request)
                        <div class="col-12 col-md-6">
                            <div class="card card-hover h-100 border-danger">
                                <div class="card-header bg-danger text-white">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span><i class="fas fa-exclamation-triangle me-2"></i>URGENTE</span>
                                        <span class="badge bg-light text-danger">{{ $request->data_pedido ? $request->data_pedido->diffInDays(now()) : 0 }}d</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar bg-danger text-white me-3">
                                                {{ strtoupper(substr($request->membro->user->name ?? 'M', 0, 2)) }}
                                            </div>
                                            <div>
                                                <h6 class="card-title mb-1">{{ $request->membro->user->name ?? 'Nome do Membro' }}</h6>
                                                <span class="badge bg-warning">Pendente</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <i class="fas fa-tag text-muted me-1"></i>
                                        <small class="text-muted">{{ $request->pedidoTipo->nome ?? 'Tipo não definido' }}</small>
                                    </div>
                                    <div class="mb-2">
                                        <i class="fas fa-align-left text-muted me-1"></i>
                                        <small class="text-muted">{{ Str::limit($request->descricao ?? 'Sem descrição', 60) }}</small>
                                    </div>
                                    <div class="mb-2">
                                        <i class="fas fa-calendar text-muted me-1"></i>
                                        <small class="text-muted">{{ $request->data_pedido ? $request->data_pedido->format('d/m/Y') : 'Data não definida' }}</small>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn bg-info text-light btn-sm flex-fill" wire:click="openModal('{{ $request->id ?? '' }}')">
                                            <i class="fas fa-edit me-1"></i>Editar
                                        </button>
                                        <button class="btn btn-outline-info btn-sm" wire:click="viewRequest('{{ $request->id ?? '' }}')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-primary btn-sm" wire:click="generatePdf({{ $request->id }})" title="Imprimir PDF">
                                            <i class="fas fa-print"></i>
                                        </button>
                                        <button class="btn btn-outline-success btn-sm" wire:click="approveRequest('{{ $request->id ?? '' }}')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" wire:click="rejectRequest('{{ $request->id ?? '' }}')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <i class="fas fa-check-circle text-success display-4 mb-3"></i>
                                    <div class="text-muted mb-3">Nenhum pedido urgente encontrado</div>
                                    <small class="text-muted">Todos os pedidos estão sendo atendidos dentro do prazo!</small>
                                </div>
                            </div>
                        </div>
                        @endforelse
                    </div>

                    <!-- Paginação Mobile Urgentes -->
                    @if(isset($urgentRequests) && $urgentRequests->hasPages())
                    <div class="mt-4">
                        <nav aria-label="Paginação Mobile">
                            {{ $urgentRequests->links() }}
                        </nav>
                        <div class="text-center text-muted mt-2">
                            <small>Mostrando {{ $urgentRequests->firstItem() }}-{{ $urgentRequests->lastItem() }} de {{ $urgentRequests->total() }} registros</small>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                @endif
            </div>
        </div>
    </div>
    @include('church.orders.modals.request-modal')
    @include('church.orders.modals.type-modal')
    <style>
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
        }

        .metric-card {
            transition: all 0.3s ease;
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .icon-interactive {
            transition: transform 0.3s ease;
        }

        .metric-card:hover .icon-interactive {
            transform: scale(1.1);
        }

        .border-left-danger {
            border-left: 4px solid #dc3545 !important;
        }

        .text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</div>
