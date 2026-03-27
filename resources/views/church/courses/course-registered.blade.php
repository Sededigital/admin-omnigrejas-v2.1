<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-info">
                            <i class="fas fa-user-graduate me-2"></i>Matrículas de Cursos
                        </h1>
                        <p class="mb-0 text-muted">Gerencie as matrículas dos alunos nos cursos</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn bg-info text-light btn-md" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#registrationModal">
                            <i class="fas fa-plus me-2"></i>Nova Matrícula
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
                        <i class="fas fa-user-graduate text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $totalMatriculas ?? 0 }}</div>
                        <div class="text-muted small">Total de Matrículas</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-success metric-card">
                    <div class="card-body">
                        <i class="fas fa-play-circle text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ $matriculasAtivas ?? 0 }}</div>
                        <div class="text-muted small">Matrículas Ativas</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-warning metric-card">
                    <div class="card-body">
                        <i class="fas fa-clock text-warning display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-warning">{{ $matriculasConcluidas ?? 0 }}</div>
                        <div class="text-muted small">Concluídas</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-info metric-card">
                    <div class="card-body">
                        <i class="fas fa-check-circle text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $matriculasDesistentes ?? 0 }}</div>
                        <div class="text-muted small">Concluídas</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros por Matrícula e Status -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Buscar Aluno</label>
                        <div class="input-group">

                            <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Nome do aluno">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Filtrar por Status</label>
                        <select class="form-select" wire:model.live="selectedStatus">
                            <option value="">Todos os status</option>
                            <option value="ativo">Ativa</option>
                            <option value="concluido">Concluída</option>
                            <option value="desistente">Desistente</option>
                            <option value="transferido">Transferida</option>
                            <option value="suspenso">Suspensa</option>
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
                                <i class="fas fa-user-graduate me-1"></i>Todas
                            </button>
                            <button class="btn btn-outline-success btn-sm" wire:click="setStatusFilter('ativo')">
                                <i class="fas fa-play-circle me-1"></i>Ativas
                            </button>
                            <button class="btn btn-outline-info btn-sm" wire:click="setStatusFilter('concluido')">
                                <i class="fas fa-check-circle me-1"></i>Concluídas
                            </button>
                            <button class="btn btn-outline-danger btn-sm" wire:click="setStatusFilter('desistente')">
                                <i class="fas fa-times-circle me-1"></i>Desistentes
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
                        <i class="fas fa-list-ul me-2"></i>Lista de Matrículas
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Aluno</th>
                                <th>Turma</th>
                                <th>Curso</th>
                                <th>Data Matrícula</th>
                                <th>Status</th>
                                <th>Apto</th>
                                <th>Certificado</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($registrations ?? [] as $registration)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar bg-info text-light text-white me-3">
                                            {{ strtoupper(substr($registration->membro->user->name ?? 'A', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $registration->membro->user->name ?? 'Nome do Aluno' }}</div>
                                            <small class="text-muted">{{ $registration->membro->cargo_label ?? 'Cargo não definido' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-semibold">{{ $registration->turma->nome ?? 'Nome da Turma' }}</div>
                                        <small class="text-muted">{{ $registration->turma->codigo ? 'Código: ' . $registration->turma->codigo : 'Sem código' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-semibold">{{ $registration->turma->curso->nome ?? 'Nome do Curso' }}</div>
                                        <small class="text-muted">{{ $registration->turma->curso->tipo_label ?? 'Tipo do curso' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $registration->data_matricula ? $registration->data_matricula->format('d/m/Y') : 'Não definida' }}</div>
                                    <small class="text-muted">{{ $registration->data_matricula ? $registration->data_matricula->diffForHumans() : '' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $this->getStatusBadgeClass($registration->status ?? 'pendente') }}">
                                        {{ $this->getStatusLabel($registration->status ?? 'pendente') }}
                                    </span>
                                </td>
                                <td>
                                    @if($registration->apto)
                                        <i class="fas fa-check-circle text-success" title="Apto"></i>
                                        @if($registration->data_apto)
                                            <br><small class="text-muted">{{ $registration->data_apto->format('d/m/Y') }}</small>
                                        @endif
                                    @else
                                        <i class="fas fa-times-circle text-danger" title="Não apto"></i>
                                    @endif
                                </td>
                                <td>
                                    @if($registration->certificado_emitido)
                                        <i class="fas fa-certificate text-success" title="Certificado emitido"></i>
                                        @if($registration->data_certificado)
                                            <br><small class="text-muted">{{ $registration->data_certificado->format('d/m/Y') }}</small>
                                        @endif
                                    @else
                                        <i class="fas fa-times-circle text-muted" title="Certificado não emitido"></i>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" wire:click="openModal('{{ $registration->id ?? '' }}')" data-bs-toggle="modal" data-bs-target="#registrationModal" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-info" wire:click="viewProgress('{{ $registration->id ?? '' }}')" title="Ver Progresso">
                                            <i class="fas fa-chart-line"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" wire:click="cancelRegistration('{{ $registration->id ?? '' }}')"
                                                onclick="return confirm('Tem certeza que deseja cancelar esta matrícula?')" title="Cancelar">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-user-graduate text-muted display-4 mb-3"></i>
                                    <div class="text-muted">Nenhuma matrícula encontrada</div>
                                    <button class="btn bg-info text-light mt-3" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#registrationModal">
                                        <i class="fas fa-plus me-1"></i>Criar Primeira Matrícula
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
                @forelse($registrations ?? [] as $registration)
                <div class="col-12 col-md-6">
                    <div class="card card-hover h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar bg-info text-light text-white me-3">
                                        {{ strtoupper(substr($registration->membro->user->name ?? 'A', 0, 2)) }}
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-1">{{ $registration->membro->user->name ?? 'Nome do Aluno' }}</h6>
                                        <span class="badge bg-{{ $this->getStatusBadgeClass($registration->status ?? 'pendente') }}">
                                            {{ $this->getStatusLabel($registration->status ?? 'pendente') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-graduation-cap text-muted me-1"></i>
                                <small class="text-muted">{{ $registration->turma->curso->nome ?? 'Nome do Curso' }}</small>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-chalkboard-teacher text-muted me-1"></i>
                                <small class="text-muted">{{ $registration->turma->nome ?? 'Nome da Turma' }}</small>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-calendar text-muted me-1"></i>
                                <small class="text-muted">{{ $registration->data_matricula ? $registration->data_matricula->format('d/m/Y') : 'Data não definida' }}</small>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted">Progresso</small>
                                    <small class="text-muted">{{ $registration->progresso ?? 0 }}%</small>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-info text-light" role="progressbar" style="width: {{ $registration->progresso ?? 0 }}%"></div>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn bg-info text-light btn-sm flex-fill" wire:click="openModal('{{ $registration->id ?? '' }}')" data-bs-toggle="modal" data-bs-target="#registrationModal">
                                    <i class="fas fa-edit me-1"></i>Editar
                                </button>
                                <button class="btn btn-outline-info btn-sm" wire:click="viewProgress('{{ $registration->id ?? '' }}')">
                                    <i class="fas fa-chart-line"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm" wire:click="cancelRegistration('{{ $registration->id ?? '' }}')"
                                        onclick="return confirm('Tem certeza que deseja cancelar esta matrícula?')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-user-graduate text-muted display-4 mb-3"></i>
                            <div class="text-muted mb-3">Nenhuma matrícula encontrada</div>
                            <button class="btn bg-info text-light" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#registrationModal">
                                <i class="fas fa-plus me-1"></i>Criar Primeira Matrícula
                            </button>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <style>
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
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

        .progress {
            border-radius: 10px;
            background-color: #e9ecef;
        }

        .progress-bar {
            border-radius: 10px;
        }
    </style>

    <!-- Modais -->
    @include('church.courses.modals.registration-modal')
</div>
