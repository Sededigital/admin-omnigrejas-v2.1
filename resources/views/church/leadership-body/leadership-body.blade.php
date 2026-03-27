<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-info">
                            <i class="fas fa-users-cog me-2"></i>Corpo de Liderança
                        </h1>
                        <p class="mb-0 text-muted">Gerencie e visualize todos os líderes das igrejas</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="d-flex justify-content-end gap-2">
                            <button class="btn btn-outline-primary btn-sm" onclick="window.location.reload()">
                                <i class="fas fa-sync-alt me-1"></i>Atualizar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-2">
                <div class="card text-center card-hover border border-primary metric-card">
                    <div class="card-body">
                        <i class="fas fa-users text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $estatisticas['total_lideres'] }}</div>
                        <div class="text-muted small">Total de Líderes</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <div class="card text-center card-hover border border-danger metric-card">
                    <div class="card-body">
                        <i class="fas fa-crown text-danger display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-danger">{{ $estatisticas['admins'] }}</div>
                        <div class="text-muted small">Administradores</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <div class="card text-center card-hover border border-primary metric-card">
                    <div class="card-body">
                        <i class="fas fa-church text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $estatisticas['pastores'] }}</div>
                        <div class="text-muted small">Pastores</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <div class="card text-center card-hover border border-info metric-card">
                    <div class="card-body">
                        <i class="fas fa-praying-hands text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $estatisticas['ministros'] }}</div>
                        <div class="text-muted small">Ministros</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <div class="card text-center card-hover border border-secondary metric-card">
                    <div class="card-body">
                        <i class="fas fa-hands-helping text-secondary display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-secondary">{{ $estatisticas['obreiros'] }}</div>
                        <div class="text-muted small">Obreiros</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <div class="card text-center card-hover border border-success metric-card">
                    <div class="card-body">
                        <i class="fas fa-hand-holding-heart text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ $estatisticas['diaconos'] }}</div>
                        <div class="text-muted small">Diáconos</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Buscar Líder</label>
                        <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Nome ou email...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Cargo</label>
                        <select class="form-select" wire:model.live="cargoFilter">
                            <option value="">Todos</option>
                            <option value="admin">Administrador</option>
                            <option value="pastor">Pastor</option>
                            <option value="ministro">Ministro</option>
                            <option value="obreiro">Obreiro</option>
                            <option value="diacono">Diácono</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Igreja</label>
                        <select class="form-select" wire:model.live="igrejaFilter">
                            <option value="">Todas as Igrejas</option>
                            @foreach($igrejas as $igreja)
                            <option value="{{ $igreja->id }}">{{ $igreja->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Status</label>
                        <select class="form-select" wire:model.live="statusFilter">
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary w-100" wire:click="limparFiltros">
                            <i class="fas fa-times me-1"></i>Limpar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards dos Líderes -->
        <div class="row g-4">
            @forelse($lideres as $lider)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card card-hover h-100 leadership-card" style="cursor: pointer;" wire:click="openLeaderModal({{ $lider->user->id }})" data-bs-toggle="modal" data-bs-target="#leaderModal">
                    <div class="card-body">
                        <!-- Header do Card -->
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                @if($lider->user->photo_url)
                                    <img src="{{ Storage::disk('supabase')->url($lider->user->photo_url) }}"
                                         class="me-3 rounded-circle border border-primary"
                                         alt="Foto {{ $lider->user->name }}"
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="leader-avatar {{ $this->getCargoBadgeClass($lider->cargo) }} text-white me-3">
                                        {{ $this->getIniciais($lider->user->name) }}
                                    </div>
                                @endif
                                <div>
                                    <h6 class="card-title mb-1">{{ Str::limit($lider->user->name, 20) }}</h6>
                                    <span class="badge {{ $this->getCargoBadgeClass($lider->cargo) }}">
                                        <i class="{{ $this->getCargoIcon($lider->cargo) }} me-1"></i>
                                        {{ $this->getCargoNome($lider->cargo) }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="text-success fw-bold">
                                    <i class="fas fa-circle text-success me-1" style="font-size: 8px;"></i>
                                    Ativo
                                </div>
                            </div>
                        </div>

                        <!-- Informações das Igrejas -->
                        <div class="mb-2">
                            <div class="d-flex flex-column gap-1">
                                @foreach($lider->igrejas as $igreja)
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-building text-muted me-1" style="font-size: 0.75rem;"></i>
                                        <small class="text-muted">{{ Str::limit(strtoupper($igreja->nome), 25) }}</small>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Informações de Contato -->
                        <div class="mb-2">
                            <i class="fas fa-envelope text-muted me-1"></i>
                            <small class="text-muted">{{ Str::limit($lider->user->email, 25) }}</small>
                        </div>

                        @if($lider->user->phone)
                        <div class="mb-2">
                            <i class="fas fa-phone text-muted me-1"></i>
                            <small class="text-muted">{{ $lider->user->phone }}</small>
                        </div>
                        @endif

                        <!-- Data de Entrada -->
                        <div class="mb-3">
                            <i class="fas fa-calendar-alt text-muted me-1"></i>
                            <small class="text-muted">
                                Líder desde {{ $lider->data_entrada->format('M/Y') }}
                            </small>
                        </div>

                        <!-- Ações -->
                        <div class="d-flex gap-2 justify-content-end">
                            <button class="btn btn-outline-primary btn-sm" onclick="event.stopPropagation(); viewLeaderProfile('{{ $lider->user->id }}')">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-info btn-sm" onclick="event.stopPropagation(); contactLeader('{{ $lider->user->email }}')">
                                <i class="fas fa-envelope"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-users-slash text-muted mb-3" style="font-size: 3rem;"></i>
                        <h5 class="text-muted">Nenhum líder encontrado</h5>
                        <p class="text-muted">Tente ajustar os filtros de busca</p>
                        @if($search || $cargoFilter || $igrejaFilter)
                            <button class="btn btn-outline-primary" wire:click="limparFiltros">
                                <i class="fas fa-times me-1"></i>Limpar filtros
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Paginação -->
        @if($lideres->hasPages())
        <div class="mt-4">
            {{ $lideres->links() }}
            <div class="text-center text-muted mt-2">
                <small>Mostrando {{ $lideres->firstItem() }}-{{ $lideres->lastItem() }} de {{ $lideres->total() }} líderes</small>
            </div>
        </div>
        @endif
    </div>

    {{-- Modal de Detalhes do Líder --}}
    <div class="modal fade" id="leaderModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-cog me-2"></i>Detalhes do Líder
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    @if($leaderDetails)
                    <!-- Informações Pessoais -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-info mb-3">
                            <i class="fas fa-user me-2"></i>Informações Pessoais
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center">
                                    @if($leaderDetails['user']->photo_url)
                                        <img src="{{ Storage::disk('supabase')->url($leaderDetails['user']->photo_url) }}"
                                             class="me-3 rounded-circle border border-primary"
                                             alt="Foto {{ $leaderDetails['user']->name }}"
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="leader-avatar bg-info text-light text-white me-3">
                                            {{ $this->getIniciais($leaderDetails['user']->name) }}
                                        </div>
                                    @endif
                                    <div>
                                        <h5 class="mb-1">{{ $leaderDetails['user']->name }}</h5>
                                        <p class="text-muted mb-0">{{ $leaderDetails['user']->email }}</p>
                                        @if($leaderDetails['user']->phone)
                                            <small class="text-muted">{{ $leaderDetails['user']->phone }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end">
                                @if($leaderDetails['estatisticas']['tempo_lideranca'])
                                <div class="text-success fw-bold">
                                    <i class="fas fa-circle text-success me-1" style="font-size: 8px;"></i>
                                    Líder Ativo
                                </div>
                                <small class="text-muted">
                                    Desde {{ $leaderDetails['estatisticas']['tempo_lideranca']->format('M/Y') }}
                                </small>
                                @else
                                <div class="text-muted fw-bold">
                                    <i class="fas fa-circle text-muted me-1" style="font-size: 8px;"></i>
                                    Sem Liderança Ativa
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($leaderDetails['liderancas']->isNotEmpty())
                    <!-- Estatísticas -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-info mb-3">
                            <i class="fas fa-chart-bar me-2"></i>Estatísticas
                        </h6>
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <div class="card text-center border-primary">
                                    <div class="card-body p-2">
                                        <i class="fas fa-building text-info mb-1"></i>
                                        <div class="fw-bold">{{ $leaderDetails['estatisticas']['total_igrejas'] }}</div>
                                        <small class="text-muted">Igrejas</small>
                                    </div>
                                </div>
                            </div>
                            @foreach($leaderDetails['estatisticas']['cargos'] as $cargo => $count)
                            <div class="col-6 col-md-3">
                                <div class="card text-center border-{{ $this->getCargoBadgeClass($cargo) }}">
                                    <div class="card-body p-2">
                                        <i class="{{ $this->getCargoIcon($cargo) }} text-{{ $this->getCargoBadgeClass($cargo) }} mb-1"></i>
                                        <div class="fw-bold">{{ $count }}</div>
                                        <small class="text-muted">{{ $this->getCargoNome($cargo) }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Igrejas onde é Líder -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-info mb-3">
                            <i class="fas fa-church me-2"></i>Igrejas onde Lidera
                        </h6>
                        <div class="row g-2">
                            @foreach($leaderDetails['liderancas']->unique('igreja_id') as $lideranca)
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-building text-muted me-3"></i>
                                            <div>
                                                <div class="fw-semibold">{{ strtoupper($lideranca->igreja->nome) }}</div>
                                                <small class="text-muted">{{ $lideranca->igreja->localizacao ?? 'Localização não informada' }}</small>
                                            </div>
                                        </div>
                                        <span class="badge {{ $this->getCargoBadgeClass($lideranca->cargo) }}">
                                            {{ $this->getCargoNome($lideranca->cargo) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Histórico de Liderança -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-info mb-3">
                            <i class="fas fa-history me-2"></i>Histórico de Liderança
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Igreja</th>
                                        <th>Cargo</th>
                                        <th>Data de Entrada</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leaderDetails['liderancas'] as $lideranca)
                                    <tr>
                                        <td>{{ strtoupper($lideranca->igreja->nome) }}</td>
                                        <td>
                                            <span class="badge {{ $this->getCargoBadgeClass($lideranca->cargo) }}">
                                                {{ $this->getCargoNome($lideranca->cargo) }}
                                            </span>
                                        </td>
                                        <td>{{ $lideranca->data_entrada->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge bg-success">Ativo</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @else
                    <!-- Mensagem quando não há lideranças ativas -->
                    <div class="text-center py-5">
                        <i class="fas fa-user-slash text-muted mb-3" style="font-size: 3rem;"></i>
                        <h5 class="text-muted">Nenhuma Liderança Ativa</h5>
                        <p class="text-muted">Este usuário não possui posições de liderança ativas no momento.</p>
                    </div>
                    @endif
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin text-info mb-3" style="font-size: 2rem;"></i>
                        <p class="text-muted">Carregando detalhes do líder...</p>
                    </div>
                    @endif
                </div>
                <div class="modal-footer bg-light border-0 rounded-bottom-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Fechar
                    </button>
                    @if($leaderDetails)
                    <a href="mailto:{{ $leaderDetails['user']->email }}" class="btn bg-info text-light">
                        <i class="fas fa-envelope me-2"></i>Enviar Email
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Estilos CSS --}}
    <style>
    .leadership-card {
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
    }

    .leadership-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        border-color: #007bff;
    }

    .leader-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 16px;
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

    .badge {
        font-size: 0.75rem;
    }
    </style>

    {{-- Scripts JavaScript --}}
    <script>
    function showLeaderDetails(leaderId) {
        // Implementar modal de detalhes
        console.log('Mostrar detalhes do líder:', leaderId);
    }

    function viewLeaderProfile(userId) {
        // Implementar visualização do perfil
        console.log('Ver perfil do usuário:', userId);
    }

    function contactLeader(email) {
        // Implementar contato por email
        window.location.href = 'mailto:' + email;
    }

    // Listener para abrir modal do líder
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('open-leader-modal', () => {
            const modal = new bootstrap.Modal(document.getElementById('leaderModal'));
            modal.show();
        });
    });
    </script>

</div>
