<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-primary">
                            <i class="fas fa-calendar-alt me-2"></i>Gestão de Eventos
                        </h1>
                        <p class="mb-0 text-muted">Gerencie todos os eventos da igreja</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-primary btn-md" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#eventModal">
                            <i class="fas fa-plus-circle me-2"></i>Criar Evento
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
                        <i class="fas fa-calendar-alt text-primary display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-primary">{{ $stats['total'] }}</div>
                        <div class="text-muted small">Total de Eventos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-info metric-card">
                    <div class="card-body">
                        <i class="fas fa-clock text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $stats['upcoming'] }}</div>
                        <div class="text-muted small">Próximos Eventos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-warning metric-card">
                    <div class="card-body">
                        <i class="fas fa-play-circle text-warning display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-warning">{{ $stats['ongoing'] }}</div>
                        <div class="text-muted small">Em Andamento</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-success metric-card">
                    <div class="card-body">
                        <i class="fas fa-check-circle text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ $stats['completed'] }}</div>
                        <div class="text-muted small">Concluídos</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros por Status -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Buscar Evento</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Título, descrição ou local">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Filtrar por Status</label>
                        <select class="form-select" wire:model.live="selectedStatus">
                            <option value="">Todos os status</option>
                            <option value="agendado">Agendado</option>
                            <option value="realizado">Realizado</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary flex-fill" wire:click="clearFilters">
                                <i class="fas fa-filter me-1"></i>Limpar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Botões de Filtro Rápido -->
                <div class="row g-2 mt-3">
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-outline-primary btn-sm" wire:click="setStatusFilter('')">
                                <i class="fas fa-calendar-alt me-1"></i>Todos
                            </button>
                            <button class="btn btn-outline-primary btn-sm" wire:click="setStatusFilter('agendado')">
                                <i class="fas fa-calendar-plus me-1"></i>Agendados
                            </button>
                            <button class="btn btn-outline-success btn-sm" wire:click="setStatusFilter('realizado')">
                                <i class="fas fa-check-circle me-1"></i>Realizados
                            </button>
                            <button class="btn btn-outline-danger btn-sm" wire:click="setStatusFilter('cancelado')">
                                <i class="fas fa-times-circle me-1"></i>Cancelados
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
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-list-ul me-2"></i>Lista de Eventos
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Evento</th>
                                <th>Data/Hora</th>
                                <th>Local</th>
                                <th>Status</th>
                                <th>Cadastro</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $event)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="event-avatar bg-primary text-white me-3 rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($event->titulo, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $event->titulo }}</div>
                                            <small class="text-muted">{{ Str::limit($event->descricao, 50) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $event->data_evento ? $event->data_evento->format('d/m/Y') : 'N/A' }}</div>
                                    <small class="text-muted">
                                        {{ $event->hora_inicio ? $event->hora_inicio->format('H:i') : '' }}
                                        @if($event->hora_fim)
                                            - {{ $event->hora_fim->format('H:i') }}
                                        @endif
                                    </small>
                                </td>
                                <td>{{ $event->local_evento ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $this->getStatusBadgeClass($event->status) }}">
                                        {{ $this->getStatusLabel($event->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div>{{ $event->created_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $event->created_at->diffForHumans() }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" wire:click="openModal('{{ $event->id }}')" data-bs-toggle="modal" data-bs-target="#eventModal" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-{{ $event->status === 'agendado' ? 'warning' : 'success' }}"
                                                wire:click="toggleEventStatus('{{ $event->id }}')"
                                                title="{{ $event->status === 'agendado' ? 'Cancelar' : 'Reagendar' }}">
                                            <i class="fas fa-{{ $event->status === 'agendado' ? 'times-circle' : 'calendar-plus' }}"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" wire:click="deleteEvent('{{ $event->id }}')"
                                                onclick="return confirm('Tem certeza que deseja excluir este evento?')" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-calendar-alt text-muted display-4 mb-3"></i>
                                    <div class="text-muted">Nenhum evento encontrado</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Mostrando {{ $events->firstItem() }}-{{ $events->lastItem() }} de {{ $events->total() }} registros</span>
                        <nav aria-label="Paginação">
                            {{ $events->links() }}
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile/Tablet: Cards -->
        <div class="d-lg-none">
            <div class="row g-3">
                @forelse($events as $event)
                <div class="col-12 col-md-6">
                    <div class="card card-hover h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="event-avatar bg-primary text-white me-3 rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                        {{ strtoupper(substr($event->titulo, 0, 2)) }}
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-1">{{ $event->titulo }}</h6>
                                        <span class="badge bg-{{ $this->getStatusBadgeClass($event->status) }}">
                                            {{ $this->getStatusLabel($event->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $event->status === 'agendado' ? 'success' : 'secondary' }} mb-2">
                                        {{ $event->status === 'agendado' ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </div>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-calendar text-muted me-1"></i>
                                <small class="text-muted">{{ $event->data_evento ? $event->data_evento->format('d/m/Y') : 'N/A' }}</small>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-clock text-muted me-1"></i>
                                <small class="text-muted">{{ $event->hora_inicio ? $event->hora_inicio->format('H:i') : 'N/A' }}</small>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                <small class="text-muted">{{ $event->local_evento ?? 'N/A' }}</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary btn-sm flex-fill" wire:click="openModal('{{ $event->id }}')" data-bs-toggle="modal" data-bs-target="#eventModal">
                                    <i class="fas fa-edit me-1"></i>Editar
                                </button>
                                <button class="btn btn-outline-danger btn-sm" wire:click="deleteEvent('{{ $event->id }}')"
                                        onclick="return confirm('Tem certeza que deseja excluir este evento?')">
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
                            <i class="fas fa-calendar-alt text-muted display-4 mb-3"></i>
                            <div class="text-muted">Nenhum evento encontrado</div>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Paginação Mobile -->
            @if($events->hasPages())
            <div class="mt-4">
                <nav aria-label="Paginação Mobile">
                    {{ $events->links() }}
                </nav>
                <div class="text-center text-muted mt-2">
                    <small>Mostrando {{ $events->firstItem() }}-{{ $events->lastItem() }} de {{ $events->total() }} registros</small>
                </div>
            </div>
            @endif
        </div>
    </div>


    <!-- Scripts para Events -->
    <script src="{{ asset('system/js/events.js') }}"></script>

    <!-- Incluir Modal -->
    @include('church.events.modals.event-modal')

</div>
