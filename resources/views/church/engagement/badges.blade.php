<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-lg-8 col-md-7">
                        <h1 class="h3 mb-1 text-success">
                            <i class="fas fa-trophy me-2"></i>Sistema de Badges
                        </h1>
                        <p class="mb-0 text-muted">
                            Gerencie as conquistas e badges dos membros da igreja
                        </p>
                    </div>
                    <div class="col-lg-4 col-md-5 mt-3 mt-md-0">
                        <button class="btn btn-success btn-sm w-100" data-bs-toggle="modal" data-bs-target="#badgeModal">
                            <i class="fas fa-plus me-1"></i>Conceder Badge
                        </button>
                    </div>
                </div>
            </div>
        </div>



        <!-- Cards de Estatísticas -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card text-center border border-success h-100">
                    <div class="card-body">
                        <i class="fas fa-medal text-success display-6 mb-2"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ $stats['total_badges'] }}</div>
                        <div class="text-muted small">Badges Totais</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border border-primary h-100">
                    <div class="card-body">
                        <i class="fas fa-calendar-day text-info display-6 mb-2"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $stats['badges_hoje'] }}</div>
                        <div class="text-muted small">Badges Hoje</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border border-info h-100">
                    <div class="card-body">
                        <i class="fas fa-calendar-week text-info display-6 mb-2"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $stats['badges_semana'] }}</div>
                        <div class="text-muted small">Badges na Semana</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border border-warning h-100">
                    <div class="card-body">
                        <i class="fas fa-tags text-warning display-6 mb-2"></i>
                        <div class="fw-bold h4 mb-1 text-warning">{{ $stats['tipos_badges'] }}</div>
                        <div class="text-muted small">Tipos de Badges</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros para Badges -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tipo de Badge</label>
                        <select class="form-select" wire:model.live="filtroTipo">
                            <option value="">Todos os tipos</option>
                            @foreach($tiposBadges as $tipo)
                                <option value="{{ $tipo }}">{{ ucfirst($tipo) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Período</label>
                        <select class="form-select" wire:model.live="filtroPeriodo">
                            <option value="todos">Todos os períodos</option>
                            <option value="hoje">Hoje</option>
                            <option value="semana">Esta semana</option>
                            <option value="mes">Este mês</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Membro</label>
                        <select class="form-select" wire:model.live="filtroUsuario">
                            <option value="">Todos os membros</option>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Buscar</label>
                        <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Nome, badge ou descrição...">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                <strong>{{ $groupedBadges->total() }}</strong> membros encontrados
                            </div>
                            <button class="btn btn-outline-secondary btn-sm" wire:click="limparFiltros">
                                <i class="fas fa-times me-1"></i>Limpar Filtros
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Badges -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-trophy text-success me-2"></i>Badges Conquistadas
                </h5>
            </div>
            <div class="card-body">
                @if($groupedBadges->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Membro</th>
                                    <th>Total de Badges</th>
                                    <th>Badges Conquistados</th>
                                    <th>Última Conquista</th>
                                    <th width="120">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupedBadges as $userBadges)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    @if($userBadges->usuario->profile_photo_path)
                                                        <img src="{{ asset('storage/' . $userBadges->usuario->profile_photo_path) }}" alt="Avatar" class="rounded-circle">
                                                    @else
                                                        <div class="bg-info text-light text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                            <span class="fw-bold">{{ substr($userBadges->usuario->name, 0, 1) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $userBadges->usuario->name }}</div>
                                                    <small class="text-muted">{{ $userBadges->usuario->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">{{ $userBadges->total_badges }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @php
                                                    $badgesArray = explode('|', $userBadges->badges_lista);
                                                @endphp
                                                @foreach(array_slice($badgesArray, 0, 3) as $badgeNome)
                                                    @if(!empty($badgeNome))
                                                        <span class="badge bg-info text-light">{{ ucfirst($badgeNome) }}</span>
                                                    @endif
                                                @endforeach
                                                @if(count($badgesArray) > 3)
                                                    <span class="badge bg-secondary">+{{ count($badgesArray) - 3 }} mais</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($userBadges->ultima_conquista)->format('d/m/Y') }}
                                            </small>
                                            <div class="text-muted small">
                                                {{ \Carbon\Carbon::parse($userBadges->ultima_conquista)->diffForHumans() }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewModal" wire:click="verDetalhes('{{ $userBadges->user_id }}')" title="Ver Detalhes">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#badgeModal" title="Conceder Badge">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $groupedBadges->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-trophy text-muted mb-4" style="font-size: 4rem;"></i>
                        <h4 class="text-muted">Nenhuma badge encontrada</h4>
                        <p class="text-muted mb-4">
                            @if($filtroTipo || $filtroPeriodo !== 'todos' || $filtroUsuario || $search)
                                Nenhuma badge encontrada com os filtros aplicados.
                            @else
                                Ainda não há badges conquistadas na sua igreja.
                            @endif
                        </p>
                        @if($filtroTipo || $filtroPeriodo !== 'todos' || $filtroUsuario || $search)
                            <button class="btn btn-outline-secondary me-2" wire:click="limparFiltros">
                                <i class="fas fa-times me-1"></i>Limpar Filtros
                            </button>
                        @endif
                        <button class="btn btn-success" wire:click="abrirModal">
                            <i class="fas fa-plus me-1"></i>Conceder Primeira Badge
                        </button>
                    </div>
                @endif
            </div>
        </div>

        {{-- MODAL DE BADGES --}}
        <div class="modal fade" id="badgeModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-trophy me-2"></i>Conceder Badge
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form wire:submit="salvar">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Membro <span class="text-danger">*</span></label>
                                    <select class="form-select @error('badgeData.user_id') is-invalid @enderror"
                                            wire:model="badgeData.user_id">
                                        <option value="">Selecione um membro...</option>
                                        @foreach($usuarios as $usuario)
                                            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('badgeData.user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Badge <span class="text-danger">*</span></label>
                                    <select class="form-select @error('badgeData.badge') is-invalid @enderror"
                                            wire:model="badgeData.badge">
                                        <option value="">Selecione o badge...</option>
                                        <option value="Iniciante">🌱 Iniciante (50 pontos)</option>
                                        <option value="Ativo">⚡ Ativo (200 pontos)</option>
                                        <option value="Engajado">🔥 Engajado (500 pontos)</option>
                                        <option value="Líder">👑 Líder (1.000 pontos)</option>
                                        <option value="Mestre">🏆 Mestre (2.000 pontos)</option>
                                        <option value="Lenda">💎 Lenda (5.000 pontos)</option>
                                        <option value="manual">Badge Personalizado</option>
                                    </select>
                                    <small class="text-muted">
                                        Selecione um badge padrão ou "Badge Personalizado" para criar um novo
                                    </small>
                                    @error('badgeData.badge')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if(isset($badgeData['badge']) && $badgeData['badge'] === 'manual')
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Nome do Badge Personalizado</label>
                                    <input type="text"  autocomplete="new-password" class="form-control" wire:model="badgeData.badge_custom"
                                           placeholder="Ex: Melhor Participante, Líder do Mês, etc.">
                                    <small class="text-muted">
                                        Digite o nome personalizado do badge
                                    </small>
                                </div>
                                @endif

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Descrição</label>
                                    <textarea class="form-control" wire:model="badgeData.descricao" rows="3"
                                              placeholder="Descreva o motivo da conquista..."></textarea>
                                    <small class="text-muted">
                                        Explique por que este membro merece esta badge
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </button>
                            <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="fas fa-trophy me-1"></i>Conceder Badge
                                </span>
                                <span wire:loading>
                                    <i class="fas fa-spinner fa-spin me-1"></i>Salvando...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

           {{-- MODAL DE VISUALIZAÇÃO DE DETALHES --}}
        <div class="modal fade" id="viewModal" tabindex="-1" wire:ignore.self>
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-user text-info me-2"></i>Detalhes do Membro
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @if($userDetails['user'])
                            <div class="row g-3">
                                <!-- Informações do Usuário -->
                                <div class="col-12">
                                    <div class="card border-info">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    @if($userDetails['user']->photo_url)
                                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('supabase')->url($userDetails['user']->photo_url) }}" alt="Avatar" class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-info text-light text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                                            <span class="fw-bold">{{ substr($userDetails['user']->name, 0, 1) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h5 class="mb-1">{{ $userDetails['user']->name }}</h5>
                                                    <p class="text-muted mb-0">{{ $userDetails['user']->email }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Estatísticas -->
                                <div class="col-md-6">
                                    <div class="card text-center h-100">
                                        <div class="card-body">
                                            <i class="fas fa-star text-warning display-6 mb-2"></i>
                                            <div class="fw-bold h4 mb-1 text-warning">{{ $userDetails['total_pontos'] }}</div>
                                            <div class="text-muted small">Pontos Totais</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card text-center h-100">
                                        <div class="card-body">
                                            <i class="fas fa-trophy text-success display-6 mb-2"></i>
                                            <div class="fw-bold h4 mb-1 text-success">{{ $userDetails['badges']->count() }}</div>
                                            <div class="text-muted small">Badges Conquistados</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Última Atividade -->
                                @if($userDetails['ultima_atividade'])
                                <div class="col-12">
                                    <div class="alert alert-light">
                                        <i class="fas fa-clock me-2"></i>
                                        <strong>Última atividade:</strong> {{ \Carbon\Carbon::parse($userDetails['ultima_atividade'])->format('d/m/Y H:i') }}
                                        <small class="text-muted">({{ \Carbon\Carbon::parse($userDetails['ultima_atividade'])->diffForHumans() }})</small>
                                    </div>
                                </div>
                                @endif

                                <!-- Badges -->
                                @if($userDetails['badges']->count() > 0)
                                <div class="col-12">
                                    <h6 class="fw-bold mb-3">
                                        <i class="fas fa-medal text-success me-2"></i>Badges Conquistados
                                    </h6>
                                    <div class="row g-2">
                                        @foreach($userDetails['badges'] as $badge)
                                            <div class="col-md-6">
                                                <div class="card border-success">
                                                    <div class="card-body p-2">
                                                        <div class="d-flex align-items-center">
                                                            <div class="badge-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                                                <i class="fas fa-trophy" style="font-size: 0.8rem;"></i>
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <div class="fw-semibold small">{{ ucfirst($badge->badge) }}</div>
                                                                <small class="text-muted">{{ $badge->data->format('d/m/Y') }}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-user text-muted mb-3" style="font-size: 3rem;"></i>
                                <p class="text-muted">Nenhum dado encontrado para este usuário.</p>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
