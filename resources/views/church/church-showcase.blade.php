<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-info">
                            <i class="fas fa-store me-2"></i>Vitrine de Igrejas
                        </h1>
                        <p class="mb-0 text-muted">Explore e conheça outras igrejas da nossa rede</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="d-flex align-items-center justify-content-end">
                            <small class="text-muted me-2">{{ $igrejas->total() }} igrejas encontradas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros e Busca -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Buscar Igrejas</label>
                        <input type="text"  autocomplete="new-password"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Nome, localização, descrição...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Status</label>
                        <select class="form-select" wire:model.live="statusFilter">
                            <option value="">Todos</option>
                            <option value="aprovado">Aprovadas</option>
                            <option value="pendente">Pendentes</option>
                            <option value="rejeitado">Rejeitadas</option>
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
                        <label class="form-label fw-semibold">Ordenar por</label>
                        <select class="form-select" wire:model.live="orderBy">
                            <option value="nome">Nome</option>
                            <option value="membros">Nº de Membros</option>
                            <option value="data">Data de Criação</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-primary w-100" wire:click="sortBy('{{ $orderBy }}')" title="Inverter ordenação">
                            <i class="fas fa-sort-{{ $orderDirection === 'asc' ? 'up' : 'down' }}"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid de Igrejas -->
        <div class="row g-4">
            @forelse($igrejas as $index => $igreja)
            <div class="col-xl-4 col-lg-6 col-md-6">
                <div class="card h-100 church-card border-0 shadow-sm" wire:click="verDetalhes({{ $igreja->id }})" style="cursor: pointer;">
                    <div class="card-header bg-gradient-primary text-white text-center py-3">
                        @if($igreja->logo)
                            <img src="{{ Storage::disk('supabase')->url($igreja->logo) }}"
                                 class="church-logo mb-2"
                                 alt="Logo {{ $igreja->nome }}"
                                 style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 3px solid white;">
                        @else
                            <div class="church-avatar {{ $this->getCorAvatar($index) }} text-white mb-2">
                                {{ $this->getIniciais($igreja->nome) }}
                            </div>
                        @endif
                        <h6 class="card-title mb-1 fw-bold">{{ Str::limit($igreja->nome, 25) }}</h6>
                        @if($igreja->sigla)
                        <small class="text-white-50">{{ $igreja->sigla }}</small>
                        @endif
                    </div>

                    <hr class="my-2 border-primary border opacity-50">

                    <div class="card-body">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="fw-bold text-info h5 mb-0">{{ $igreja->membros_count }}</div>
                                    <small class="text-muted">Membros</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center">
                                    <div class="fw-bold text-success h5 mb-0">{{ $igreja->aliancas->count() }}</div>
                                    <small class="text-muted">Alianças</small>
                                </div>
                            </div>
                        </div>

                        @if($igreja->categoria)
                        <div class="mb-2">
                            <span class="badge bg-info text-light">{{ $igreja->categoria->nome }}</span>
                        </div>
                        @endif

                        @if($igreja->localizacao)
                        <div class="mb-2">
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($igreja->localizacao, 30) }}
                            </small>
                        </div>
                        @endif

                        @if($igreja->sobre)
                        <p class="card-text small text-muted mb-3">
                            {{ Str::limit($igreja->sobre, 60) }}
                        </p>
                        @endif

                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>{{ $igreja->created_at->format('d/m/Y') }}
                            </small>
                            <span class="badge {{ $this->getStatusBadgeClass($igreja->status_aprovacao) }}">
                                {{ $this->getStatusText($igreja->status_aprovacao) }}
                            </span>
                        </div>
                    </div>

                    <div class="card-footer bg-light text-center">
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#churchDetailsModal" wire:click="verDetalhes({{ $igreja->id }})">
                            <i class="fas fa-eye me-1"></i>Ver Detalhes
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <div class="text-muted">
                        <i class="fas fa-search fa-3x mb-3"></i>
                        <h5>Nenhuma igreja encontrada</h5>
                        <p>Não foram encontradas igrejas com os critérios de busca aplicados.</p>
                        @if($search || $categoriaFilter || $tipoFilter)
                            <button class="btn btn-sm btn-outline-primary" wire:click="$set('search', '')">
                                <i class="fas fa-times me-1"></i>Limpar filtros
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Paginação -->
        @if($igrejas->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $igrejas->links() }}
        </div>
        @endif
    </div>

    <!-- Modal de Detalhes da Igreja -->
    <div class="modal fade" id="churchDetailsModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-building text-info me-2"></i>Detalhes da Igreja
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    @if($loadingDetalhes)
                    <div class="text-center py-5">
                        <div class="spinner-border text-info" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="mt-3 text-muted">Carregando detalhes da igreja...</p>
                    </div>
                    @elseif($igrejaSelecionada)
                    <div class="row g-4">
                        <!-- Informações Básicas -->
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-3">
                                @if($igrejaSelecionada->logo)
                                    <img src="{{ Storage::disk('supabase')->url($igrejaSelecionada->logo) }}"
                                         class="me-3 rounded-circle border border-primary"
                                         alt="Logo {{ $igrejaSelecionada->nome }}"
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                    <div class="church-avatar bg-info text-light text-white me-3">
                                        {{ $this->getIniciais($igrejaSelecionada->nome) }}
                                    </div>
                                @endif
                                <div>
                                    <h4 class="mb-1">{{ $igrejaSelecionada->nome }}</h4>
                                    @if($igrejaSelecionada->sigla)
                                    <p class="text-muted mb-0">{{ $igrejaSelecionada->sigla }}</p>
                                    @endif
                                </div>
                            </div>

                            @if($igrejaSelecionada->sobre)
                            <div class="mb-3">
                                <h6 class="fw-bold text-info">Sobre</h6>
                                <p class="mb-0">{{ $igrejaSelecionada->sobre }}</p>
                            </div>
                            @endif

                            @if($igrejaSelecionada->descricao)
                            <div class="mb-3">
                                <h6 class="fw-bold text-info">Descrição</h6>
                                <p class="mb-0">{{ $igrejaSelecionada->descricao }}</p>
                            </div>
                            @endif
                        </div>

                        <!-- Estatísticas -->
                        <div class="col-md-4">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="border-bottom pb-2">
                                                <div class="fw-bold h4 text-info mb-0">{{ $igrejaSelecionada->membros_count }}</div>
                                                <small class="text-muted">Membros Ativos</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="fw-bold h5 text-success mb-0">{{ $igrejaSelecionada->eventos_count }}</div>
                                            <small class="text-muted">Eventos</small>
                                        </div>
                                        <div class="col-6">
                                            <div class="fw-bold h5 text-info mb-0">{{ $igrejaSelecionada->aliancas->count() }}</div>
                                            <small class="text-muted">Alianças</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informações Adicionais -->
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title fw-bold text-info">
                                                <i class="fas fa-info-circle me-2"></i>Informações Gerais
                                            </h6>
                                            <div class="row g-2">
                                                <div class="col-12">
                                                    <strong>Tipo:</strong>
                                                    <span class="badge bg-secondary">{{ $this->getTipoText($igrejaSelecionada->tipo) }}</span>
                                                </div>
                                                @if($igrejaSelecionada->categoria)
                                                <div class="col-12">
                                                    <strong>Categoria:</strong>
                                                    <span class="badge bg-info text-light">{{ $igrejaSelecionada->categoria->nome }}</span>
                                                </div>
                                                @endif
                                                <div class="col-12">
                                                    <strong>Status:</strong>
                                                    <span class="badge {{ $this->getStatusBadgeClass($igrejaSelecionada->status_aprovacao) }}">
                                                        {{ $this->getStatusText($igrejaSelecionada->status_aprovacao) }}
                                                    </span>
                                                </div>
                                                <div class="col-12">
                                                    <strong>Criada em:</strong>
                                                    {{ $igrejaSelecionada->created_at->format('d/m/Y') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title fw-bold text-info">
                                                <i class="fas fa-map-marker-alt me-2"></i>Localização & Contato
                                            </h6>
                                            <div class="row g-2">
                                                @if($igrejaSelecionada->localizacao)
                                                <div class="col-12">
                                                    <strong>Localização:</strong><br>
                                                    <small>{{ $igrejaSelecionada->localizacao }}</small>
                                                </div>
                                                @endif
                                                @if($igrejaSelecionada->contacto)
                                                <div class="col-12">
                                                    <strong>Contato:</strong><br>
                                                    <small>{{ $igrejaSelecionada->contacto }}</small>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Administradores/Pastores/Ministros -->
                        @if($igrejaSelecionada->lideranca->count() > 0)
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold text-info">
                                        <i class="fas fa-users-cog me-2"></i>Liderança ({{ $igrejaSelecionada->lideranca->count() }})
                                    </h6>
                                    <div class="row g-2">
                                        @foreach($igrejaSelecionada->lideranca as $lider)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="border rounded p-2">
                                                <div class="fw-semibold">{{ $lider->user->name }}</div>
                                                <small class="text-muted">{{ ucfirst($lider->cargo) }}</small>
                                                @if($lider->user->email)
                                                <br><small class="text-muted">{{ $lider->user->email }}</small>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Alianças -->
                        @if($igrejaSelecionada->aliancas->count() > 0)
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold text-info">
                                        <i class="fas fa-handshake me-2"></i>Alianças ({{ $igrejaSelecionada->aliancas->count() }})
                                    </h6>
                                    <div class="row g-2">
                                        @foreach($igrejaSelecionada->aliancas as $alianca)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="border rounded p-2">
                                                <div class="fw-semibold">{{ $alianca->nome }}</div>
                                                @if($alianca->sigla)
                                                <small class="text-muted">{{ $alianca->sigla }}</small>
                                                @endif
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Estilos CSS --}}
    <style>
    .church-card {
        transition: all 0.3s ease;
        border-radius: 15px;
        overflow: hidden;
    }

    .church-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }

    .church-avatar, .church-logo {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 18px;
        margin: 0 auto;
    }

    .badge {
        font-size: 0.75rem;
    }

    /* Responsividade para mobile */
    @media (max-width: 768px) {
        .church-card {
            margin-bottom: 1rem;
        }

        .modal-dialog {
            margin: 0.5rem;
        }
    }
    </style>

    {{-- Scripts --}}
    @push('scripts')
    <script>
    document.addEventListener('livewire:initialized', () => {
        const churchDetailsModal = document.getElementById('churchDetailsModal');

        // Listener para quando o modal for fechado (ESC ou clique fora)
        churchDetailsModal.addEventListener('hidden.bs.modal', function () {
            @this.fecharDetalhes();
        });

        // Listener para abrir modal via Livewire
        Livewire.on('open-church-details-modal', () => {
            const modal = new bootstrap.Modal(churchDetailsModal);
            modal.show();
        });
    });
    </script>
    @endpush
</div>
