<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-info">
                            <i class="fas fa-chalkboard-teacher me-2"></i>Turmas de Cursos
                        </h1>
                        <p class="mb-0 text-muted">Gerencie as turmas dos cursos da igreja</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn bg-info text-light btn-md" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#classModal">
                            <i class="fas fa-plus me-2"></i>Nova Turma
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
                        <i class="fas fa-chalkboard-teacher text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $totalTurmas ?? 0 }}</div>
                        <div class="text-muted small">Total de Turmas</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-success metric-card">
                    <div class="card-body">
                        <i class="fas fa-play-circle text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ $turmasAtivas ?? 0 }}</div>
                        <div class="text-muted small">Turmas Ativas</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-info metric-card">
                    <div class="card-body">
                        <i class="fas fa-users text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $turmasPlanejadas ?? 0 }}</div>
                        <div class="text-muted small">Turmas Planejadas</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-warning metric-card">
                    <div class="card-body">
                        <i class="fas fa-calendar-day text-warning display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-warning">{{ $turmasConcluidas ?? 0 }}</div>
                        <div class="text-muted small">Aulas Hoje</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros por Turma e Status -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Buscar Turma</label>
                        <div class="input-group">

                            <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Nome da turma">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Filtrar por Status</label>
                        <select class="form-select" wire:model.live="selectedStatus">
                            <option value="">Todos os status</option>
                            <option value="planejado">Planejada</option>
                            <option value="ativo">Ativa</option>
                            <option value="concluido">Concluída</option>
                            <option value="suspenso">Suspensa</option>
                            <option value="cancelado">Cancelada</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Filtrar por Curso</label>
                        <select class="form-select" wire:model.live="selectedCourse">
                            <option value="">Todos os cursos</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->nome }}</option>
                            @endforeach
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

                <!-- Botões de Filtro Rápido -->
                <div class="row g-2 mt-3">
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-outline-primary btn-sm" wire:click="setStatusFilter('')">
                                <i class="fas fa-chalkboard-teacher me-1"></i>Todas
                            </button>
                            <button class="btn btn-outline-success btn-sm" wire:click="setStatusFilter('ativo')">
                                <i class="fas fa-play-circle me-1"></i>Ativas
                            </button>
                            <button class="btn btn-outline-warning btn-sm" wire:click="setStatusFilter('planejado')">
                                <i class="fas fa-clock me-1"></i>Planejadas
                            </button>
                            <button class="btn btn-outline-info btn-sm" wire:click="setStatusFilter('concluido')">
                                <i class="fas fa-check-circle me-1"></i>Concluídas
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
                        <i class="fas fa-list-ul me-2"></i>Lista de Turmas
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Turma</th>
                                <th>Curso</th>
                                <th>Instrutor</th>
                                <th>Horário</th>
                                <th>Período</th>
                                <th>Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classes ?? [] as $class)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="class-icon bg-info text-light text-white me-3">
                                            <i class="fas fa-chalkboard-teacher"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $class->nome ?? 'Nome da Turma' }}</div>
                                            <small class="text-muted">{{ $class->codigo ? 'Código: ' . $class->codigo : 'Sem código' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-semibold">{{ $class->curso->nome ?? 'Nome do Curso' }}</div>
                                        <small class="text-muted">{{ $class->curso->tipo_label ?? 'Tipo do curso' }}</small>
                                    </div>
                                </td>
                                <td>{{ $class->instrutor->name ?? 'Não definido' }}</td>
                                <td>
                                    <div>{{ $class->hora_inicio ? $class->hora_inicio->format('H:i') : 'Não definido' }} - {{ $class->hora_fim ? $class->hora_fim->format('H:i') : 'Não definido' }}</div>
                                    <small class="text-muted">{{ $class->getDiaSemanaLabel($class->dia_semana) ?? 'Dia não definido' }}</small>
                                </td>
                                <td>
                                    <div>{{ $class->data_inicio ? $class->data_inicio->format('d/m/Y') : 'Não definido' }}</div>
                                    <small class="text-muted">até {{ $class->data_fim ? $class->data_fim->format('d/m/Y') : 'Não definido' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $this->getStatusBadgeClass($class->status ?? 'planejado') }}">
                                        {{ $this->getStatusLabel($class->status ?? 'planejado') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" wire:click="openModal('{{ $class->id ?? '' }}')" data-bs-toggle="modal" data-bs-target="#classModal" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-info" wire:click="viewStudents('{{ $class->id ?? '' }}')" title="Ver Alunos">
                                            <i class="fas fa-users"></i>
                                        </button>
                                        <button class="btn btn-outline-warning" wire:click="viewSchedule('{{ $class->id ?? '' }}')" title="Cronograma">
                                            <i class="fas fa-calendar-alt"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" wire:click="deleteClass('{{ $class->id ?? '' }}')"
                                                onclick="return confirm('Tem certeza que deseja excluir esta turma?')" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-chalkboard-teacher text-muted display-4 mb-3"></i>
                                    <div class="text-muted">Nenhuma turma encontrada</div>
                                    <button class="btn bg-info text-light mt-3" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#classModal">
                                        <i class="fas fa-plus me-1"></i>Criar Primeira Turma
                                    </button>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Mobile/Tablet: Cards -->
        <div class="d-lg-none">
            <div class="row g-3">
                @forelse($classes ?? [] as $class)
                <div class="col-12 col-md-6">
                    <div class="card card-hover h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="class-icon bg-info text-light text-white me-3">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-1">{{ $class->nome ?? 'Nome da Turma' }}</h6>
                                        <span class="badge bg-{{ $this->getStatusBadgeClass($class->status ?? 'planejado') }}">
                                            {{ $this->getStatusLabel($class->status ?? 'planejado') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-graduation-cap text-muted me-1"></i>
                                <small class="text-muted">{{ $class->curso->nome ?? 'Nome do Curso' }}</small>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-user text-muted me-1"></i>
                                <small class="text-muted">{{ $class->instrutor->name ?? 'Instrutor não definido' }}</small>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-clock text-muted me-1"></i>
                                <small class="text-muted">{{ $class->hora_inicio ? $class->hora_inicio->format('H:i') : 'Horário não definido' }}</small>
                            </div>
                            <div class="mb-3">
                                <i class="fas fa-calendar text-muted me-1"></i>
                                <small class="text-muted">{{ $class->data_inicio ? $class->data_inicio->format('d/m/Y') : 'Data não definida' }}</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn bg-info text-light btn-sm flex-fill" wire:click="openModal('{{ $class->id ?? '' }}')" data-bs-toggle="modal" data-bs-target="#classModal">
                                    <i class="fas fa-edit me-1"></i>Editar
                                </button>
                                <button class="btn btn-outline-info btn-sm" wire:click="viewStudents('{{ $class->id ?? '' }}')">
                                    <i class="fas fa-users"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm" wire:click="deleteClass('{{ $class->id ?? '' }}')"
                                        onclick="return confirm('Tem certeza que deseja excluir esta turma?')">
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
                            <i class="fas fa-chalkboard-teacher text-muted display-4 mb-3"></i>
                            <div class="text-muted mb-3">Nenhuma turma encontrada</div>
                            <button class="btn bg-info text-light" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#classModal">
                                <i class="fas fa-plus me-1"></i>Criar Primeira Turma
                            </button>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <style>
        .class-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
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

        .border-left-primary {
            border-left: 4px solid #007bff !important;
        }
    </style>

    <!-- Modais -->
    @include('church.courses.modals.class-modal')
</div>
