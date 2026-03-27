<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-info">
                            <i class="fas fa-church me-2"></i>Cultos Padrão
                        </h1>
                        <p class="mb-0 text-muted">Gerencie os cultos semanais fixos da igreja</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn bg-info text-light btn-md" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#cultModal">
                            <i class="fas fa-plus-circle me-2"></i>Criar Culto
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
                        <i class="fas fa-church text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $stats['total'] }}</div>
                        <div class="text-muted small">Total de Cultos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-success metric-card">
                    <div class="card-body">
                        <i class="fas fa-check-circle text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ $stats['active'] }}</div>
                        <div class="text-muted small">Cultos Ativos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-warning metric-card">
                    <div class="card-body">
                        <i class="fas fa-pause-circle text-warning display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-warning">{{ $stats['inactive'] }}</div>
                        <div class="text-muted small">Cultos Inativos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-info metric-card">
                    <div class="card-body">
                        <i class="fas fa-plus-circle text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $stats['new_this_month'] }}</div>
                        <div class="text-muted small">Novos (Este Mês)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Buscar Culto</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Título ou descrição">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Ordenar por</label>
                        <select class="form-select" wire:model.live="orderBy">
                            <option value="dia_semana">Dia da Semana</option>
                            <option value="hora_inicio">Horário</option>
                            <option value="titulo">Título</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button class="btn bg-info text-light flex-fill" wire:click="clearFilters">
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
                    <h5 class="mb-0 text-info">
                        <i class="fas fa-list-ul me-2"></i>Lista de Cultos Padrão
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Culto</th>
                                <th>Dia da Semana</th>
                                <th>Horário</th>
                                <th>Status</th>
                                <th>Cadastro</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cults as $cult)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="cult-avatar bg-info text-light text-white me-3 rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($cult->titulo, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $cult->titulo }}</div>
                                            <small class="text-muted">{{ Str::limit($cult->descricao, 50) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $this->getDiaSemanaBadgeClass($cult->dia_semana) }}">
                                        {{ $this->getDiaSemanaLabel($cult->dia_semana) }}
                                    </span>
                                </td>
                                <td>
                                    <div>{{ $cult->hora_inicio ? $cult->hora_inicio->format('H:i') : 'N/A' }}</div>
                                    @if($cult->hora_fim)
                                        <small class="text-muted">até {{ $cult->hora_fim->format('H:i') }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $cult->ativo ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $cult->ativo ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td>
                                    <div>{{ $cult->criado_em ? $cult->criado_em->format('d/m/Y') : 'N/A' }}</div>
                                    <small class="text-muted">{{ $cult->criado_em ? $cult->criado_em->diffForHumans() : '' }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" wire:click="openModal('{{ $cult->id }}')" data-bs-toggle="modal" data-bs-target="#cultModal" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-{{ $cult->ativo ? 'warning' : 'success' }}"
                                                wire:click="toggleCultStatus('{{ $cult->id }}')"
                                                title="{{ $cult->ativo ? 'Desativar' : 'Ativar' }}">
                                            <i class="fas fa-{{ $cult->ativo ? 'pause-circle' : 'play-circle' }}"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" wire:click="deleteCult('{{ $cult->id }}')"
                                                onclick="return confirm('Tem certeza que deseja excluir este culto?')" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-church text-muted display-4 mb-3"></i>
                                    <div class="text-muted">Nenhum culto padrão encontrado</div>
                                    @if($search)
                                        <button class="btn btn-sm btn-outline-primary mt-2" wire:click="$set('search', '')">
                                            Limpar filtros
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($cults->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">
                            Mostrando {{ $cults->firstItem() }}-{{ $cults->lastItem() }} de {{ $cults->total() }} registros
                        </span>
                        {{ $cults->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Mobile/Tablet: Cards -->
        <div class="d-lg-none">
            <div class="row g-3">
                @forelse($cults as $cult)
                <div class="col-12 col-md-6">
                    <div class="card card-hover h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="cult-avatar bg-info text-light text-white me-3 rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                        {{ strtoupper(substr($cult->titulo, 0, 2)) }}
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-1">{{ $cult->titulo }}</h6>
                                        <span class="badge bg-{{ $this->getDiaSemanaBadgeClass($cult->dia_semana) }}">
                                            {{ $this->getDiaSemanaLabel($cult->dia_semana) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge {{ $cult->ativo ? 'bg-success' : 'bg-secondary' }} mb-2">
                                        {{ $cult->ativo ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </div>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-clock text-muted me-1"></i>
                                <small class="text-muted">{{ $cult->hora_inicio ? $cult->hora_inicio->format('H:i') : 'N/A' }}</small>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-align-left text-muted me-1"></i>
                                <small class="text-muted">{{ Str::limit($cult->descricao, 60) }}</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn bg-info text-light btn-sm flex-fill" wire:click="openModal('{{ $cult->id }}')" data-bs-toggle="modal" data-bs-target="#cultModal">
                                    <i class="fas fa-edit me-1"></i>Editar
                                </button>
                                <button class="btn btn-outline-danger btn-sm" wire:click="deleteCult('{{ $cult->id }}')"
                                        onclick="return confirm('Tem certeza que deseja excluir este culto?')">
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
                            <i class="fas fa-church text-muted display-4 mb-3"></i>
                            <div class="text-muted">Nenhum culto padrão encontrado</div>
                            @if($search)
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
            @if($cults->hasPages())
            <div class="mt-4">
                <nav aria-label="Paginação Mobile">
                    {{ $cults->links() }}
                </nav>
                <div class="text-center text-muted mt-2">
                    <small>Mostrando {{ $cults->firstItem() }}-{{ $cults->lastItem() }} de {{ $cults->total() }} registros</small>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Modal para Culto Padrão -->
    <div class="modal fade" id="cultModal" tabindex="-1" aria-labelledby="cultModalLabel" aria-hidden="true"
         data-bs-backdrop="static" data-bs-keyboard="false" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <!-- Header do Modal -->
                <div class="modal-header bg-light border-bottom">
                    <h5 class="modal-title fw-bold" id="cultModalLabel">
                        <i class="fas fa-church text-info me-2"></i>
                        <span id="modal-title">{{ $editingCult ? 'Editar Culto Padrão' : 'Criar Culto Padrão' }}</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <!-- Corpo do Modal -->
                <div class="modal-body p-4">
                    <form wire:submit.prevent="saveCult">

                        <!-- Título -->
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input type="text"  autocomplete="new-password" class="form-control @error('titulo') is-invalid @enderror"
                                           wire:model="titulo" placeholder="Nome do culto" required>
                                    <label><i class="fas fa-heading text-info me-1"></i>Título do Culto *</label>
                                    @error('titulo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Dia da Semana -->
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select @error('dia_semana') is-invalid @enderror"
                                            wire:model="dia_semana">
                                        <option value="">Selecione o dia</option>
                                        <option value="0">Domingo</option>
                                        <option value="1">Segunda-feira</option>
                                        <option value="2">Terça-feira</option>
                                        <option value="3">Quarta-feira</option>
                                        <option value="4">Quinta-feira</option>
                                        <option value="5">Sexta-feira</option>
                                        <option value="6">Sábado</option>
                                    </select>
                                    <label><i class="fas fa-calendar-week text-info me-1"></i>Dia da Semana *</label>
                                    @error('dia_semana')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Hora de Início -->
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="time" class="form-control @error('hora_inicio') is-invalid @enderror"
                                           wire:model="hora_inicio" required>
                                    <label><i class="fas fa-clock text-info me-1"></i>Hora de Início *</label>
                                    @error('hora_inicio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Hora de Fim -->
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="time" class="form-control @error('hora_fim') is-invalid @enderror"
                                           wire:model="hora_fim">
                                    <label><i class="fas fa-clock text-info me-1"></i>Hora de Fim</label>
                                    @error('hora_fim')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status Ativo -->
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="ativo" wire:model="ativo">
                                    <label class="form-check-label" for="ativo">
                                        <i class="fas fa-toggle-on text-info me-1"></i>Culto Ativo
                                    </label>
                                </div>
                            </div>

                            <!-- Descrição -->
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control @error('descricao') is-invalid @enderror"
                                              wire:model="descricao" rows="3"
                                              placeholder="Descrição do culto"></textarea>
                                    <label><i class="fas fa-align-left text-info me-1"></i>Descrição</label>
                                    @error('descricao')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status Visual -->
                            <div class="col-12">
                                <div class="alert alert-light border">
                                    <i class="fas fa-info-circle text-info me-2"></i>
                                    <strong>Status:</strong>
                                    <span class="text-muted">
                                        {{ $editingCult ? 'Editando Culto Padrão' : 'Novo Culto Padrão' }}
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
                    <button type="button" class="btn bg-info text-light" wire:click="saveCult" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveCult">
                            <i class="fas fa-save me-1"></i>{{ $editingCult ? 'Atualizar Culto' : 'Salvar Culto' }}
                        </span>
                        <span wire:loading wire:target="saveCult">
                            <i class="fas fa-spinner fa-spin me-1"></i>{{ $editingCult ? 'Atualizando...' : 'Salvando...' }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts para Standard Cult -->
    <script src="{{ asset('system/js/events.js') }}"></script>

</div>
