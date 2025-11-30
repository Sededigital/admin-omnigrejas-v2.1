<div>
    <div class="container-fluid p-4">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h3 mb-1 text-primary">
                            <i class="fas fa-hands-helping me-2"></i>Voluntários
                        </h1>
                        <p class="mb-0 text-muted">
                            Gerencie os voluntários da sua igreja
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <button class="btn btn-primary" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#volunteerModal">
                            <i class="fas fa-plus me-1"></i>Adicionar Voluntário
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @push('styles')
        <link rel="stylesheet" href="{{ asset('system/css/community.css') }}">
        @endpush

        {{-- VOLUNTEERS MODALS --}}
        @include('church.members.modals.volunteer-modal')

        {{-- Script para Volunteers --}}
        <script src="{{ asset('system/js/members.js') }}" data-navigate-once></script>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Status</label>
                        <select class="form-select" wire:model.live="filtroAtivo">
                            <option value="todos">Todos os voluntários</option>
                            <option value="ativos">Apenas ativos</option>
                            <option value="inativos">Apenas inativos</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Área de Interesse</label>
                        <input type="text"  autocomplete="new-password"
                               class="form-control"
                               wire:model.live.debounce.300ms="filtroArea"
                               placeholder="Filtrar por área...">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Total</label>
                        <div class="form-control-plaintext pt-2">
                            <strong>{{ count($voluntarios) }}</strong> voluntário(s) encontrado(s)
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Voluntários -->
        <div class="row">
            @if(!empty($voluntarios))
                @foreach($voluntarios as $voluntario)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 {{ $voluntario->ativo ? 'border-success' : 'border-secondary' }}">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">
                                        <i class="fas fa-user text-primary me-2"></i>
                                        {{ $voluntario->membro->user->name }}
                                    </h6>
                                    <small class="text-muted">
                                        <i class="fas fa-user-tag me-1"></i>
                                        {{ ucfirst($voluntario->membro->cargo) }}
                                    </small>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" wire:click="openModal('{{ $voluntario->id }}')" data-bs-toggle="modal" data-bs-target="#volunteerModal">
                                            <i class="fas fa-edit me-2"></i>Editar
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" wire:click="toggleStatus('{{ $voluntario->id }}')">
                                            <i class="fas fa-{{ $voluntario->ativo ? 'ban' : 'check' }} me-2"></i>
                                            {{ $voluntario->ativo ? 'Desativar' : 'Ativar' }}
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" wire:click="excluirVoluntario('{{ $voluntario->id }}')">
                                            <i class="fas fa-trash me-2"></i>Excluir
                                        </a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong class="text-primary">
                                        <i class="fas fa-star me-1"></i>Área de Interesse:
                                    </strong>
                                    <p class="mb-2">{{ $voluntario->area_interesse }}</p>
                                </div>

                                <div class="mb-3">
                                    <strong class="text-primary">
                                        <i class="fas fa-clock me-1"></i>Disponibilidade:
                                    </strong>
                                    <p class="mb-2">{{ $voluntario->disponibilidade }}</p>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge {{ $voluntario->ativo ? 'bg-success' : 'bg-secondary' }}">
                                        <i class="fas fa-{{ $voluntario->ativo ? 'check-circle' : 'times-circle' }} me-1"></i>
                                        {{ $voluntario->ativo ? 'Ativo' : 'Inativo' }}
                                    </span>
                                    <small class="text-muted">
                                        {{ $voluntario->created_at->diffForHumans() }}
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
                            <i class="fas fa-hands-helping text-muted mb-4" style="font-size: 4rem;"></i>
                            <h4 class="text-muted">Nenhum voluntário encontrado</h4>
                            <p class="text-muted mb-4">
                                @if($filtroAtivo !== 'todos' || $filtroArea)
                                    Nenhum voluntário encontrado com os filtros aplicados.
                                @else
                                    Ainda não há voluntários cadastrados na sua igreja.
                                @endif
                            </p>
                            @if($filtroAtivo !== 'todos' || $filtroArea)
                                <button class="btn btn-outline-secondary me-2" wire:click="$set('filtroAtivo', 'todos')" wire:click="$set('filtroArea', '')">
                                    <i class="fas fa-times me-1"></i>Limpar Filtros
                                </button>
                            @endif
                            <button class="btn btn-primary" wire:click="openModal" data-bs-toggle="modal" data-bs-target="#volunteerModal">
                                <i class="fas fa-plus me-1"></i>Adicionar Primeiro Voluntário
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @push('scripts')
        {{-- Script para Scroll Automático --}}
        <script>
        // Função para fazer scroll até o final do container
        function scrollToBottom(containerId) {
            const container = document.getElementById(containerId);
            if (container) {
                setTimeout(() => {
                    container.scrollTop = container.scrollHeight;
                }, 100);
            }
        }
        </script>
        @endpush


    </div>
</div>
