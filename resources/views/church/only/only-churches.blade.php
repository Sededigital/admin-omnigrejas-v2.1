<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-primary">
                            <i class="fas fa-building me-2"></i>Minhas Igrejas
                        </h1>
                        <p class="mb-0 text-muted">Gerencie as igrejas onde você é membro ou administrador</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        @can('manage-churches')
                         <button class="btn btn-primary btn-md" data-bs-toggle="modal" data-bs-target="#churchModal">
                            <i class="fas fa-plus-circle me-2"></i>Nova Igreja
                        </button>
                        @endcan
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
                        <i class="fas fa-check-circle text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ $igrejasAtivas }}</div>
                        <div class="text-muted small">Igrejas Ativas</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-info metric-card">
                    <div class="card-body">
                        <i class="fas fa-plus-circle text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $igrejasNovas }}</div>
                        <div class="text-muted small">Novas (Este Mês)</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-warning metric-card">
                    <div class="card-body">
                        <i class="fas fa-code-branch text-warning display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-warning">{{ $igrejas->where('tipo', 'filial')->count() }}</div>
                        <div class="text-muted small">Filiais</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros e Busca -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Buscar Igreja</label>
                        <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Nome, NIF, localização...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Status</label>
                        <select class="form-select" wire:model.live="statusFilter">
                            <option value="">Todos</option>
                            <option value="aprovado">Ativa</option>
                            <option value="pendente">Pendente</option>
                            <option value="rejeitado">Rejeitada</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Tipo</label>
                        <select class="form-select" wire:model.live="tipoFilter">
                            <option value="">Todos</option>
                            <option value="sede">Sede</option>
                            <option value="filial">Filial</option>
                            <option value="independente">Independente</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Categoria</label>
                        <select class="form-select" wire:model.live="categoriaFilter">
                            <option value="">Todas</option>
                            @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                            @endforeach
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
                    <div class="ms-auto">
                        <small class="text-muted">{{ $igrejas->total() }} igrejas encontradas</small>
                    </div>
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
                                <th>Tipo</th>
                                <th>Endereço</th>
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
                                @can('manage-churches')
                                <th class="text-center">Ações</th>
                                @endcan

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
                                            @if($igreja->sigla)
                                            <small class="text-muted">{{ $igreja->sigla }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ ucfirst($igreja->tipo ?? 'Independente') }}
                                    </span>
                                    @if($igreja->tipo === 'filial' && $igreja->sede)
                                    <br><small class="text-muted">Sede: {{ $igreja->sede->nome }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $igreja->localizacao ?? 'Não informado' }}</div>
                                    <small class="text-muted">{{ $igreja->contacto ?? 'Sem contato' }}</small>
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
                                    <div class="btn-group btn-group-sm" role="group">

                                        @can('view-churches')
                                         <a href="{{ url('/dashboard-church') }}" class="btn btn-outline-info" title="Ver Dashboard">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan


                                        @can('manage-churches')
                                        <button class="btn btn-outline-warning" wire:click="openAccessCodeModal('{{ $igreja->id }}')" data-bs-toggle="modal" data-bs-target="#accessCodeModalGen" title="Código de Acesso">
                                            <i class="fas fa-key"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" wire:click="openAdminModal({{ $igreja->id }})" data-bs-toggle="modal" data-bs-target="#adminModal" title="Gerenciar Admins">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <button class="btn btn-outline-primary" wire:click="editIgreja({{ $igreja->id }})" title="Editar Igreja" data-bs-toggle="modal" data-bs-target="#churchModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-search mb-2" style="font-size: 2rem;"></i>
                                        <p>Nenhuma igreja encontrada</p>
                                        @if($search || $statusFilter || $tipoFilter || $categoriaFilter)
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

                            <div class="mb-2">
                                <i class="fas fa-building text-muted me-1"></i>
                                <small class="text-muted">{{ ucfirst($igreja->tipo ?? 'Independente') }}</small>
                            </div>

                            <div class="d-flex gap-1 flex-wrap">
                                @if($this->permissionHelper->hasPermission('ver_igrejas'))
                                <a href="" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endif
                                @if($this->permissionHelper->hasPermission('gerenciar_igrejas'))
                                <button class="btn btn-outline-warning btn-sm" wire:click="openAccessCodeModal({{ $igreja->id }})" data-bs-toggle="modal" data-bs-target="#accessCodeModalGen" title="Código de Acesso">
                                    <i class="fas fa-key"></i>
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" wire:click="openAdminModal({{ $igreja->id }})" data-bs-toggle="modal" data-bs-target="#adminModal" title="Gerenciar Admins">
                                    <i class="fas fa-cog"></i>
                                </button>
                                <button class="btn btn-primary btn-sm" wire:click="editIgreja({{ $igreja->id }})" title="Editar" data-bs-toggle="modal" data-bs-target="#churchModal">
                                    <i class="fas fa-edit"></i>
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
                            <i class="fas fa-search text-muted mb-3" style="font-size: 3rem;"></i>
                            <h5 class="text-muted">Nenhuma igreja encontrada</h5>
                            <p class="text-muted">Tente ajustar os filtros de busca</p>
                            @if($search || $statusFilter || $tipoFilter || $categoriaFilter)
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
                                <i class="fas fa-clock me-2"></i>Igrejas Recentes
                            </h5>
                            <div class="row g-2">
                                @forelse($igrejasRecentes->take(3) as $igreja)
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-semibold">{{ Str::limit($igreja->nome, 25) }}</div>
                                                <small class="text-muted">{{ ucfirst($igreja->tipo ?? 'Independente') }}</small>
                                            </div>
                                            <small class="text-muted">{{ $igreja->created_at->format('M Y') }}</small>
                                        </div>
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
                                @forelse($maioresCongregacoes->take(3) as $igreja)
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-semibold">{{ Str::limit($igreja->nome, 25) }}</div>
                                                <small class="text-muted">{{ ucfirst($igreja->tipo ?? 'Independente') }}</small>
                                            </div>
                                            <small class="text-muted">{{ $igreja->membros_count }} membros</small>
                                        </div>
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
    @include('church.only.modals.only-church-modal')

    <script src="{{ asset('system/js/igreja.js') }}"></script>

    {{-- Modal de Confirmação de Remoção --}}
    <div class="modal fade" id="removeAdminModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h6 class="modal-title mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Remoção
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <i class="fas fa-user-minus text-danger fa-3x mb-3"></i>
                    <h6 class="fw-bold mb-2">Remover Administrador</h6>
                    <p class="text-muted mb-0">
                        Tem certeza que deseja remover <strong id="adminToRemoveName">{{ $adminToRemove ? $adminToRemove['name'] : '' }}</strong> como administrador desta igreja?
                    </p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" wire:click="removeAdminConfirmed">
                        <i class="fas fa-user-minus me-1"></i>Remover
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Código de Acesso --}}
    <div class="modal fade" id="accessCodeModalGen" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h6 class="modal-title mb-0">
                        <i class="fas fa-key me-2"></i>Código de Acesso
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Exibir senha atual se existir -->
                    <div class="mb-4" wire:loading.remove>
                        @if($currentAccessCode)
                        <div class="alert alert-info">
                            <h6 class="alert-heading mb-2">
                                <i class="fas fa-info-circle me-1"></i>Código Atual
                            </h6>
                            <div class="d-flex align-items-center">
                                <code class="fs-4 fw-bold text-primary me-3">{{ $currentAccessCode }}</code>
                                <button
                                    class="btn btn-sm btn-outline-secondary"
                                    onclick="copyAccessCode(this, '{{ $currentAccessCode }}')"
                                    title="Copiar código">
                                    <i class="fas fa-copy"></i>
                                </button>

                            </div>
                        </div>
                        @else
                        <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                <strong>Nenhum código definido</strong><br>
                                <small>Esta igreja não possui código de acesso configurado.</small>
                            </div>

                            {{-- Informações sobre regras de segurança --}}
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-shield-alt me-1"></i>
                                <strong>Regras de Segurança:</strong><br>
                                <small>
                                    • Novo código pode ser gerado a cada 10 dias<br>
                                    • Máximo de 5 gerações por dia<br>
                                    • Códigos são únicos e rastreáveis
                                </small>
                            </div>
                        @endif
                    </div>

                    <!-- Botão para gerar nova senha -->
                    <div class="text-center">
                        <button class="btn btn-warning btn-lg" wire:click="generateAccessCode" wire:loading.attr="disabled" wire:loading.class="disabled">
                            <span wire:loading.remove>
                                <i class="fas fa-magic me-2"></i>Gerar Novo Código
                            </span>
                            <span wire:loading>
                                <i class="fas fa-spinner fa-spin me-2"></i>Gerando...
                            </span>
                        </button>
                        <p class="text-muted mt-2 mb-0">
                            <small>O código terá 6 dígitos e será único no sistema</small>
                        </p>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 rounded-bottom-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Gerenciar Admins --}}
    <div class="modal fade" id="adminModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-cog me-2"></i>Gerenciar Administradores
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Admins Atuais -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="fas fa-users-cog me-2"></i>Administradores Atuais
                        </h6>
                        <div class="row g-2">
                            @forelse($adminUsers as $admin)
                            @php
                                $adminId = is_array($admin) ? $admin['id'] : $admin->id;
                                $canRemove = $this->canRemoveAdmin($adminId);
                                $isCurrentUser = $adminId === Auth::id();
                            @endphp
                            <div class="col-md-4 col-sm-6">
                                <div class="card border">
                                    <div class="card-body p-2 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            @php
                                                $userId = is_array($admin) ? $admin['id'] : $admin->id;
                                                $user = \App\Models\User::find($userId);
                                                $hasAvatar = $user && $user->photo_url;
                                            @endphp
                                            @if($hasAvatar)
                                                <img src="{{ Storage::disk('supabase')->url($user->photo_url) }}"
                                                     class="me-2 rounded-circle border"
                                                     alt="Avatar {{ $user->name }}"
                                                     style="width: 32px; height: 32px; object-fit: cover;">
                                            @else
                                                <div class="admin-avatar bg-secondary text-white me-2" style="width: 32px; height: 32px; font-size: 12px;">
                                                    @php
                                                        $name = is_array($admin) ? $admin['name'] : $admin->name;
                                                        echo substr($name, 0, 1);
                                                    @endphp
                                                </div>
                                            @endif
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold small mb-0">
                                                    @php
                                                        echo Str::limit(is_array($admin) ? $admin['name'] : $admin->name, 15);
                                                        if ($isCurrentUser) echo ' (Você)';
                                                    @endphp
                                                </div>
                                                <span class="badge bg-secondary badge-sm" style="font-size: 10px;">
                                                    @php
                                                        $cargo = is_array($admin) ? $admin['cargo'] : $admin->cargo;
                                                        echo ucfirst($cargo);
                                                    @endphp
                                                </span>
                                            </div>
                                        </div>
                                        @if($canRemove)
                                        <button class="btn btn-outline-danger btn-sm"
                                                wire:click="confirmRemoveAdmin('{{ $adminId }}')"
                                                data-bs-toggle="modal"
                                                data-bs-target="#removeAdminModal"
                                                title="Remover admin"
                                                style="padding: 0.2rem 0.4rem;">
                                            <i class="fas fa-user-minus" style="font-size: 10px;"></i>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-users-slash fa-3x mb-3"></i>
                                    <p class="mb-0">Nenhum administrador configurado</p>
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Adicionar Novos Admins -->
                    <div class="border-top pt-4">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="fas fa-user-plus me-2"></i>Adicionar Administrador
                        </h6>

                        <!-- Busca de Usuários -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Buscar Usuário</label>
                            <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="searchUser"
                                   placeholder="Digite nome ou email...">
                        </div>

                        <!-- Lista de Usuários Disponíveis -->
                        <div class="row g-2" style="max-height: 300px; overflow-y: auto;">
                            @foreach($this->getAvailableUsers() as $user)
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar bg-primary text-white me-3">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $user->name }}</div>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                        <button class="btn btn-success btn-sm" wire:click="addAdmin('{{ $user->id }}')">
                                            <i class="fas fa-plus me-1"></i>Adicionar
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @if($this->getAvailableUsers()->isEmpty() && $searchUser)
                            <div class="col-12">
                                <div class="text-center text-muted py-3">
                                    <i class="fas fa-search fa-2x mb-2"></i>
                                    <p class="mb-0">Nenhum usuário encontrado</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 rounded-bottom-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

        {{-- Estilos CSS --}}
        <style>
        .church-avatar, .admin-avatar, .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .metric-card:hover {
            transform: scale(1.05);
        }

        .icon-interactive {
            transition: transform 0.3s ease;
        }

        .metric-card:hover .icon-interactive {
            transform: scale(1.1);
        }

        .user-avatar {
            background-color: #6c757d !important;
        }
        </style>

        {{-- Script para copiar código --}}
        @push('scripts')
            <script>
            window.copyAccessCode = function (button, text) {
                if (!text) return;

                navigator.clipboard.writeText(text).then(() => {
                    // Trocar ícone temporariamente
                    const icon = button.querySelector('i');
                    const originalClass = icon.className;
                    icon.className = 'fas fa-check text-success';

                    // Mostrar toast bonito
                    const toast = document.createElement('div');
                    toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed';
                    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
                    toast.innerHTML = `
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas fa-check me-2"></i> Código copiado!
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    `;
                    document.body.appendChild(toast);

                    const bsToast = new bootstrap.Toast(toast);
                    bsToast.show();

                    // Restaurar o ícone original e remover toast
                    setTimeout(() => {
                        icon.className = originalClass;
                        document.body.removeChild(toast);
                    }, 2500);
                }).catch(err => {
                    console.error('Erro ao copiar:', err);
                });
            };

            // Re-anexar comportamento após Livewire atualizar DOM (modo SPA)
            document.addEventListener("livewire:navigated", () => {
                // Nenhuma reatribuição necessária porque copyAccessCode está no escopo global
            });
            </script>
        @endpush


</div>
