<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-info">
                            <i class="fas fa-graduation-cap me-2"></i>Gestão de Cursos
                        </h1>
                        <p class="mb-0 text-muted">Gerencie todos os cursos da igreja</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn bg-info text-light btn-md" onclick="window.coursesManager ? window.coursesManager.openModal() : openCourseModal()" data-bs-toggle="modal" data-bs-target="#courseModal">
                            <i class="fas fa-plus me-2"></i>Novo Curso
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
                        <i class="fas fa-graduation-cap text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $totalCursos ?? 0 }}</div>
                        <div class="text-muted small">Total de Cursos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-success metric-card">
                    <div class="card-body">
                        <i class="fas fa-play-circle text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ $cursosAtivos ?? 0 }}</div>
                        <div class="text-muted small">Cursos Ativos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-warning metric-card">
                    <div class="card-body">
                        <i class="fas fa-clock text-warning display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-warning">{{ $cursosPlanejados ?? 0 }}</div>
                        <div class="text-muted small">Planejados</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-info metric-card">
                    <div class="card-body">
                        <i class="fas fa-check-circle text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $cursosConcluidos ?? 0 }}</div>
                        <div class="text-muted small">Concluídos</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Buscar Curso</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text"  autocomplete="new-password" class="form-control" wire:model.live.debounce.300ms="search" placeholder="Nome do curso">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Filtrar por Status</label>
                        <select class="form-select" wire:model.live="selectedStatus">
                            <option value="">Todos os status</option>
                            <option value="planejado">Planejado</option>
                            <option value="ativo">Ativo</option>
                            <option value="concluido">Concluído</option>
                            <option value="suspenso">Suspenso</option>
                            <option value="cancelado">Cancelado</option>
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

                <!-- Botões de Filtro Rápido -->
                <div class="row g-2 mt-3">
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-outline-primary btn-sm" wire:click="setStatusFilter('')">
                                <i class="fas fa-graduation-cap me-1"></i>Todos
                            </button>
                            <button class="btn btn-outline-success btn-sm" wire:click="setStatusFilter('ativo')">
                                <i class="fas fa-play-circle me-1"></i>Ativos
                            </button>
                            <button class="btn btn-outline-warning btn-sm" wire:click="setStatusFilter('planejado')">
                                <i class="fas fa-clock me-1"></i>Planejados
                            </button>
                            <button class="btn btn-outline-info btn-sm" wire:click="setStatusFilter('concluido')">
                                <i class="fas fa-check-circle me-1"></i>Concluídos
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" wire:click="setStatusFilter('suspenso')">
                                <i class="fas fa-pause-circle me-1"></i>Suspensos
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
                        <i class="fas fa-list-ul me-2"></i>Lista de Cursos
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Curso</th>
                                <th>Tipo</th>
                                <th>Instrutor</th>
                                <th>Duração</th>
                                <th>Status</th>
                                <th>Início</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($courses ?? [] as $course)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="course-icon bg-info text-light text-white me-3">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $course->nome ?? 'Nome do Curso' }}</div>
                                            <small class="text-muted">{{ Str::limit($course->descricao ?? 'Descrição do curso', 50) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $course->tipo_label ?? 'Geral' }}
                                    </span>
                                </td>
                                <td>{{ $course->instrutorPrincipal->name ?? 'Não definido' }}</td>
                                <td>
                                    <div>{{ $course->duracao_formatada ?? '0 semanas' }}</div>
                                    <small class="text-muted">{{ $course->carga_horaria_formatada ?? '0 horas' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $this->getStatusBadgeClass($course->status ?? 'planejado') }}">
                                        {{ $course->status_label ?? 'Planejado' }}
                                    </span>
                                </td>
                                <td>
                                    <div>{{ $course->data_inicio ? $course->data_inicio->format('d/m/Y') : 'Não definido' }}</div>
                                    <small class="text-muted">{{ $course->data_inicio ? $course->data_inicio->diffForHumans() : '' }}</small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" onclick="editCourse('{{ $course->id ?? '' }}')" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-info" wire:click="viewCourse('{{ $course->id ?? '' }}')" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" wire:click="deleteCourse('{{ $course->id ?? '' }}')"
                                                onclick="return confirm('Tem certeza que deseja excluir este curso?')" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-graduation-cap text-muted display-4 mb-3"></i>
                                    <div class="text-muted">Nenhum curso encontrado</div>
                                    <button class="btn bg-info text-light mt-3" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#courseModal">
                                        <i class="fas fa-plus me-1"></i>Criar Primeiro Curso
                                    </button>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(isset($courses) && $courses->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Mostrando {{ $courses->firstItem() }}-{{ $courses->lastItem() }} de {{ $courses->total() }} registros</span>
                        <nav aria-label="Paginação">
                            {{ $courses->links() }}
                        </nav>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Mobile/Tablet: Cards -->
        <div class="d-lg-none">
            <div class="row g-3">
                @forelse($courses ?? [] as $course)
                <div class="col-12 col-md-6">
                    <div class="card card-hover h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="course-icon bg-info text-light text-white me-3">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-1">{{ $course->nome ?? 'Nome do Curso' }}</h6>
                                        <span class="badge bg-{{ $this->getStatusBadgeClass($course->status ?? 'planejado') }}">
                                            {{ $course->status_label ?? 'Planejado' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-user text-muted me-1"></i>
                                <small class="text-muted">{{ $course->instrutorPrincipal->name ?? 'Instrutor não definido' }}</small>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-clock text-muted me-1"></i>
                                <small class="text-muted">{{ $course->duracao_formatada ?? '0 semanas' }} • {{ $course->carga_horaria_formatada ?? '0 horas' }}</small>
                            </div>
                            <div class="mb-3">
                                <i class="fas fa-calendar text-muted me-1"></i>
                                <small class="text-muted">{{ $course->data_inicio ? $course->data_inicio->format('d/m/Y') : 'Data não definida' }}</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn bg-info text-light btn-sm flex-fill" wire:click="openModal('{{ $course->id ?? '' }}')" data-bs-toggle="modal" data-bs-target="#courseModal">
                                    <i class="fas fa-edit me-1"></i>Editar
                                </button>
                                <button class="btn btn-outline-info btn-sm" wire:click="viewCourse('{{ $course->id ?? '' }}')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm" wire:click="deleteCourse('{{ $course->id ?? '' }}')"
                                        onclick="return confirm('Tem certeza que deseja excluir este curso?')">
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
                            <i class="fas fa-graduation-cap text-muted display-4 mb-3"></i>
                            <div class="text-muted mb-3">Nenhum curso encontrado</div>
                            <button class="btn bg-info text-light" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#courseModal">
                                <i class="fas fa-plus me-1"></i>Criar Primeiro Curso
                            </button>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Paginação Mobile -->
            @if(isset($courses) && $courses->hasPages())
            <div class="mt-4">
                <nav aria-label="Paginação Mobile">
                    {{ $courses->links() }}
                </nav>
                <div class="text-center text-muted mt-2">
                    <small>Mostrando {{ $courses->firstItem() }}-{{ $courses->lastItem() }} de {{ $courses->total() }} registros</small>
                </div>
            </div>
            @endif
        </div>
    </div>

    <style>
        .course-icon {
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
    </style>

    <!-- Modais -->
    @include('church.courses.modals.course-modal')

    <script src="{{ asset('system/js/courses.js') }}"></script>
</div>
