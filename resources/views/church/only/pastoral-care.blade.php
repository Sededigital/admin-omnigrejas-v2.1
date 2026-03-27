<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-info">
                            <i class="fas fa-praying-hands me-2"></i>Cuidado Pastoral
                        </h1>
                        <p class="mb-0 text-muted">
                            Registre e acompanhe os atendimentos pastorais da sua igreja
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn bg-info text-light" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#pastoralCareModal">
                            <i class="fas fa-plus me-1"></i>Registrar Atendimento
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
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tipo</label>
                        <select class="form-select" wire:model.live="filtroTipo">
                            <option value="">Todos os tipos</option>
                            @if(isset($tiposAtendimento))
                                @foreach($tiposAtendimento as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Pastor</label>
                        <select class="form-select" wire:model.live="filtroPastor">
                            <option value="">Todos os pastores</option>
                            @if(isset($pastoresDisponiveis))
                                @foreach($pastoresDisponiveis as $pastor)
                                    <option value="{{ $pastor['id'] }}">{{ $pastor['nome'] }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Membro</label>
                        <select class="form-select" wire:model.live="filtroMembro">
                            <option value="">Todos os membros</option>
                            @if(isset($membrosDisponiveis))
                                @foreach($membrosDisponiveis as $membro)
                                    <option value="{{ $membro['id'] }}">{{ $membro['nome'] }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Total</label>
                        <div class="form-control-plaintext pt-2">
                            <strong>{{ count($atendimentos) }}</strong> atendimento(s) encontrado(s)
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Atendimentos Pastorais -->
        <div class="row">
            @if(!empty($atendimentos))
                @foreach($atendimentos as $atendimento)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">
                                        <i class="fas fa-{{ $atendimento->tipo === 'aconselhamento' ? 'comments' : ($atendimento->tipo === 'visita' ? 'home' : ($atendimento->tipo === 'oracao' ? 'pray' : ($atendimento->tipo === 'encorajamento' ? 'heart' : 'question')) ) }} text-info me-2"></i>
                                        {{ ucfirst($atendimento->tipo) }}
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $atendimento->data_atendimento ? $atendimento->data_atendimento->format('d/m/Y') : 'Data não informada' }}
                                    </small>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" wire:click="openModal('{{ $atendimento->id }}')" data-bs-toggle="modal" data-bs-target="#pastoralCareModal">
                                            <i class="fas fa-edit me-2"></i>Editar
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" wire:click="excluirAtendimento('{{ $atendimento->id }}')">
                                            <i class="fas fa-trash me-2"></i>Excluir
                                        </a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong class="text-info">
                                        <i class="fas fa-user me-1"></i>Membro:
                                    </strong>
                                    <p class="mb-2">{{ $atendimento->membro->user->name }}</p>
                                </div>

                                <div class="mb-3">
                                    <strong class="text-info">
                                        <i class="fas fa-user-tie me-1"></i>Pastor:
                                    </strong>
                                    <p class="mb-2">{{ $atendimento->pastor->name }}</p>
                                </div>

                                @if($atendimento->descricao)
                                    <div class="mb-3">
                                        <strong class="text-info">
                                            <i class="fas fa-align-left me-1"></i>Descrição:
                                        </strong>
                                        <p class="mb-2">{{ Str::limit($atendimento->descricao, 100) }}</p>
                                    </div>
                                @endif

                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-info text-light">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $atendimento->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-praying-hands text-muted mb-4" style="font-size: 4rem;"></i>
                            <h4 class="text-muted">Nenhum atendimento pastoral encontrado</h4>
                            <p class="text-muted mb-4">
                                @if($filtroTipo || $filtroPastor || $filtroMembro)
                                    Nenhum atendimento encontrado com os filtros aplicados.
                                @else
                                    Ainda não há atendimentos pastorais registrados na sua igreja.
                                @endif
                            </p>
                            @if($filtroTipo || $filtroPastor || $filtroMembro)
                                <button class="btn btn-outline-secondary me-2" wire:click="$set('filtroTipo', '')" wire:click="$set('filtroPastor', '')" wire:click="$set('filtroMembro', '')">
                                    <i class="fas fa-times me-1"></i>Limpar Filtros
                                </button>
                            @endif
                            <button class="btn bg-info text-light" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#pastoralCareModal">
                                <i class="fas fa-plus me-1"></i>Registrar Primeiro Atendimento
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- PASTORAL CARE MODALS --}}
        @include('church.only.modals.pastoral-care-modal')

    </div>
</div>
