<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-primary">
                            <i class="fas fa-building me-2"></i>Gestão de Igrejas
                        </h1>
                        <p class="mb-0 text-muted">Gerencie todas as igrejas da rede</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="btn-group" role="group">
                            <a class="btn btn-outline-secondary btn-md" href="{{ route('admin.admin-church') }}"  wire:navigate >
                                <i class="fas fa-cog me-2"></i>Gestão ADMIN
                            </a>
                            <button class="btn btn-primary btn-md" data-bs-toggle="modal" data-bs-target="#churchModal">
                                <i class="fas fa-plus-circle me-2"></i>Nova Igreja
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Métricas -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-primary metric-card">
                    <div class="card-body">
                        <i class="fas fa-building text-primary display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-primary">{{ $totalIgrejas }}</div>
                        <div class="text-muted small">Total de Igrejas</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-success metric-card">
                    <div class="card-body">
                        <i class="fas fa-plus-circle text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ $igrejasNovas }}</div>
                        <div class="text-muted small">Novas (Este Mês)</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-success metric-card">
                    <div class="card-body">
                        <i class="fas fa-check-circle text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ $igrejasAtivas }}</div>
                        <div class="text-muted small">Igrejas Ativas</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border-2 border-dashed metric-card" role="button" data-bs-toggle="modal" data-bs-target="#churchModal">
                    <div class="card-body">
                        <i class="fas fa-plus-square text-primary display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h5 mb-1 text-primary">Adicionar</div>
                        <div class="text-muted small">Nova Igreja</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros e Busca -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Buscar Igreja</label>
                        <input type="text"  autocomplete="new-password" class="form-control" wire:model.live="search" placeholder="Nome da igreja, NIF, localização...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Status</label>
                        <select class="form-select" wire:model.live="statusFilter">
                            <option value="">Todos</option>
                            <option value="ativa">Ativa</option>
                            <option value="inativa">Inativa</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Ordenar por</label>
                        <select class="form-select" wire:model.live="orderBy">
                            <option value="nome">Nome</option>
                            <option value="data">Data de Criação</option>
                            <option value="membros">Nº de Membros</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-secondary w-100" wire:click="$refresh">
                            <i class="fas fa-sync-alt me-1"></i>Atualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop: Tabela -->
        <div class="d-none d-lg-block">
            <div class="card">
                <div class="card-header d-flex align-items-center mb-3">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-list-ul me-2"></i>Lista de Igrejas
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th wire:click="sortBy('nome')" style="cursor: pointer;">
                                    Igreja
                                    @if($orderBy === 'nome')
                                        <i class="fas fa-sort-{{ $orderDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th>Endereço</th>
                                <th>Pastor/Líder</th>
                                <th>Status</th>
                                <th wire:click="sortBy('membros')" style="cursor: pointer;">
                                    Membros
                                    @if($orderBy === 'membros')
                                        <i class="fas fa-sort-{{ $orderDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('data')" style="cursor: pointer;">
                                    Criação
                                    @if($orderBy === 'data')
                                        <i class="fas fa-sort-{{ $orderDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($igrejas as $index => $igreja)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($igreja->logo)
                                            <img src="{{ Storage::disk('supabase')->url($igreja->logo) }}"
                                                class="me-3 rounded-circle border border-primary"
                                                alt="Logo {{ $igreja->nome }}"
                                                style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="church-avatar {{ $this->getCorAvatar($index) }} text-white me-3">
                                                {{ $this->getIniciais($igreja->nome) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-semibold">{{ $igreja->nome }}</div>
                                            <small class="text-muted">{{ ucfirst($igreja->tipo ?? 'Independente') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $igreja->localizacao ?? 'Não informado' }}</div>
                                    <small class="text-muted">{{ $igreja->contacto ?? 'Sem contato' }}</small>
                                </td>
                                <td>
                                    @php
                                        $pastor = $this->getPastorPrincipal($igreja);
                                    @endphp
                                    @if($pastor)
                                        <div class="fw-semibold">{{ $pastor->name }}</div>
                                        <small class="text-muted">{{ ucfirst($igreja->membros->where('user_id', $pastor->id)->first()->cargo ?? 'Membro') }}</small>
                                    @else
                                        <div class="text-muted">Sem pastor definido</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $this->getStatusBadgeClass($igreja->status_aprovacao) }}">
                                        {{ $this->getStatusText($igreja->status_aprovacao) }}
                                    </span>
                                </td>
                                <td><span class="fw-semibold">{{ $igreja->membros_count }}</span></td>
                                <td>
                                    <div>{{ $igreja->created_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $igreja->created_at->diffForHumans() }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" onclick="editIgreja('{{ $igreja->id }}')" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="{{ route('admin.admin-church', $igreja->id) }}" class="btn btn-outline-warning" wire:navigate title="Gerenciar Admins">
                                            <i class="fas fa-user-shield"></i>
                                        </a>
                                        <button class="btn btn-outline-info" title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" wire:click="deleteIgreja({{ $igreja->id }})"
                                                wire:confirm="Tem certeza que deseja excluir esta igreja?" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-search mb-2" style="font-size: 2rem;"></i>
                                        <p>Nenhuma igreja encontrada</p>
                                        @if($search || $statusFilter)
                                            <button class="btn btn-sm btn-outline-primary" wire:click="$set('search', '')">
                                                Limpar filtros
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($igrejas->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">
                            Mostrando {{ $igrejas->firstItem() }}-{{ $igrejas->lastItem() }} de {{ $igrejas->total() }} registros
                        </span>
                        {{ $igrejas->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Mobile/Tablet: Cards -->
        <div class="d-lg-none">
            <div class="row g-3">
                @forelse($igrejas as $index => $igreja)
                <div class="col-12 col-md-6">
                    <div class="card card-hover h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    @if($igreja->logo)
                                        <img src="{{ Storage::disk('supabase')->url($igreja->logo) }}"
                                             class="me-3 rounded-circle border border-primary"
                                             alt="Logo {{ $igreja->nome }}"
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="church-avatar {{ $this->getCorAvatar($index) }} text-white me-3">
                                            {{ $this->getIniciais($igreja->nome) }}
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="card-title mb-1">{{ $igreja->nome }}</h6>
                                        <span class="badge {{ $this->getStatusBadgeClass($igreja->status_aprovacao) }}">
                                            {{ $this->getStatusText($igreja->status_aprovacao) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-primary h5">{{ $igreja->membros_count }}</div>
                                    <small class="text-muted">membros</small>
                                </div>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                <small class="text-muted">{{ $igreja->localizacao ?? 'Localização não informada' }}</small>
                            </div>
                            <div class="mb-3">
                                <i class="fas fa-user text-muted me-1"></i>
                                @php
                                    $pastor = $this->getPastorPrincipal($igreja);
                                @endphp
                                <small class="text-muted">
                                    {{ $pastor ? $pastor->name : 'Sem pastor definido' }}
                                </small>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary btn-sm flex-fill" onclick="editIgreja('{{ $igreja->id }}')">
                                    <i class="fas fa-edit me-1"></i>Editar
                                </button>
                                <a href="{{ route('admin.admin-church', $igreja->id) }}" class="btn btn-outline-warning btn-sm" wire:navigate>
                                    <i class="fas fa-user-shield me-1"></i>Admins
                                </a>
                                <button class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm" wire:click="deleteIgreja({{ $igreja->id }})"
                                        wire:confirm="Tem certeza que deseja excluir esta igreja?">
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
                            <i class="fas fa-search text-muted mb-3" style="font-size: 3rem;"></i>
                            <h5 class="text-muted">Nenhuma igreja encontrada</h5>
                            <p class="text-muted">Tente ajustar os filtros de busca</p>
                            @if($search || $statusFilter)
                                <button class="btn btn-outline-primary" wire:click="$set('search', '')">
                                    <i class="fas fa-times me-1"></i>Limpar filtros
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Paginação Mobile -->
            @if($igrejas->hasPages())
            <div class="mt-4">
                {{ $igrejas->links() }}
                <div class="text-center text-muted mt-2">
                    <small>Mostrando {{ $igrejas->firstItem() }}-{{ $igrejas->lastItem() }} de {{ $igrejas->total() }} registros</small>
                </div>
            </div>
            @endif
        </div>

        <!-- Seção de Estatísticas -->
        <div class="mt-5">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-history me-2"></i>Igrejas Recentes
                            </h5>
                            <div class="row g-2">
                                @forelse($igrejasRecentes->take(2) as $igreja)
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded text-center">
                                        <div class="fw-semibold">{{ Str::limit($igreja->nome, 20) }}</div>
                                        <small class="text-muted">{{ $igreja->created_at->format('M Y') }}</small>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12">
                                    <div class="text-center text-muted py-3">
                                        <i class="fas fa-inbox"></i>
                                        <p class="mb-0">Nenhuma igreja recente</p>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-users me-2"></i>Maiores Congregações
                            </h5>
                            <div class="row g-2">
                                @forelse($maioresCongregacoes->take(2) as $igreja)
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded text-center">
                                        <div class="fw-semibold">{{ Str::limit($igreja->nome, 20) }}</div>
                                        <small class="text-muted">{{ $igreja->membros_count }} membros</small>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12">
                                    <div class="text-center text-muted py-3">
                                        <i class="fas fa-users"></i>
                                        <p class="mb-0">Nenhuma congregação encontrada</p>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Igreja (sempre carregado para melhor performance) --}}
    @include('church.only.modals.church-modal')

    <script src="{{ asset('system/js/igreja.js') }}"></script>


</div>
