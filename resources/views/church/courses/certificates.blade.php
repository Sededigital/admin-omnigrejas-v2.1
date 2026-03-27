<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-info">
                            <i class="fas fa-certificate me-2"></i>Certificados de Cursos
                        </h1>
                        <p class="mb-0 text-muted">Gerencie os certificados emitidos para os alunos</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn bg-info text-light btn-md" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#certificateModal">
                            <i class="fas fa-plus me-2"></i>Emitir Certificado
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
                        <i class="fas fa-certificate text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $totalCertificados ?? 0 }}</div>
                        <div class="text-muted small">Total de Certificados</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-success metric-card">
                    <div class="card-body">
                        <i class="fas fa-check-circle text-success display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-success">{{ $certificadosEmitidos ?? 0 }}</div>
                        <div class="text-muted small">Certificados Emitidos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-warning metric-card">
                    <div class="card-body">
                        <i class="fas fa-clock text-warning display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-warning">{{ $certificadosPendentes ?? 0 }}</div>
                        <div class="text-muted small">Pendentes</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card text-center card-hover border border-info metric-card">
                    <div class="card-body">
                        <i class="fas fa-download text-info display-6 mb-2 icon-interactive"></i>
                        <div class="fw-bold h4 mb-1 text-info">{{ $certificadosCancelados ?? 0 }}</div>
                        <div class="text-muted small">Baixados</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Validação de Certificado -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-search me-2"></i>Validar Certificado
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Código de Verificação</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-barcode text-muted"></i>
                            </span>
                            <input type="text"  autocomplete="new-password" class="form-control" wire:model="validationCode" placeholder="Digite o código de verificação">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button class="btn bg-info text-light w-100" wire:click="validateCertificate">
                            <i class="fas fa-search me-1"></i>Validar Certificado
                        </button>
                    </div>
                </div>

                @if(isset($validationResult))
                <div class="mt-3">
                    @if($validationResult['valid'])
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Certificado Válido</strong><br>
                            <small class="text-muted">
                                Aluno: {{ $validationResult['member'] ?? 'N/A' }}<br>
                                Curso: {{ $validationResult['course'] ?? 'N/A' }}<br>
                                Data de Emissão: {{ $validationResult['date'] ?? 'N/A' }}
                            </small>
                        </div>
                    @else
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle me-2"></i>
                            <strong>{{ $validationResult['message'] ?? 'Código inválido' }}</strong>
                        </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Filtros por Certificado e Status -->
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
                            <option value="emitido">Emitido</option>
                            <option value="pendente">Pendente</option>
                            <option value="cancelado">Cancelado</option>
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
                                <i class="fas fa-certificate me-1"></i>Todos
                            </button>
                            <button class="btn btn-outline-success btn-sm" wire:click="setStatusFilter('emitido')">
                                <i class="fas fa-check-circle me-1"></i>Emitidos
                            </button>
                            <button class="btn btn-outline-warning btn-sm" wire:click="setStatusFilter('pendente')">
                                <i class="fas fa-clock me-1"></i>Pendentes
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
                        <i class="fas fa-list-ul me-2"></i>Lista de Certificados
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Aluno</th>
                                <th>Curso</th>
                                <th>Turma</th>
                                <th>Data Emissão</th>
                                <th>Data Conclusão</th>
                                <th>Frequência</th>
                                <th>Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($certificates ?? [] as $certificate)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar bg-info text-light text-white me-3">
                                            {{ strtoupper(substr($certificate->matricula->membro->user->name ?? 'A', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $certificate->matricula->membro->user->name ?? 'Nome do Aluno' }}</div>
                                            <small class="text-muted">{{ $certificate->matricula->membro->cargo_label ?? 'Cargo não definido' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-semibold">{{ $certificate->matricula->turma->curso->nome ?? 'Nome do Curso' }}</div>
                                        <small class="text-muted">{{ $certificate->matricula->turma->curso->tipo_label ?? 'Tipo do curso' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-semibold">{{ $certificate->matricula->turma->nome ?? 'Nome da Turma' }}</div>
                                        <small class="text-muted">{{ $certificate->matricula->turma->codigo ? 'Código: ' . $certificate->matricula->turma->codigo : 'Sem código' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $certificate->data_emissao ? $certificate->data_emissao->format('d/m/Y') : 'Não emitido' }}</div>
                                    <small class="text-muted">{{ $certificate->data_emissao ? $certificate->data_emissao->diffForHumans() : '' }}</small>
                                </td>
                                <td>
                                    <div>{{ $certificate->data_conclusao ? $certificate->data_conclusao->format('d/m/Y') : 'Não definida' }}</div>
                                </td>
                                <td>
                                    <div>{{ $certificate->frequencia_final ? $certificate->frequencia_final . '%' : 'Não definida' }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $this->getStatusBadgeClass($certificate->data_emissao ? 'emitido' : 'pendente') }}">
                                        {{ $this->getStatusLabel($certificate->data_emissao ? 'emitido' : 'pendente') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" wire:click="openModal('{{ $certificate->id ?? '' }}')" data-bs-toggle="modal" data-bs-target="#certificateModal" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @if($certificate->data_emissao)
                                        <button class="btn btn-outline-info" wire:click="downloadCertificate('{{ $certificate->id ?? '' }}')" title="Download">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" wire:click="previewCertificate('{{ $certificate->id ?? '' }}')" title="Preview">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @endif
                                        <button class="btn btn-outline-danger" wire:click="revokeCertificate('{{ $certificate->id ?? '' }}')"
                                                onclick="return confirm('Tem certeza que deseja revogar este certificado?')" title="Revogar">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-certificate text-muted display-4 mb-3"></i>
                                    <div class="text-muted">Nenhum certificado encontrado</div>
                                    <button class="btn bg-info text-light mt-3" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#certificateModal">
                                        <i class="fas fa-plus me-1"></i>Emitir Primeiro Certificado
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
                @forelse($certificates ?? [] as $certificate)
                <div class="col-12 col-md-6">
                    <div class="card card-hover h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar bg-info text-light text-white me-3">
                                        {{ strtoupper(substr($certificate->matricula->membro->user->name ?? 'A', 0, 2)) }}
                                    </div>
                                    <div>
                                        <h6 class="card-title mb-1">{{ $certificate->matricula->membro->user->name ?? 'Nome do Aluno' }}</h6>
                                        <span class="badge bg-{{ $this->getStatusBadgeClass($certificate->data_emissao ? 'emitido' : 'pendente') }}">
                                            {{ $this->getStatusLabel($certificate->data_emissao ? 'emitido' : 'pendente') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-graduation-cap text-muted me-1"></i>
                                <small class="text-muted">{{ $certificate->matricula->turma->curso->nome ?? 'Nome do Curso' }}</small>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-chalkboard-teacher text-muted me-1"></i>
                                <small class="text-muted">{{ $certificate->matricula->turma->nome ?? 'Nome da Turma' }}</small>
                            </div>
                            <div class="mb-2">
                                <i class="fas fa-calendar text-muted me-1"></i>
                                <small class="text-muted">{{ $certificate->data_emissao ? $certificate->data_emissao->format('d/m/Y') : 'Não emitido' }}</small>
                            </div>
                            <div class="mb-3">
                                <i class="fas fa-percentage text-muted me-1"></i>
                                <small class="text-muted">{{ $certificate->frequencia_final ? $certificate->frequencia_final . '%' : 'Frequência não definida' }}</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn bg-info text-light btn-sm flex-fill" wire:click="openModal('{{ $certificate->id ?? '' }}')" data-bs-toggle="modal" data-bs-target="#certificateModal">
                                    <i class="fas fa-edit me-1"></i>Editar
                                </button>
                                @if($certificate->data_emissao)
                                <button class="btn btn-outline-info btn-sm" wire:click="downloadCertificate('{{ $certificate->id ?? '' }}')">
                                    <i class="fas fa-download"></i>
                                </button>
                                @endif
                                <button class="btn btn-outline-danger btn-sm" wire:click="revokeCertificate('{{ $certificate->id ?? '' }}')"
                                        onclick="return confirm('Tem certeza que deseja revogar este certificado?')">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-certificate text-muted display-4 mb-3"></i>
                            <div class="text-muted mb-3">Nenhum certificado encontrado</div>
                            <button class="btn bg-info text-light" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#certificateModal">
                                <i class="fas fa-plus me-1"></i>Emitir Primeiro Certificado
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

        code {
            font-size: 0.875em;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
    </style>

    <!-- Modais -->
    @include('church.courses.modals.certificate-modal')
</div>
