<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-primary">
                            <i class="fas fa-boxes me-2"></i>Recursos da Igreja
                        </h1>
                        <p class="mb-0 text-muted">
                            Gerencie os recursos e equipamentos da sua igreja
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-primary" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#resourceModal">
                            <i class="fas fa-plus me-1"></i>Adicionar Recurso
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @push('styles')
        <link rel="stylesheet" href="{{ asset('system/css/community.css') }}">
        @endpush

        {{-- Script para Alliance/Meetings --}}
        <script src="{{ asset('system/js/alliance.js') }}"></script>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tipo</label>
                        <select class="form-select" wire:model.live="filtroTipo">
                            <option value="">Todos os tipos</option>
                            @if(isset($tiposRecursos))
                                @foreach($tiposRecursos as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Disponibilidade</label>
                        <select class="form-select" wire:model.live="filtroDisponivel">
                            <option value="todos">Todos os recursos</option>
                            <option value="disponiveis">Apenas disponíveis</option>
                            <option value="indisponiveis">Apenas indisponíveis</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Total</label>
                        <div class="form-control-plaintext pt-2">
                            <strong>{{ count($recursos) }}</strong> recurso(s) encontrado(s)
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Recursos -->
        <div class="row">
            @if(!empty($recursos))
                @foreach($recursos as $recurso)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 {{ $recurso->disponivel ? 'border-success' : 'border-warning' }}">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">
                                        <i class="fas fa-{{ $recurso->tipo === 'sala' ? 'door-open' : ($recurso->tipo === 'equipamento' ? 'cogs' : ($recurso->tipo === 'material' ? 'box' : 'question')) }} text-primary me-2"></i>
                                        {{ $recurso->nome }}
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-tag me-1"></i>
                                        {{ ucfirst($recurso->tipo) }}
                                    </small>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" wire:click="openModal('{{ $recurso->id }}')" data-bs-toggle="modal" data-bs-target="#resourceModal">
                                            <i class="fas fa-edit me-2"></i>Editar
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" wire:click="toggleDisponibilidade('{{ $recurso->id }}')">
                                            <i class="fas fa-{{ $recurso->disponivel ? 'ban' : 'check' }} me-2"></i>
                                            {{ $recurso->disponivel ? 'Indisponibilizar' : 'Disponibilizar' }}
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" wire:click="excluirRecurso('{{ $recurso->id }}')">
                                            <i class="fas fa-trash me-2"></i>Excluir
                                        </a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body">
                                @if($recurso->descricao)
                                    <div class="mb-3">
                                        <strong class="text-primary">
                                            <i class="fas fa-align-left me-1"></i>Descrição:
                                        </strong>
                                        <p class="mb-2">{{ $recurso->descricao }}</p>
                                    </div>
                                @endif

                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge {{ $recurso->disponivel ? 'bg-success' : 'bg-warning text-dark' }}">
                                        <i class="fas fa-{{ $recurso->disponivel ? 'check-circle' : 'exclamation-triangle' }} me-1"></i>
                                        {{ $recurso->disponivel ? 'Disponível' : 'Indisponível' }}
                                    </span>
                                    <small class="text-muted">
                                        {{ $recurso->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-boxes text-muted mb-4" style="font-size: 4rem;"></i>
                            <h4 class="text-muted">Nenhum recurso encontrado</h4>
                            <p class="text-muted mb-4">
                                @if($filtroTipo || $filtroDisponivel !== 'todos')
                                    Nenhum recurso encontrado com os filtros aplicados.
                                @else
                                    Ainda não há recursos cadastrados na sua igreja.
                                @endif
                            </p>
                            @if($filtroTipo || $filtroDisponivel !== 'todos')
                                <button class="btn btn-outline-secondary me-2" wire:click="$set('filtroTipo', '')" wire:click="$set('filtroDisponivel', 'todos')">
                                    <i class="fas fa-times me-1"></i>Limpar Filtros
                                </button>
                            @endif
                            <button class="btn btn-primary" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#resourceModal">
                                <i class="fas fa-plus me-1"></i>Adicionar Primeiro Recurso
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- RESOURCES MODALS --}}
        @include('church.settings.modals.resource-modal')

    </div>
</div>
