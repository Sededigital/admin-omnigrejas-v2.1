<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-info">
                            <i class="fas fa-users-cog me-2"></i>Escala de Eventos
                        </h1>
                        <p class="mb-0 text-muted">Gerencie a escala de membros para os eventos</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn bg-info text-light btn-md" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#scaleModal">
                            <i class="fas fa-user-plus me-2"></i>Escalar Membro
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Métricas - Layout Modificado -->
        <div class="row g-3 mb-4">
            <!-- Card Principal - Total de Escalas -->
            <div class="col-12 col-lg-6 mb-3">
                <div class="card border-0 shadow-sm " style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h2 class="h1 mb-1 fw-bold text-light">{{ $stats['total'] }}</h2>
                                <p class="mb-0 opacity-75">Total de Escalas</p>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-users-cog display-4 opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cards Secundários -->
            <div class="col-12 col-lg-6">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="card text-center card-hover border border-info metric-card h-100">
                            <div class="card-body">
                                <i class="fas fa-calendar-check text-info display-6 mb-2 icon-interactive"></i>
                                <div class="fw-bold h4 mb-1 text-info">{{ $stats['active'] }}</div>
                                <div class="text-muted small">Escalas Ativas</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card text-center card-hover border border-success metric-card h-100">
                            <div class="card-body">
                                <i class="fas fa-check-circle text-success display-6 mb-2 icon-interactive"></i>
                                <div class="fw-bold h4 mb-1 text-success">{{ $stats['completed'] }}</div>
                                <div class="text-muted small">Concluídas</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card text-center card-hover border border-warning metric-card h-100">
                            <div class="card-body">
                                <i class="fas fa-plus-circle text-warning display-6 mb-2 icon-interactive"></i>
                                <div class="fw-bold h4 mb-1 text-warning">{{ $stats['new_this_month'] }}</div>
                                <div class="text-muted small">Novas (Este Mês)</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card text-center card-hover border-2 border-dashed metric-card h-100" role="button" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#scaleModal">
                            <div class="card-body">
                                <i class="fas fa-user-plus text-info display-6 mb-2 icon-interactive"></i>
                                <div class="fw-bold h5 mb-1 text-info">Adicionar</div>
                                <div class="text-muted small">Nova Escala</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Buscar Escala</label>
                        <div class="input-group">
                            <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Nome do evento, membro ou função">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Filtrar por Evento</label>
                        <select class="form-select" wire:model.live="selectedEvent">
                            <option value="">Todos os eventos</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}">{{ $event->titulo }} - {{ $event->data_evento->format('d/m/Y') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            <button class="btn bg-info text-light flex-fill" wire:click="clearFilters">
                                <i class="fas fa-filter me-1"></i>Limpar Filtros
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabela de Escalas -->
        <div class="card">
            <div class="card-header d-flex align-items-center mb-3">
                <h5 class="mb-0 text-info">
                    <i class="fas fa-list-ul me-2"></i>Lista de Escalas
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Evento</th>
                            <th>Membro</th>
                            <th>Função</th>
                            <th>Data do Evento</th>
                            <th>Cadastro</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($scales as $scale)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="event-avatar bg-info text-light text-white me-3 rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                        {{ strtoupper(substr($scale->evento->titulo ?? 'E', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $scale->evento->titulo ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $scale->evento->local_evento ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar bg-success text-white me-3 rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                        {{ strtoupper(substr($scale->membro->user->name ?? 'M', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $scale->membro->user->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $scale->membro->cargo ?? '' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info text-light">{{ $scale->funcao }}</span>
                            </td>
                            <td>
                                <div>{{ $scale->evento->data_evento ? $scale->evento->data_evento->format('d/m/Y') : 'N/A' }}</div>
                                <small class="text-muted">
                                    {{ $scale->evento->hora_inicio ? $scale->evento->hora_inicio->format('H:i') : '' }}
                                    @if($scale->evento->hora_fim)
                                        - {{ $scale->evento->hora_fim->format('H:i') }}
                                    @endif
                                </small>
                            </td>
                            <td>
                                <div>{{ $scale->created_at->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $scale->created_at->diffForHumans() }}</small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" wire:click="openModal({{ $scale->id }})" data-bs-toggle="modal" data-bs-target="#scaleModal" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" wire:click="deleteScale({{ $scale->id }})"
                                            onclick="return confirm('Tem certeza que deseja remover esta escala?')" title="Remover">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-users-cog text-muted display-4 mb-3"></i>
                                <div class="text-muted">Nenhuma escala encontrada</div>
                                @if($search || $selectedEvent)
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
            @if($scales->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">
                        Mostrando {{ $scales->firstItem() }}-{{ $scales->lastItem() }} de {{ $scales->total() }} registros
                    </span>
                    {{ $scales->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>

    @include('church.events.modals.scale-modal')
    <!-- Scripts para Scale -->
    <script src="{{ asset('system/js/events.js') }}"></script>

</div>
