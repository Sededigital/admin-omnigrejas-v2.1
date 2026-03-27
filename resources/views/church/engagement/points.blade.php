<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-lg-8 col-md-7">
                        <h1 class="h3 mb-1 text-info">
                            <i class="fas fa-star me-2"></i>Sistema de Pontos
                        </h1>
                        <p class="mb-0 text-muted">
                            Gerencie a pontuação e engajamento dos membros da igreja
                        </p>
                    </div>
                    <div class="col-lg-4 col-md-5 mt-3 mt-md-0">
                        <button class="btn bg-info text-light btn-sm w-100" data-bs-toggle="modal" data-bs-target="#pointModal">
                            <i class="fas fa-plus me-1"></i>Registrar Pontos
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @push('styles')
        <link rel="stylesheet" href="{{ asset('system/css/community.css') }}">
        @endpush

        <!-- Cards de Estatísticas -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card text-center border border-primary h-100">
                    <div class="card-body">
                        <i class="fas fa-trophy text-info display-6 mb-2"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $stats['total_pontos'] }}</div>
                        <div class="text-muted small">Pontos Totais</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border border-success h-100">
                    <div class="card-body">
                        <i class="fas fa-plus-circle text-success display-6 mb-2"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ $stats['pontos_positivos'] }}</div>
                        <div class="text-muted small">Pontos Positivos</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border border-danger h-100">
                    <div class="card-body">
                        <i class="fas fa-minus-circle text-danger display-6 mb-2"></i>
                        <div class="fw-bold h4 mb-1 text-danger">{{ $stats['pontos_negativos'] }}</div>
                        <div class="text-muted small">Pontos Negativos</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border border-info h-100">
                    <div class="card-body">
                        <i class="fas fa-users text-info display-6 mb-2"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $stats['usuarios_ativos'] }}</div>
                        <div class="text-muted small">Membros Ativos</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros para Pontos -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tipo de Pontuação</label>
                        <select class="form-select" wire:model.live="filtroTipo">
                            <option value="">Todos os tipos</option>
                            <option value="positivo">Pontos Positivos</option>
                            <option value="negativo">Pontos Negativos</option>
                            <option value="neutro">Pontos Neutros</option>
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
                        <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Nome ou motivo...">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                <strong>{{ $groupedPoints->total() }}</strong> membros encontrados
                            </div>
                            <button class="btn btn-outline-secondary btn-sm" wire:click="limparFiltros">
                                <i class="fas fa-times me-1"></i>Limpar Filtros
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Pontos -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-star text-warning me-2"></i>Histórico de Pontuação
                </h5>
            </div>
            <div class="card-body">
                @if($groupedPoints->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Membro</th>
                                    <th>Pontuação Total</th>
                                    <th>Total de Registros</th>
                                    <th>Motivos</th>
                                    <th>Última Atividade</th>
                                    <th width="120">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupedPoints as $userPoints)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    @if($userPoints->usuario->profile_photo_path)
                                                        <img src="{{ asset('storage/' . $userPoints->usuario->profile_photo_path) }}" alt="Avatar" class="rounded-circle">
                                                    @else
                                                        <div class="bg-info text-light text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                            <span class="fw-bold">{{ substr($userPoints->usuario->name, 0, 1) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $userPoints->usuario->name }}</div>
                                                    <small class="text-muted">{{ $userPoints->usuario->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $userPoints->pontos_totais > 0 ? 'bg-success' : ($userPoints->pontos_totais < 0 ? 'bg-danger' : 'bg-secondary') }} fs-6">
                                                <i class="fas fa-{{ $userPoints->pontos_totais > 0 ? 'plus' : ($userPoints->pontos_totais < 0 ? 'minus' : 'circle') }}-circle me-1"></i>
                                                {{ $userPoints->pontos_totais > 0 ? '+' : '' }}{{ $userPoints->pontos_totais }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-light">{{ $userPoints->total_registros }}</span>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 200px;" title="{{ str_replace(',', ', ', $userPoints->motivos) }}">
                                                {{ Str::limit(str_replace(',', ', ', $userPoints->motivos), 30) }}
                                            </div>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($userPoints->ultima_atividade)->format('d/m/Y') }}
                                            </small>
                                            <div class="text-muted small">
                                                {{ \Carbon\Carbon::parse($userPoints->ultima_atividade)->diffForHumans() }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewModal" wire:click="verDetalhes('{{ $userPoints->user_id }}')" title="Ver Detalhes">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#pointModal" title="Adicionar Pontos">
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
                        {{ $groupedPoints->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-star text-muted mb-4" style="font-size: 4rem;"></i>
                        <h4 class="text-muted">Nenhum registro de pontos encontrado</h4>
                        <p class="text-muted mb-4">
                            @if($filtroTipo || $filtroPeriodo !== 'todos' || $filtroUsuario || $search)
                                Nenhum registro encontrado com os filtros aplicados.
                            @else
                                Ainda não há registros de pontuação na sua igreja.
                            @endif
                        </p>
                        @if($filtroTipo || $filtroPeriodo !== 'todos' || $filtroUsuario || $search)
                            <button class="btn btn-outline-secondary me-2" wire:click="limparFiltros">
                                <i class="fas fa-times me-1"></i>Limpar Filtros
                            </button>
                        @endif
                        <button class="btn bg-info text-light" wire:click="abrirModal">
                            <i class="fas fa-plus me-1"></i>Registrar Primeiro Ponto
                        </button>
                    </div>
                @endif
            </div>
        </div>

        {{-- MODAL DE PONTOS --}}
        <div class="modal fade" id="pointModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-star text-warning me-2"></i>Registrar Pontuação
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form wire:submit="salvar">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Membro <span class="text-danger">*</span></label>
                                    <select class="form-select @error('pointData.user_id') is-invalid @enderror"
                                            wire:model="pointData.user_id">
                                        <option value="">Selecione um membro...</option>
                                        @foreach($usuarios as $usuario)
                                            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('pointData.user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Pontos <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('pointData.pontos') is-invalid @enderror"
                                           wire:model="pointData.pontos" min="-1000" max="1000"
                                           placeholder="Ex: 10, -5, 0">
                                    <small class="text-muted">
                                        Use valores positivos para recompensas, negativos para penalidades, zero para neutro
                                    </small>
                                    @error('pointData.pontos')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Motivo <span class="text-danger">*</span></label>
                                    <select class="form-select @error('pointData.motivo') is-invalid @enderror"
                                            wire:model="pointData.motivo">
                                        <option value="">Selecione o motivo...</option>
                                        <option value="login_diario">Login Diário (5 pontos)</option>
                                        <option value="post_criado">Post Criado (15 pontos)</option>
                                        <option value="comentario_post">Comentário em Post (10 pontos)</option>
                                        <option value="reacao_post">Reação em Post (2 pontos)</option>
                                        <option value="evento_participado">Participação em Evento (20 pontos)</option>
                                        <option value="pedido_oracao">Pedido de Oração (8 pontos)</option>
                                        <option value="doacao_online">Doação Online (25 pontos)</option>
                                        <option value="voluntario_escala">Voluntário em Escala (30 pontos)</option>
                                        <option value="curso_concluido">Curso Concluído (50 pontos)</option>
                                        <option value="badge_conquistado">Badge Conquistado (100 pontos)</option>
                                        <option value="manual">Pontuação Manual (Personalizada)</option>
                                    </select>
                                    <small class="text-muted">
                                        Selecione o motivo padrão ou "Pontuação Manual" para personalizar
                                    </small>
                                    @error('pointData.motivo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if(isset($pointData['motivo']) && $pointData['motivo'] === 'manual')
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Motivo Personalizado</label>
                                    <textarea class="form-control" wire:model="pointData.motivo_custom" rows="2"
                                              placeholder="Descreva o motivo personalizado..."></textarea>
                                    <small class="text-muted">
                                        Descreva o motivo específico desta pontuação manual
                                    </small>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </button>
                            <button type="submit" class="btn bg-info text-light" wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="fas fa-save me-1"></i>Registrar
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
